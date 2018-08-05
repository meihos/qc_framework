<?php

namespace Libraries\Router\AltoRouter;

use Core\Structure\Interfaces\LibraryFactoryInterface;

/**
 * Class Factory
 * @package Libraries\Log\Monolog
 */
class Factory implements LibraryFactoryInterface
{

    /**
     * @param $settings
     * @return Logger|null
     */
    public function buildLibraryInstance($settings)
    {
        $resource = null;
        if (isset($settings['path']) && isset($settings['level']) && isset($settings['handle'])) {
            $levelsMap = MonologLogger::getLevels();
            $levelName = strtoupper($settings['level']);
            $level = (isset($levelsMap[$levelName])) ? $levelsMap[$levelName] : MonologLogger::DEBUG;

            $log = new MonologLogger($settings['handle']);
            $log->pushHandler(new StreamHandler($settings['path'], $level));
            $log->pushProcessor(new PsrLogMessageProcessor());
            $resource = new Logger($log, $level);
        }

        return $resource;
    }


}