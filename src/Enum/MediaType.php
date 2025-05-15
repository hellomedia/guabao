<?php

namespace App\Enum;

use App\Enum\Trait\EnumUtilsTrait;

enum MediaType: string
{
    use EnumUtilsTrait;
    
    case IMAGE = 'image';
    case VIDEO = 'video';
}