<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8328ba4d75ded79d01836c61e948ec0d
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '25072dd6e2470089de65ae7bf11d3109' => __DIR__ . '/..' . '/symfony/polyfill-php72/bootstrap.php',
        '667aeda72477189d0494fecd327c3641' => __DIR__ . '/..' . '/symfony/var-dumper/Resources/functions/dump.php',
        'fe62ba7e10580d903cc46d808b5961a4' => __DIR__ . '/..' . '/tightenco/collect/src/Collect/Support/helpers.php',
        'caf31cc6ec7cf2241cb6f12c226c3846' => __DIR__ . '/..' . '/tightenco/collect/src/Collect/Support/alias.php',
        '0d19fc0eb29a7335217751bbf2db8c03' => __DIR__ . '/../..' . '/core/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Wpm\\' => 4,
            'WpmPack\\Test\\' => 13,
            'WpmPack\\Sort\\' => 13,
            'WpmPack\\Img\\' => 12,
            'WpmPack\\Acf\\' => 12,
        ),
        'T' => 
        array (
            'Tightenco\\Collect\\' => 18,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Php72\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Component\\VarDumper\\' => 28,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Wpm\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core',
        ),
        'WpmPack\\Test\\' => 
        array (
            0 => __DIR__ . '/../..' . '/pack/test',
        ),
        'WpmPack\\Sort\\' => 
        array (
            0 => __DIR__ . '/../..' . '/pack/sort',
        ),
        'WpmPack\\Img\\' => 
        array (
            0 => __DIR__ . '/../..' . '/pack/img',
        ),
        'WpmPack\\Acf\\' => 
        array (
            0 => __DIR__ . '/../..' . '/pack/acf',
        ),
        'Tightenco\\Collect\\' => 
        array (
            0 => __DIR__ . '/..' . '/tightenco/collect/src/Collect',
        ),
        'Symfony\\Polyfill\\Php72\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php72',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Component\\VarDumper\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/var-dumper',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8328ba4d75ded79d01836c61e948ec0d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8328ba4d75ded79d01836c61e948ec0d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
