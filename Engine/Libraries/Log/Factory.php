<?php

namespace Libraries\Log;

use Core\Structure\Components\AbstractLibraryFactory;
use Libraries\Log\Monolog\Factory as MonologFactory;

/**
 * Class Factory
 * @package Libraries\Log
 */
class Factory extends AbstractLibraryFactory
{

    protected function configAutoBuilding()
    {
        $this->factories = [
            'monolog' => MonologFactory::class,
        ];
    }

}