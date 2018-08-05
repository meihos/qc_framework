<?php
/* Test part for check performance of framework */
/** @var \Core\Core $core */

$dbSettings = [
    'engine' => 'mysql',
    'host' => '192.168.1.1',
    'port' => 3306,
    'user' => 'roma',
    'password' => 'Me6Faiki',
    'database' => 'marketing_prod_roma',
    'charset' => 'utf8',
];

$memcacheSettings = ['host' => '127.0.0.1', 'port' => '11211'];
$logSettings = ['path' => __DIR__ . '/log_log.log', 'level' => 'DEBUG', 'handle' => 'db_log'];

/* Get Cache Adapter */
$core->getCacheManager()->addSettings('db_cache', ['type' => 'memcached', 'settings' => $memcacheSettings]);
$cacheInstance = $core->getCacheManager()->getInstance('db_cache');

/* Get Log Adapter */
$core->getLogManager()->addSettings('db_log', ['type' => 'monolog', 'settings' => $logSettings]);
$logInstance = $core->getLogManager()->getInstance('db_log');


/* Get Repository */
$core->getConnectionManager()->addSettings('db', $dbSettings);
$connection = $core->getConnectionManager()->getConnection('db');
$connection->setCacheAdapter($cacheInstance);
$connection->setLogAdapter($logInstance);

/** @var \Applications\Console\Domain\Repository\Nil\NewRepository $repository */
$repository = $connection->buildRepository(\Applications\Console\Domain\Repository\Nil\NewRepository::class, \Libraries\Sql\NilPortugues\Builder::class);
var_dump($repository->getMarketingChannel());
$client = $repository->find(['gaId' => 'GA1.2.1538726925.1503314037'], \Applications\Console\Domain\Model\MarketingClient::class);
var_dump($client);

if ($client instanceof \Applications\Console\Domain\Model\MarketingClient) {
    $client->changeRegistration((new \DateTime())->format('Y-m-d H:i:s'));
    $repository->save($client);
}

/* END */