<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfbea346f60dfda54dc824b1eeae57196
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfbea346f60dfda54dc824b1eeae57196::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfbea346f60dfda54dc824b1eeae57196::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfbea346f60dfda54dc824b1eeae57196::$classMap;

        }, null, ClassLoader::class);
    }
}
