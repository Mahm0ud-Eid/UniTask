<?php

/**
 * Class FileExtensionsEnum
 * @package common\enum
 */

namespace common\enum;

class FileExtensionsEnum
{
    public const PYTHON = 'py';
    public const PHP = 'php';
    public const JAVA = 'java';
    public const JAVASCRIPT = 'js';
    public const C_SHARP = 'cs';
    public const C = 'c';
    public const C_PLUS_PLUS = 'cpp';
    public const RUBY = 'rb';
    public const GO = 'go';
    public const SWIFT = 'swift';
    public const TYPESCRIPT = 'ts';
    public const HTML = 'html';
    public const CSS = 'css';

    public const LABEL = [
        self::PYTHON => 'Python',
        self::PHP => 'PHP',
        self::JAVA => 'Java',
        self::JAVASCRIPT => 'JavaScript',
        self::C_SHARP => 'C#',
        self::C => 'C',
        self::C_PLUS_PLUS => 'C++',
        self::RUBY => 'Ruby',
        self::GO => 'Go',
        self::SWIFT => 'Swift',
        self::TYPESCRIPT => 'TypeScript',
        self::HTML => 'HTML',
        self::CSS => 'CSS',
    ];

    public const ICON = [
        self::PYTHON => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg',
        self::PHP => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg',
        self::JAVA => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg',
        self::JAVASCRIPT => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg',
        self::C_SHARP => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/csharp/csharp-original.svg',
        self::C => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/c/c-original.svg',
        self::C_PLUS_PLUS => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/cplusplus/cplusplus-original.svg',
        self::RUBY => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/ruby/ruby-original.svg',
        self::GO => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/go/go-original.svg',
        self::SWIFT => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/swift/swift-original.svg',
        self::TYPESCRIPT => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg',
        self::HTML => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg',
        self::CSS => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
    ];
}
