<?php

namespace Dc\Unlocodes\Tests\Unit;

use Dc\Unlocodes\Facades\Unlocode;
use Dc\Unlocodes\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnlocodeFacadeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function unlocodes_facade_can_be_used_to_store_and_retrieve_unlocodes() {
        Unlocode::create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
        ]);
        $this->assertNotEmpty(Unlocode::where('countrycode', 'QQ'));
    }
}
