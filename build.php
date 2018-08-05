<?php
ini_set('display_errors', 1);
ini_set('date.timezone', 'Europe/Moscow');

function customError($level, $message, $file, $line, $context)
{
    $messageException = 'Error on line #' . $line . ' at file [' . $file . '] with message - ' . $message . '[' . $level . ']';
    throw new Exception($messageException);
}

set_error_handler("customError", E_ALL);

$projectDirectory = __DIR__;

$namespaces = [
    'Core' => $projectDirectory . '/Engine/Core',
    'Modules' => $projectDirectory . '/Engine/Modules',
    'Libraries' => $projectDirectory . '/Engine/Libraries',

    'Vendor' => $projectDirectory . '/vendor',
];

require_once $projectDirectory . '/Engine/Core/AutoLoad/init.php';
$core = QC_Init_Core($projectDirectory, $namespaces);
$baseFolder = $projectDirectory . '/config/sources/';

$nl = "\r\n";
$lineDelimiter = "-----------------------------------------------------";
while (true) {
    $exec = true;
    $message = $nl . 'Please enter source for build.' .
        $nl . 'Possible format - "file sources/test" or "folder sources/modules"' .
        $nl . 'For terminate please enter "exit"' . $nl;
    echo getColoredConsoleString($message, 'cyan');

    $line = trim(fgets(STDIN));
    $args = explode(" ", $line);

    if (strtolower($line) == 'exit') {
        break;
    }

    if (count($args) != 2) {
        $message = $nl . "Incorrect input params";
        echo getColoredConsoleString($message, 'red');
        $exec = false;
    }

    if ($exec) {
        buildFile($core, $baseFolder, $args[0], $args[1]);
    }
    echo $nl . $lineDelimiter . $nl;
}

function buildFile(\Core\Core $core, $baseFolder, $type, $source)
{
    switch ($type) {
        case 'file':

            $configFile = $baseFolder . $source . '.php';
            $message = "Try build file [" . $configFile . "] \r\n";
            echo getColoredConsoleString($message, 'green');
            if (!file_exists($configFile)) {
                $message = "File [" . $configFile . "] not found \r\n";
                echo getColoredConsoleString($message, 'red');
                return false;
            }

            $data = (include $configFile);
            if (empty($data) || !is_array($data) || !isset($data['config']) || !isset($data['filename'])) {
                $message = "Empty config file or incorrect structure \r\n";
                echo getColoredConsoleString($message, 'red');
                return false;
            }

            $config = $data['config'];
            $core->getSettingsManager()->saveConfigFile($data['filename'], $config);

            $message = 'Config file was created [' . $data['filename'] . '] ' . date('Y-m-d H:i:s') . "\r\n";
            echo getColoredConsoleString($message, 'green');
            break;
        case 'folder':

            $requireFolder = $baseFolder . $source . '/';
            if (!file_exists($requireFolder)) {
                $message = "Folder [" . $requireFolder . "] not found \r\n";
                echo getColoredConsoleString($message, 'red');
                return false;
            }

            $handle = opendir($requireFolder);
            while (($fileName = readdir($handle)) !== false) {

                if (($fileName != '.') && ($fileName != '..')) {
                    $fullPath = $requireFolder . $fileName;
                    $message = "Try build file [" . $fullPath . "] \r\n";
                    echo getColoredConsoleString($message, 'green');

                    if (!file_exists($fullPath)) {
                        continue;
                    }

                    $data = (include $fullPath);
                    if (empty($data) || !is_array($data) || !isset($data['config']) || !isset($data['filename'])) {
                        $message = "Empty config file or incorrect structure \r\n";
                        echo getColoredConsoleString($message, 'red');
                        continue;
                    }

                    $config = $data['config'];
                    $core->getSettingsManager()->saveConfigFile($data['filename'], $config);

                    $message = 'Config file was created [' . $data['filename'] . '] ' . date('Y-m-d H:i:s') . "\r\n";
                    echo getColoredConsoleString($message, 'green');
                }
            }
            break;
        default:
            $message = "\r\nIncorrect type for source possible types : folder|file";
            echo getColoredConsoleString($message, 'red');
            break;
    }
}


function getColoredConsoleString($string, $foreground_color = null, $background_color = null)
{
    // Set up shell colors
    $foreground_colors = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37'];

    $background_colors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47'
    ];

    $colored_string = "";

    // Check if given foreground color found
    if (isset($foreground_colors[$foreground_color])) {
        $colored_string .= "\033[" . $foreground_colors[$foreground_color] . "m";
    }
    // Check if given background color found
    if (isset($background_colors[$background_color])) {
        $colored_string .= "\033[" . $background_colors[$background_color] . "m";
    }

    // Add string and end coloring
    $colored_string .= $string . "\033[0m";

    return $colored_string;
}