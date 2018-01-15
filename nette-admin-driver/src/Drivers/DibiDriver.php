<?php declare(strict_types=1);

namespace AdminDriver\Drivers;

use Nette\Forms\Container;


/**
 * Class DibiDriver
 *
 * @author  geniv
 * @package AdminDriver\Drivers
 */
class DibiDriver implements IDriver
{
    const NAME = 'dibi';


    /**
     * DibiDriver constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
    }


    /**
     * Get form.
     *
     * @param string    $prefix
     * @param Container $form
     */
    public function getForm(string $prefix, Container $form)
    {
        $form->addText('pk', $prefix . 'pk')
            ->setRequired(true);
        $form->addText('select', $prefix . 'select');
        $form->addText('table', $prefix . 'table')
            ->setRequired(true);
        $form->addText('where', $prefix . 'where');
        $form->addText('groupby', $prefix . 'groupby');
        $form->addText('order', $prefix . 'order');
        $form->addText('limit', $prefix . 'limit');
        $form->addText('fk', $prefix . 'fk');

        $form->addCheckbox('testsql', $prefix . 'testsql');
    }


    /**
     * Factory.
     *
     * @param array $configure
     * @return IDriver
     */
    public function factory(array $configure): IDriver
    {
        // TODO: Implement create() method.

//        $section['driver']['type'] == dibi

        return $this;
    }


    /**
     * Insert.
     *
     * @param $values
     * @return int
     */
    public function insert($values): int
    {
        // TODO: Implement insert() method.
    }


    /**
     * Update.
     *
     * @param int   $id
     * @param array $values
     * @return int
     */
    public function update(int $id, array $values): int
    {
        // TODO: Implement update() method.
    }


    /**
     * Delete.
     *
     * @param int $id
     * @return int
     */
    public function delete(int $id): int
    {
        // TODO: Implement delete() method.
    }
}
