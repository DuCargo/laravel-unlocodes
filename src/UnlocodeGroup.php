<?php

namespace Dc\Unlocodes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

/**
 * Class UnlocodeGroup
 * @package Dc\Unlocodes
 */
class UnlocodeGroup extends Model
{
    public $timestamps = true;
    protected $guarded = [];

    protected $primaryKey = 'name';
    public $incrementing = false;

    /**
     * Define a one-to-many relationship with a custom foreign key.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasMany(
            $instance->newQuery(), $this, 'ugu.groupname', $localKey
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unlocodes() {
        $groupname = $this->name;
        return $this->hasMany(Unlocode::class)
            ->join('unlocode_group_unlocodes AS ugu', function (JoinClause $join) use ($groupname) {
                $join->on('ugu.countrycode', '=', 'unlocodes.countrycode')
                    ->on('ugu.placecode', '=', 'unlocodes.placecode')
                    ->on('ugu.groupname', '=', $groupname);
            });

    }
}
