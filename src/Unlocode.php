<?php

namespace Dc\Unlocodes;

use Dc\Events\UnlocodeSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Notifications\Notifiable;

/**
 * Class Unlocode
 * @package Dc\Unlocodes
 * @mixin \Illuminate\Database\Query\Builder
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

    protected $primaryKey = 'countrycode';
    protected $primaryKey2 = 'placecode';
    public $incrementing = false;

    /**
     * @return string The 5-letter UNLOCODE
     */
    public function getUnlocodeAttribute()
    {
        return $this->countrycode . $this->placecode;
    }

    /**
     * Set the keys for a save update query using countrycode and placecode.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery())
            ->where($this->primaryKey2, '=',
                $this->original[$this->primaryKey2] ?? $this->getAttribute($this->primaryKey2));

        return $query;
    }

    /**
     * The groups that an unlocode belongs to
     */
    public function groups() {
        $countryCode = $this->countrycode;
        $placeCode = $this->placecode;
        // FIXME Resulting query is suboptimal:
        // select "unlocode_groups".*, "unlocode_group_unlocodes"."countrycode" as "pivot_countrycode", "unlocode_group_unlocodes"."groupname" as "pivot_groupname"
        // from "unlocode_groups"
        // inner join "unlocode_group_unlocodes" on "unlocode_groups"."name" = "unlocode_group_unlocodes"."groupname"
        // inner join "unlocode_group_unlocodes" as "ugu" on "ugu"."groupname" = "unlocode_groups"."name" and "ugu"."countrycode" = "XX" and "ugu"."placecode" = "XXX"
        // where "unlocode_group_unlocodes"."countrycode" = "XX"`
        return $this->belongsToMany(UnlocodeGroup::class, 'unlocode_group_unlocodes', 'countrycode', 'groupname')
            ->join('unlocode_group_unlocodes AS ugu', function (JoinClause $join) use ($countryCode, $placeCode) {
                $join
                    ->on('ugu.groupname', '=', 'unlocode_groups.name')
                    ->on('ugu.countrycode', '=', $countryCode)
                    ->on('ugu.placecode', '=', $placeCode);
            });
    }
}
