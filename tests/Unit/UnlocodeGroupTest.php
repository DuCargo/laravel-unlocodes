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
        // Given we have an unlocode group
        $europeGroup = factory(UnlocodeGroup::class)->create();
        // When we attach two unlocodes to it
        $europeGroup->unlocodes()->attach(factory(Unlocode::class)->create());
        $europeGroup->unlocodes()->attach(factory(Unlocode::class)->create(['countrycode' => 'QQ', 'placecode' => 'QQQ']));
        // Then both can be retrieved through it
        $europeGroup = UnlocodeGroup::find('Europe')->with('unlocodes')->firstOrFail();
        $this->assertEquals(2, $europeGroup->unlocodes->count());
    }
}
