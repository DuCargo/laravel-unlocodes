<?php

namespace Dc\Unlocodes\Facades;

/**
 * LaravelUnlocode Facade
 *
 * @category Laravel Unlocode
 * @package  Dc/Unlocodes
 */
class Unlocode extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Dc\Unlocodes\Unlocode::class;
    }
}
