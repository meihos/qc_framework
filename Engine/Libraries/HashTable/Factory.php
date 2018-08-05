<?php

namespace Libraries\HashTable;

use Core\Structure\Components\AbstractLibraryFactory;
use Libraries\HashTable\File\Factory as FileFactory;

/**
 * Class Builder
 * @package Libraries\HashTable
 */
class Factory extends AbstractLibraryFactory
{

    protected function configAutoBuilding()
    {
        $this->factories = [
            'file' => FileFactory::class,
        ];
    }
}