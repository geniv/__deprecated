<?php

namespace TracyClean;

use Nette\Utils\Finder;


/**
 * Trait TracyClean
 *
 * @author  geniv
 * @package TracyClean
 */
trait TracyClean
{

    /**
     * Internal handler for cleaner
     */
    public function handleInternalTracyClean()
    {
        $dirs = [
            'temp/cache',
            'temp/sessions',
            'admin/temp/cache',
            'admin/temp/sessions',
        ];
        $path = $this->context->parameters['wwwDir'] . '/../';

        $itemCount = 0;
        foreach ($dirs as $dir) {
            if (file_exists($path . $dir)) {
                foreach (Finder::find('*')->from($path . $dir) as $item) {
                    if ($item->isFile() && unlink($item->getPathname())) {
                        $itemCount++;
                    }

                    if ($item->isDir() && @rmdir($item->getPath())) {
                        $itemCount++;
                    }
                }

            }
        }
        $this->flashMessage('smazano ' . $itemCount . ' polozek');
        $this->redirect('this');
    }
}
