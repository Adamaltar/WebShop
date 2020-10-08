<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1f1515feb95658abd40e8728e90de778
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

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1f1515feb95658abd40e8728e90de778::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1f1515feb95658abd40e8728e90de778::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
