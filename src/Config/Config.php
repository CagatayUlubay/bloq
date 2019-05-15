<?php
declare(strict_types=1);

namespace CagatayUlubay\Config;

/**
 * Simple Interface for the Config class
 *
 * Only use this within the DI Container
 *
 * @package CagatayUlubay\Config
 */
final class Config
{
    private const DEFAULT_IDENTIFIER = 'main-application';

    /**
     * @var array $content Config variables stored here
     */
    public static $content = [];

    /**
     * Set Config data
     *
     * @param mixed     $data       The data you want to store
     * @param string    $identifier Config Identifier name
     *
     * @return void
     */
    public static function set($data, string $identifier = self::DEFAULT_IDENTIFIER) : void
    {
        static::$content[$identifier] = $data;
    }

    /**
     * Get Config data by Config identifier name
     *
     * Notice: It's not possible to receive all data from an identifier.
     * Use the DIC to initialize everything needed and pass only those, NEVER the full config data
     *
     * @param string $name       Data key name
     * @param string $identifier Config Identifier name
     *
     * @return mixed
     *
     * @throws MissingConfigException
     */
    public static function get(string $name, string $identifier = self::DEFAULT_IDENTIFIER)
    {
        if (!isset(self::$content[$identifier][$name])) {
            throw new MissingConfigException(sprintf('Config identifier "%s" is missing', $identifier));
        }

        return static::$content[$identifier][$name];
    }

    /**
     * Load config into the $content array
     *
     * @param string    $path       Path to the config file
     * @param string    $identifier Config Identifier Name
     * @param bool      $override   Determine if config identifier should be overwritten, if it already exists |default: false
     *
     * @throws MissingFileException
     */
    public static function loadConfig(string $path, string $identifier = self::DEFAULT_IDENTIFIER, bool $override = false) : void
    {
        if (!\file_exists($path)) {
            throw new MissingFileException("File does not exist on path: " . $path);
        }

        // Early return if config name identifier exists and $override is false
        if (isset(static::$content[$identifier]) && $override === false) {
            return;
        }

        static::set(include($path), $identifier);
    }
}