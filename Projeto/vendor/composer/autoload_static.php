<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit810ec495463b0f80405c59a9ecb3ef2f
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'setasign\\Fpdi\\' => 14,
        ),
        'c' => 
        array (
            'chillerlan\\Settings\\' => 20,
            'chillerlan\\QRCode\\' => 18,
        ),
        'O' => 
        array (
            'OpenBoleto\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'setasign\\Fpdi\\' => 
        array (
            0 => __DIR__ . '/..' . '/setasign/fpdi/src',
        ),
        'chillerlan\\Settings\\' => 
        array (
            0 => __DIR__ . '/..' . '/chillerlan/php-settings-container/src',
        ),
        'chillerlan\\QRCode\\' => 
        array (
            0 => __DIR__ . '/..' . '/chillerlan/php-qrcode/src',
        ),
        'OpenBoleto\\' => 
        array (
            0 => __DIR__ . '/..' . '/openboleto/openboleto/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'FPDF' => __DIR__ . '/..' . '/setasign/fpdf/fpdf.php',
        'PHPGangsta_GoogleAuthenticator' => __DIR__ . '/..' . '/phpgangsta/googleauthenticator/PHPGangsta/GoogleAuthenticator.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit810ec495463b0f80405c59a9ecb3ef2f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit810ec495463b0f80405c59a9ecb3ef2f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit810ec495463b0f80405c59a9ecb3ef2f::$classMap;

        }, null, ClassLoader::class);
    }
}
