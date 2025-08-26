<?php

namespace App\Enum;

use App\Enum\Trait\EnumUtilsTrait;

enum VideoOrientation: string
{
    use EnumUtilsTrait;
    
    case PORTRAIT = 'portrait';
    case LANDSCAPE = 'landscape';
}