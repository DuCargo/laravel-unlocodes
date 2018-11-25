<?php

namespace Dc\Unlocodes\Tests\Unit;

use Dc\Unlocodes\Unlocode;
use Dc\Unlocodes\Tests\TestCase;
use Dc\Unlocodes\UnlocodeGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnlocodeGroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function unlocodes_can_be_grouped() {
        // Given we have our default NLRTM unlocode already seeded and we create a model for it
        factory(Unlocode::class)->create();
        // When we look up the Europe group
        $europeCodes = UnlocodeGroup::findOrFail('Europe');
        // The NLRTM is contained withing
        $this->assertEquals('NL', $europeCodes->unlocodes->first()->countrycode);
        $this->assertEquals('RTM', $europeCodes->unlocodes->first()->placecode);
    }
}
