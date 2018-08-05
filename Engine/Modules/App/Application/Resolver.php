<?php
namespace Modules\App\Application;

use Core\Common\Request;
use Core\Settings\Configurator;
use Modules\App\Domain\Factory\Application as ApplicationFactory;
use Modules\App\Domain\Model\Application as ApplicationModel;

/**
 * Class Resolver
 * @package Modules\App\Application
 */
class Resolver
{
    /**
     * @var array
     */
    private $applicationList;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var ApplicationFactory
     */
    private $applicationFactory;
    /**
     * @var Configurator
     */
    private $settingsManager;

    private $currentApplication;
    private $isSettingsLoaded;

    public function __construct(Configurator $settingsManager, Request $request, ApplicationFactory $applicationFactory, array $applicationList)
    {
        $this->applicationList = $applicationList;
        $this->request = $request;
        $this->applicationFactory = $applicationFactory;
        $this->settingsManager = $settingsManager;

        $this->currentApplication = null;
        $this->isSettingsLoaded = false;
    }

    public function currentApplication()
    {
        $this->loadSettings();
        if (is_null($this->currentApplication)) {
            $this->currentApplication = $this->getCurrentApplication();
        }

        return $this->currentApplication;
    }

    protected function loadSettings()
    {
        if ($this->isSettingsLoaded) {
            return true;
        }

        $isSomeLoaded = false;
        foreach ($this->applicationList as $applicationSettings) {
            if (!isset($applicationSettings['path']) || !isset($applicationSettings['name'])) {
                continue;
            }

            $appSettingsName = $this->buildSettingsName($applicationSettings['name']);
            $this->settingsManager->readSettingsFile($applicationSettings['path'], $appSettingsName, true);
            $isSomeLoaded = true;
        }

        return $isSomeLoaded;
    }

    private function buildSettingsName($name)
    {
        return 'app_' . $name;
    }

    protected function getCurrentApplication()
    {
        $application = null;
        $currentSchema = ($this->request->isHttps()) ? 'https' : 'http';
        $currentHost = $this->request->getRequestInfo('HTTP_HOST');
        $currentUri = $this->request->getRequestInfo('REQUEST_URI');
        $currentPort = $this->request->getRequestInfo('SERVER_PORT');
        $currentMethod = $this->request->getRequestInfo('REQUEST_METHOD');

        if ($this->request->isConsole()) {
            $currentUri = implode(' ', $this->request->getRequestInfo('argv'));
            $currentHost = 'console';
            $currentSchema = 'cgi';
        }

        $application = $this->applicationFactory->createForInit($currentSchema, $currentHost, $currentUri, $currentPort, $currentMethod);

        foreach ($this->applicationList as $applicationSettings) {
            if (!isset($applicationSettings['path']) || !isset($applicationSettings['name'])) {
                continue;
            }

            $appSettingsName = $this->buildSettingsName($applicationSettings['name']);
            $settings = $this->settingsManager->getSettings($appSettingsName, []);

            if ($this->matchSettings($settings, $application)) {
                $application->setNamespace($settings['application']['namespace']);
                $application->setSystem($settings['application']['system']);
                $application->setPath($settings['application']['path']);
                $application->setConfig($settings);
                break;
            }
        }

        return $application;
    }

    private function matchSettings($fullSettings, ApplicationModel $application)
    {
        if (!isset($fullSettings['application']) || !is_array($fullSettings['application'])) {
            return false;
        }

        $settings = $fullSettings['application'];

        if (($application->getSchema() == $settings['schema']) && ($application->getHost() == $settings['host'])) {
            if (!empty($settings['basePath'])) {

                if (strpos($application->getUri(), $settings['basePath'], 0) !== 0) {
                    return false;
                }

                if (substr($application->getUri(), strlen($settings['basePath']), 1) !== '/') {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
} 