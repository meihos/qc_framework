<?php

namespace Modules\App;

use Core\Core;
use Core\Structure\Components\AbstractModule;
use Modules\App\Application\Resolver;

/**
 * Class Module
 * @package Modules\App
 */
class Module extends AbstractModule
{
    /** @var Resolver */
    protected $resolver;
    /** @var string */
    protected $configPath;

    /**
     * Module constructor.
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        parent::__construct($core);
        $this->configPath = PATH_TO_SYSTEM . '/config/build/modules/app.config';
    }

    public function buildModule()
    {
        $application = $this->resolver->currentApplication();
        if (is_null($application->getSystem())) {
            $this->core->getErrors()->error('Not found correct system');
            return false;
        };

        $this->core->getClassLoader()->addPrefix($application->getNamespace(), $application->getPath());
        $this->core->eventManager->trigger('module.app.findApplication', ['application' => $application]);
    }

    /**
     * @return Resolver
     */
    public function getResolver()
    {
        return $this->resolver;
    }
} 