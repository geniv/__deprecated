<?php

/**
 * @author geniv
 */

use Dibi\Connection;
use Nette\Application\Responses\RedirectResponse;
use Nette\Forms\Form;


$context = require_once(__DIR__ . '/app/bootstrap.php');
$connection = $context->getByType(Connection::class);
$database = $connection->getDatabaseInfo();

$tables = array_map(function ($item) {
    return $item->name;
}, $database->getTables());

// load value first table for prefix value
$prefix = explode('_', $tables[0], 2)[0];

$form = new Form;
$form->addText('database', 'Database name')->setDisabled();
$form->addText('prefix', 'Prefix');
$form->addCheckboxList('tablesList', 'Tables', $tables);
$form->addSubmit('selDel', 'Delete selected tables');
$form->addSubmit('selTrunc', 'Truncate selected tables');
$form->addSubmit('rename', 'Rename all tables');

$form->setDefaults([
    'database' => $database->getName(),
    'prefix'   => $prefix,
]);

// if form send success
if ($form->isSuccess()) {
    $values = $form->getValues(true);

    // drop selected tables
    if ($form['selDel']->isSubmittedBy()) {
        foreach ($values['tablesList'] as $table) {
            $connection->query('DROP TABLE [' . $tables[$table] . ']');
        }
    }


    // truncate selected tables
    if ($form['selTrunc']->isSubmittedBy()) {
        $connection->query('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($values['tablesList'] as $table) {
            $connection->query('TRUNCATE [' . $tables[$table] . ']');
        }
        $connection->query('SET FOREIGN_KEY_CHECKS = 1;');
    }


    // rename all tables
    if ($form['rename']->isSubmittedBy()) {
        foreach ($tables as $table) {
            $name = explode('_', $table);
            $name[0] = $values['prefix'];
            $connection->query('RENAME TABLE [' . $table . '] TO [' . implode('_', $name) . '];');
        }

//        $file = __DIR__ . '/../config/config-tables-specification.neon';
//        $conf = Neon::decode(file_get_contents($file));
//        $conf['parameters']['tb_prefix'] = $values['prefix'] . '_';
//        file_put_contents($file, Neon::encode($conf, Neon::BLOCK));
    }

    (new RedirectResponse('install.php'))->send(new \Nette\Http\Request(new \Nette\Http\UrlScript('')), new \Nette\Http\Response());
}

echo $form;
