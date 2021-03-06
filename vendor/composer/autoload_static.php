<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit444dda6d78709cdd9ca2fe3e35391ca4
{
    public static $files = array (
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Spatie\\Macroable\\' => 17,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
        'F' => 
        array (
            'Faker\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Spatie\\Macroable\\' => 
        array (
            0 => __DIR__ . '/..' . '/spatie/macroable/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fakerphp/faker/src/Faker',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit444dda6d78709cdd9ca2fe3e35391ca4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit444dda6d78709cdd9ca2fe3e35391ca4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit444dda6d78709cdd9ca2fe3e35391ca4::$classMap;

        }, null, ClassLoader::class);
    }
}
