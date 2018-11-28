<?php

namespace Dc\Unlocodes;

use Dc\Events\UnlocodeSaved;
use Dc\Unlocodes\Helpers\UnlocodeHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class Unlocode
 *
 * @package Dc\Unlocodes
 * @mixin   \Illuminate\Database\Query\Builder
 */
class Unlocode extends Model
{
    use Notifiable;

    const VALIDATION_RULES = [
        'countrycode' => 'required|regex:/^[A-Z]{2}$/',
        'placecode' => 'required|regex:/^[A-Z2-9]{3}$/',
        'name' => 'required|string|max:100',
        'subdivision' => 'nullable|string|max:3',
        'longitude' => 'nullable|numeric',
        'latitude' => 'nullable|numeric',
    ];
    const UNLOCODE_REGEX = '/^[A-Z]{2}[A-Z2-9]{3}$/';

    public $timestamps = true;

    protected $guarded = ['status', 'IATA'];

    protected $primaryKey = 'unlocode';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        self::creating([Unlocode::class, 'refreshUnlocodeFor']);
        self::updating([Unlocode::class, 'refreshUnlocodeFor']);
        self::updated([Unlocode::class, 'clearCacheFor']);
        self::deleted([Unlocode::class, 'clearCacheFor']);
    }

    static function refreshUnlocodeFor($model) {
        $model->unlocode = $model->countrycode . $model->placecode;
    }

    static function clearCacheFor($model) {
        /* @noinspection PhpUnhandledExceptionInspection Never occurs with our args */
        cache()->forget(UnlocodeHelper::cacheKey($model));
    }

    /**
     * The groups that an unlocode belongs to
     */
    public function groups()
    {
        return $this->belongsToMany(UnlocodeGroup::class, 'unlocode_group_unlocodes', 'unlocode', 'groupname');
    }
}
