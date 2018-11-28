<?php

namespace Dc\Unlocodes\Tests\Unit;

use Dc\Unlocodes\Helpers\UnlocodeHelper;
use Dc\Unlocodes\Unlocode;
use Dc\Unlocodes\Tests\TestCase;
use Dc\Unlocodes\UnlocodeGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnlocodeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function unlocodes_can_be_stored_and_retrieved() {
        Unlocode::create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
        ]);
        $this->assertNotEmpty(Unlocode::where('countrycode', 'QQ'));
    }

    /** @test */
    function converting_to_an_array()
    {
        // Given we have an unlocode
        /* @var $unlocode Unlocode */
        $unlocode = factory(Unlocode::class)->create([
            'countrycode' => 'NL',
            'placecode' => 'RTM',
            'name' => 'Rotterdam',
        ]);

        // When we convert the unlocode to an array
        $result = $unlocode->toArray();

        // Then it should contain these fields
        $this->assertArraySubset(array (
            'countrycode' => 'NL',
            'placecode' => 'RTM',
            'name' => 'Rotterdam',
            'longitude' => NULL,
            'latitude' => NULL,
            'subdivision' => '',
            'status' => '',
            'date' => '',
            'IATA' => '',
        ), $result);
    }

    /** @test */
    function unlocode_belongs_to_groups()
    {
        // Given we create an unlocode for NLRTM and attach it to a group
        $unlocode = factory(Unlocode::class)->create();
        // When we retrieve the groups
        $groups = $unlocode->groups;
        // They exist
        $this->assertNotEmpty($groups);
    }

    /** @test */
    function unlocode_has_unlocode()
    {
        $unlocode = factory(Unlocode::class)->create();
        $this->assertEquals('NLRTM', $unlocode->unlocode);
    }

    /** @test */
    function unlocode_updated_on_update()
    {
        $unlocode = factory(Unlocode::class)->create();
        $unlocode->update(['countrycode' => 'QQ', 'placecode' => 'QQQ']);
        $this->assertEquals('QQQQQ', $unlocode->unlocode);
    }

    /** @test */
    function cache_cleared_on_delete()
    {
        $unlocode = factory(Unlocode::class)->create();
        \Cache::shouldReceive('forget')->with(UnlocodeHelper::cacheKey($unlocode))->once();
        $unlocode->delete();
    }

    /** @test */
    function cache_cleared_on_update()
    {
        $unlocode = factory(Unlocode::class)->create();
        \Cache::shouldReceive('forget')->with(UnlocodeHelper::cacheKey($unlocode))->once();
        $unlocode->update(['name' => 'ABC']);
    }
}
