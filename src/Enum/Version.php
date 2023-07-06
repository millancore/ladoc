<?php

declare(strict_types=1);

namespace Ladoc\Enum;

use InvalidArgumentException;

enum Version: string
{
    case V10 = '10.x';
    case V9 = '9.x';
    case V8 = '8.x';
    case V7 = '7.x';
    case V6 = '6.x';
    case V5_8 = '5.8';
    case V5_7 = '5.7';
    case V5_6 = '5.6';
    case V5_5 = '5.5';
    case V5_4 = '5.4';
    case V5_3 = '5.3';
    case V5_2 = '5.2';
    case V5_1 = '5.1';
    case V5_0 = '5.0';
    case V4_2 = '4.2';
    case V4_1 = '4.1';
    case V4_0 = '4.0';

    public static function getLatestVersion(): Version
    {
        return self::V10;
    }

    /**
     * @throws \Exception
     */
    public static function fromValue(int|float|string $version): Version
    {
        if(is_numeric($version) && $version >= 6) {
            $version = $version . '.x';
        }

        foreach (self::cases() as $case) {
            if($case->value === (string) $version) {
                return $case;
            }
        }

        throw new InvalidArgumentException(sprintf('Version %s not found', $version));
    }

}
