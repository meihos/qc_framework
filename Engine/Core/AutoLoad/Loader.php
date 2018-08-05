<?php
namespace Core\AutoLoad;

/**
 * Class Loader
 * @package Core\AutoLoad
 */
class Loader
{
    private $prefixes = array();
    private $fallbackDirs = array();
    private $useIncludePath = false;

    /**
     * Returns prefixes.
     *
     * @return array
     */
    public function getPrefixes()
    {
        return $this->prefixes;
    }

    /**
     * Returns fallback directories.
     *
     * @return array
     */
    public function getFallbackDirs()
    {
        return $this->fallbackDirs;
    }

    /**
     * Adds prefixes.
     *
     * @param array $prefixes Prefixes to add
     */
    public function addPrefixes(array $prefixes)
    {
        foreach ($prefixes as $prefix => $path) {
            $this->addPrefix($prefix, $path);
        }
    }

    /**
     * Registers a set of classes.
     *
     * @param string $prefix The classes prefix
     * @param array|string $paths The location(s) of the classes
     */
    public function addPrefix($prefix, $paths)
    {
        if (!$prefix) {
            foreach ((array)$paths as $path) {
                $this->fallbackDirs[] = $path;
            }

            return;
        }
        if (isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = array_merge(
                $this->prefixes[$prefix],
                (array)$paths
            );
        } else {
            $this->prefixes[$prefix] = (array)$paths;
        }
    }

    /**
     * Can be used to check if the autoloader uses the include path to check
     * for classes.
     *
     * @return bool
     */
    public function getUseIncludePath()
    {
        return $this->useIncludePath;
    }

    /**
     * Turns on searching the include for class files.
     *
     * @param bool $useIncludePath
     */
    public function setUseIncludePath($useIncludePath)
    {
        $this->useIncludePath = (bool)$useIncludePath;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    /**
     * Unregisters this instance as an autoloader.
     */
    public function unRegister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $class The name of the class
     *
     * @return bool|null True, if loaded
     */
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            require $file;

            return true;
        }
    }

    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|null The path, if found
     * @throws \Exception
     */
    public function findFile($class)
    {
        $hasNamespace = false;

        if (false !== $pos = strrpos($class, '\\')) {
            // namespaced class name
            $hasNamespace = true;
            $classPath = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 0, $pos)) . DIRECTORY_SEPARATOR;
            $className = substr($class, $pos + 1);
        } else {
            // PEAR-like class name
            $classPath = null;
            $className = $class;
        }

        $classPath .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        foreach ($this->prefixes as $prefix => $dirs) {
            if ($class === strstr($class, $prefix)) {

                $newClassPath = substr($classPath, strlen($prefix) + 1);
                foreach ($dirs as $dir) {
                    if (file_exists($dir . DIRECTORY_SEPARATOR . $newClassPath)) {
                        return $dir . DIRECTORY_SEPARATOR . $newClassPath;
                    }
                }
            }
        }

        foreach ($this->fallbackDirs as $dir) {

            if (file_exists($dir . DIRECTORY_SEPARATOR . $classPath)) {
                return $dir . DIRECTORY_SEPARATOR . $classPath;
            }
        }

        if ($this->useIncludePath && $file = stream_resolve_include_path($classPath)) {
            return $file;
        }

        if (!class_exists($class) && ($hasNamespace)) {
            throw new \Exception('File for class [' . $class . '] not found');
        }
    }
}