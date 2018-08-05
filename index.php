<?php
ini_set('display_errors', 1);
ini_set('date.timezone', 'Europe/Moscow');


$projectDirectory = __DIR__;

$namespaces = [
    'Core' => $projectDirectory . '/Engine/Core',
    'Modules' => $projectDirectory . '/Engine/Modules',
    'Libraries' => $projectDirectory . '/Engine/Libraries',
    'Vendor' => $projectDirectory . '/vendor',
];

$libraryStackConfig = $projectDirectory . '/config/build/library_stack';
$moduleStackConfig = $projectDirectory . '/config/build/module_stack';

require_once $projectDirectory . '/Engine/Core/AutoLoad/init.php';
$core = QC_Init_Core($projectDirectory, $namespaces);
$core->initComponents($libraryStackConfig, $moduleStackConfig);

function showApp(\Core\Events\Event $e)
{
    $application = $e->getParameter('application', null);
    if ($application instanceof \Modules\App\Domain\Model\Application) {
        echo $application->getNamespace() . "\r\n";
        echo $application->getPath() . "\r\n";
    } else {
        echo 'LOLOLO';
    }
}

$guardSettings = ['path' => PATH_TO_SYSTEM . '/data/hashTable/guard.tmp'];
$guardHashTableAdapter = $core->libraries()->hashTable->buildByType('file', $guardSettings);
$core->getGuard()->setAdapter($guardHashTableAdapter);

$core->eventManager->attach('module.app.findApplication', 'showApp');
$core->modules()->loadModules();

$mainLogSettings = ['path' => __DIR__ . '/error.log', 'level' => 'DEBUG', 'handle' => 'db_log'];
$logAdapter = $core->libraries()->log->buildByType('monolog', $mainLogSettings);
$core->getErrors()->setLogger($logAdapter);


include 'test_part.php';









