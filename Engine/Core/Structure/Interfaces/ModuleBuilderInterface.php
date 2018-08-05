<?php
namespace Core\Structure\Interfaces;

use Core\Core;

/**
 * Interface ModuleBuilderInterface
 * @package Core\Structure\Interfaces
 */
interface ModuleBuilderInterface
{
    public function __construct(Core $core);

    public function buildModuleComponent(array $moduleSettings);
} 