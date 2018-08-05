<?php

namespace Core\Log\Adapter;

/**
 * Interface FactoryInterface
 * @package Core\Log\Adapter
 */
interface FactoryInterface
{

    /**
     * Return LogWriter instance, what implements with LoggerInterface
     * @param $handle string
     * @return AdapterInterface|null
     */
    public function buildLoggerInstance($handle);
} 