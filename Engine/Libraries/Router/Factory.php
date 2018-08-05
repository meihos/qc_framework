<?php
namespace Libraries\Router;

use Core\Structure\Components\AbstractLibraryFactory;
use Libraries\Log\Monolog\Factory as MonologFactory;

/**
 * Class Factory
 * @package Libraries\Router
 */
class Factory extends AbstractLibraryFactory
{

    protected function configAutoBuilding()
    {
        $this->factories = [
            'alto' => MonologFactory::class,
        ];
    }

}