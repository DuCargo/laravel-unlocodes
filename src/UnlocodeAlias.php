<?php

namespace Dc\Unlocodes;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UnlocodeAlias
 *
 * @package Dc\Unlocodes
 */
class UnlocodeAlias extends Model
{
    public $timestamps = true;
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function original()
    {
        return $this->belongsTo(Unlocode::class, 'unlocode');
    }
}
