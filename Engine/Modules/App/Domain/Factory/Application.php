<?php
namespace Modules\App\Domain\Factory;

use Modules\App\Domain\Model\Application as ApplicationModel;

/**
 * Class Application
 * @package Modules\App\Domain\Factory
 */
class Application
{

    /**
     * @param $schema
     * @param $host
     * @param $uri
     * @param $port
     * @param $method
     * @return ApplicationModel
     */
    public function createForInit($schema, $host, $uri, $port, $method)
    {
        $application = new ApplicationModel();

        $application->setSchema($schema);
        $application->setHost($host);
        $application->setUri($uri);
        $application->setPort($port);
        $application->setMethod($method);


        return $application;
    }
} 