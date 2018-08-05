<?php
namespace Core\Structure\Interfaces;

/**
 * Interface LibraryFactoryInterface
 * @package Core\Structure\Interfaces
 */
interface LibraryFactoryInterface
{
    /**
     * @param $settings
     * @return mixed
     */
    public function buildLibraryInstance($settings);
}