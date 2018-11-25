<?php

namespace Dc\Unlocodes\Tests\Unit;

use Dc\Unlocodes\Helpers\UnlocodeHelper;
use Dc\Unlocodes\Unlocode;
use Dc\Unlocodes\Tests\TestCase;

class UnlocodeHelperTest extends TestCase
{
    /** @test */
    function spread_unlocode_throws_exception_on_invalid_format() {
        $this->expectException(\InvalidArgumentException::class);
        UnlocodeHelper::spread_unlocode('ABCDEF');
    }

    /** @test */
    function spread_unlocode_spreads_unlocode()
    {
        $unlocode = UnlocodeHelper::spread_unlocode('NLRTM');
        $this->assertEquals(['NL', 'RTM'], $unlocode);
    }

    /** @test */
    function cache_key_include_unlocode()
    {
        $cacheKey = UnlocodeHelper::cacheKey('NL', 'RTM');
        $this->assertEquals('unlocode_NLRTM', $cacheKey);
    }
}
