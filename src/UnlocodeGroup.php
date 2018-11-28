<?php

namespace Dc\Unlocodes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

/**
 * Class UnlocodeGroup
 *
 * @package Dc\Unlocodes
 */
class UnlocodeGroup extends Model
{
    public $timestamps = true;
    protected $guarded = [];

    protected $primaryKey = 'name';
    public $incrementing = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function unlocodes()
    {
        return $this->belongsToMany(Unlocode::class, 'unlocode_group_unlocodes', 'groupname', 'unlocode');
    }
}
