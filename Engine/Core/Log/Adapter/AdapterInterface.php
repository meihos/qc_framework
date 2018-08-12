<?php

namespace Core\Log\Adapter;

use Psr\Log\LoggerInterface;

/**
 * Interface AdapterInterface
 * @package Core\Log\Adapter
 */
interface AdapterInterface extends LoggerInterface
{
    /**
     * Return default level for logger instance
     *
     * @return string
     */
    public function defaultLevel();
} 