<?php

namespace Dc\Unlocodes\Helpers;

use Dc\Unlocodes\Unlocode;

/**
 * Class UnlocodeHelper
 * @category   Laravel Unlocodes
 * @package    Dc\Unlocodes\Helpers
 * @copyright  Copyright (c) 2017-2018 DuCargo (https://ducargo.com)
 * @author     DuCargo <info@ducargo.com>
 * @license    The MIT License (MIT)
 */
class UnlocodeHelper
{
    /**
     * Spread an unlocode out into two variables
     * @param string $unlocode E.g. NLRTM
     * @return array list($countrycode, $placecode)
     */
    public static function spread_unlocode(string $unlocode): array {
        if( !preg_match(Unlocode::UNLOCODE_REGEX, $unlocode) ) {
            throw new \InvalidArgumentException('"'.e($unlocode).'" is not a valid UN/LOCODE');
        }
        return [ substr( $unlocode, 0, 2 ), substr( $unlocode, 2, 5 ) ];
    }

    /**
     * @param string $countrycode
     * @param string $placecode
     * @return string The cache key for the given unlocode
     */
    static function cacheKey(string $countrycode, string $placecode) {
        return "unlocode_{$countrycode}{$placecode}";
    }
}
