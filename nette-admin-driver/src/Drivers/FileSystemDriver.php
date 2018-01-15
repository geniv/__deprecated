<?php declare(strict_types=1);

namespace AdminDriver\Drivers;

use Nette\Forms\Container;


/**
 * Class FileSystemDriver
 *
 * @author  geniv
 * @package AdminDriver\Drivers
 */
class FileSystemDriver
{
    const NAME = 'filesystem';


    /**
     * FileSystemDriver constructor.
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
        $form->addText('path', $prefix . 'path');
    }
}
