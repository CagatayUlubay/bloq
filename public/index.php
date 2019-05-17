<?php
declare(strict_types=1);

use CagatayUlubay\Config\Config;
use CagatayUlubay\Config\MissingFileException;
use CagatayUlubay\Config\MissingConfigException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \Psr\Log\NullLogger;

// Default Vendor autoload
require_once "../vendor/autoload.php";

$ds = DIRECTORY_SEPARATOR;
$rootPath = __DIR__ . $ds . '..' . $ds;

// Set Logger with default StreamHandler
try {
    $logPath = $rootPath . 'logs' . $ds . 'application.log';
    $log = new Logger('main-application');
    $streamHandler = new StreamHandler($logPath);
    $log->pushHandler($streamHandler);
} catch (\Exception $e) {
    $log = new NullLogger();
}

// Load Config
try {
    $configPath = $rootPath . 'config' . $ds . 'global.php';
    Config::loadConfig($configPath, "main-application");
} catch (MissingFileException $e) {
    $log->critical('Main Application Configuration failed! Message: ' . $e->getMessage());
}

// Set Environment variables
try {
    $envPath = Config::get('paths');
    $dotenv = Dotenv\Dotenv::create($envPath['root']);
    $dotenv->load();

    $dotenv->required('environment')->allowedValues(['production', 'development']);

} catch (MissingConfigException $e) {
    $log->critical('Application failed to load environment configuration. Message: ' . $e->getMessage());
}

$environment = getenv('environment');

// @Todo: Add Exception/Error/Throwable logging