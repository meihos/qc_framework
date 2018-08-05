<?php

namespace Modules\App\Init;

use Core\Core;
use Core\Structure\Interfaces\ModuleBuilderInterface;
use Modules\App\Application\Resolver;
use Modules\App\Domain\Factory\Application as ApplicationFactory;

/**
 * Class ResolverBuilder
 * @package Modules\App\Init
 */
class ResolverBuilder implements ModuleBuilderInterface
{
    /** @var Core */
    protected $core;

    /**
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->core = $core;
    }

    /**
     * @param array $moduleSettings
     * @return Resolver
     */
    public function buildModuleComponent(array $moduleSettings)
    {
        $settingsManager = $this->core->getSettingsManager();
        $request = $this->core->request;
        $applicationFactory = new ApplicationFactory();
        $applicationList = (isset($moduleSettings['applications'])) ? $moduleSettings['applications'] : [];

        $resolver = new Resolver($settingsManager, $request, $applicationFactory, $applicationList);

        return $resolver;
    }
} 