<?php

/**
 * Unlocode Helper
 *
 * @category  Laravel-Unlocodes
 * @package   Dc\Unlocodes\Helpers
 * @author    DuCargo <info@ducargo.com>
 * @copyright 2017-2018 DuCargo
 * @license   The MIT License (MIT)
 * @link      https://ducargo.com
 */

namespace Dc\Unlocodes\Helpers;

use Dc\Unlocodes\Unlocode;

/**
 * Class UnlocodeHelper
 */
class UnlocodeHelper
{
    /**
     * Spread an unlocode out into two variables
     *
     * @param  string $unlocode E.g. NLRTM
     * @return array list($countrycode, $placecode)
     */
    public static function spreadUnlocode(string $unlocode): array
    {
        if (!preg_match(Unlocode::UNLOCODE_REGEX, $unlocode)) {
            throw new \InvalidArgumentException('"' . e($unlocode) . '" is not a valid UN/LOCODE');
        }
        return [substr($unlocode, 0, 2), substr($unlocode, 2, 5)];
    }

    /**
     * @param  string $unlocodeOrCountrycode
     * @param  string|null $placecode If first is countrycode
     * @return string The cache key for the given unlocode
     */
    public static function cacheKey($unlocodeOrCountrycode, string $placecode = null)
    {
        $prefix = "unlocode_";
        if ($unlocodeOrCountrycode instanceof Unlocode) {
            return "{$prefix}{$unlocodeOrCountrycode->unlocode}";
        }
        return "{$prefix}{$unlocodeOrCountrycode}" . $placecode ?? '';
    }
}
