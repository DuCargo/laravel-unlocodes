<?php

namespace Dc\Unlocodes\Tests\Unit;

use Dc\Unlocodes\Unlocode;
use Dc\Unlocodes\Tests\TestCase;
use Dc\Unlocodes\UnlocodeAlias;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnlocodeAliasTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function unlocodes_can_have_aliases() {
        // Given we have an unlocode
        factory(Unlocode::class)->create();
        // When we attach two aliases to it
        factory(UnlocodeAlias::class)->create();
        factory(UnlocodeAlias::class)->create(['alias' => 'Amsterdam']);
        // Then both can be retrieved through the relationship
        $unlocode = Unlocode::find('NLRTM')->with('aliases')->firstOrFail();
        $this->assertEquals(2, $unlocode->aliases->count());
    }

    /** @test */
    function unlocodes_can_attach_aliases() {
        // Given we have an unlocode
        $unlocode = factory(Unlocode::class)->create();
        // When we attach an alias to it
        $unlocode->aliases()->create(['unlocode' => 'NLRTM', 'alias' => 'Amsterdam']);
        // Then it can be retrieved through the relationship
        $unlocode = Unlocode::find('NLRTM')->with('aliases')->firstOrFail();
        $this->assertEquals(1, $unlocode->aliases->count());
    }

    /** @test */
    function alias_belongs_to_unlocode()
    {
        // Given we create an unlocode for NLRTM and attach it to a group
        $unlocode = factory(Unlocode::class)->create();
        factory(UnlocodeAlias::class)->create();
        // When we retrieve the groups Then they exist
        $unlocodeAlias = UnlocodeAlias::where('unlocode', '=', 'NLRTM')->firstOrFail();
        $this->assertEquals($unlocode->unlocode, $unlocodeAlias->original->unlocode);
    }
}
