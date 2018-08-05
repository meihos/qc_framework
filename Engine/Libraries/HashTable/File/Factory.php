<?php

namespace Libraries\HashTable\File;

use Core\Structure\Interfaces\LibraryFactoryInterface;

/**
 * Class Factory
 * @package Libraries\HashTable\File
 */
class Factory implements LibraryFactoryInterface
{

    /**
     * @param $settings
     * @return Adapter|null
     */
    public function buildLibraryInstance($settings)
    {
        $resource = null;
        if (isset($settings['path']) && file_exists($settings['path'])) {
            $resource = new Adapter($settings['path']);
        }

        return $resource;
    }
}