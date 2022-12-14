<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8820d3fde7ef089fd9bf0b0b6f31e331
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Spatie\\Color\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Spatie\\Color\\' => 
        array (
            0 => __DIR__ . '/..' . '/spatie/color/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8820d3fde7ef089fd9bf0b0b6f31e331::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8820d3fde7ef089fd9bf0b0b6f31e331::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit8820d3fde7ef089fd9bf0b0b6f31e331::$classMap;

        }, null, ClassLoader::class);
    }
}
