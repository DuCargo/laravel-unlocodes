<?php

namespace Dc\Unlocodes\Tests\Unit;

use Dc\Unlocodes\Unlocode;
use Dc\Unlocodes\Tests\TestCase;
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
        // Given groups have been seeded and we create an unlocode for NLRTM
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
}
