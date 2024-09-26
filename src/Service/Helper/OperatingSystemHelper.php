<?php

declare(strict_types=1);

namespace App\Service\Helper;

use App\UniqueNameInterface\ServerInterface;

class OperatingSystemHelper
{
    public static function isUnix(): bool {

        $os = strtolower(php_uname('s'));
        if (str_contains($os, strtolower(ServerInterface::OS_WINDOWS))) {

            return false;
        } else {

            return true;
        }
    }

    public static function isWindows(): bool {

        $os = strtolower(php_uname('s'));
        if (str_contains($os, strtolower(ServerInterface::OS_WINDOWS))) {

            return true;
        } else {

            return false;
        }
    }
}
