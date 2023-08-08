<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitc91f9d8a2f27eb3e0d3972d31ca8b8bc
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitc91f9d8a2f27eb3e0d3972d31ca8b8bc', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitc91f9d8a2f27eb3e0d3972d31ca8b8bc', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitc91f9d8a2f27eb3e0d3972d31ca8b8bc::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}