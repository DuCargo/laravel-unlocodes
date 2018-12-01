<?php

namespace Dc\Unlocodes\Tests\Feature;

use Dc\Unlocodes\Seeds\UnlocodeDatapackageSeeder;
use Dc\Unlocodes\Seeds\UnlocodeGroupsTableSeeder;
use Dc\Unlocodes\Seeds\UnlocodeGroupUnlocodesTableSeeder;
use Dc\Unlocodes\Seeds\UnlocodesTableSeeder;
use Dc\Unlocodes\Tests\UnlocodeTestCase;
use Dc\Unlocodes\Unlocode;
use Dc\Unlocodes\UnlocodeGroup;

/**
 * Class SeedersTest tests the working of the db seeders
 * @package Dc\Unlocodes\Tests\Feature
 */
class SeedersTest extends UnlocodeTestCase
{
    /** @test */
    function unlocodes_can_be_seeded_from_local_csv()
    {
        $this->assertEquals(0, Unlocode::count());
        $result = $this->artisan('db:seed', ['--class' => UnlocodesTableSeeder::class]);
        $this->assertLessThanOrEqual(0, $result);
        $this->assertNotEmpty(Unlocode::first()->unlocode);
    }

    /** @test */
    function unlocodes_can_be_seeded_with_datapackage()
    {
        $this->assertEquals(0, Unlocode::count());
        $result = $this->artisan('db:seed', ['--class' => UnlocodeDatapackageSeeder::class]);
        $this->assertLessThanOrEqual(0, $result);
        $this->assertNotEmpty(Unlocode::first()->unlocode);
    }

    /** @test */
    function unlocode_groups_can_be_seeded_from_local_csv()
    {
        $this->assertEquals(0, UnlocodeGroup::count());
        $result = $this->artisan('db:seed', ['--class' => UnlocodeGroupsTableSeeder::class]);
        $this->assertLessThanOrEqual(0, $result);
        $this->assertNotEmpty(UnlocodeGroup::first()->name);
    }

    /** @test */
    function unlocodes_in_groups_can_be_seeded_from_local_csv()
    {
        // Given we have some unlocodes and groups
        $this->artisan('db:seed', ['--class' => UnlocodesTableSeeder::class]);
        $result = $this->artisan('db:seed', ['--class' => UnlocodeGroupsTableSeeder::class]);
        $this->assertCount(0, UnlocodeGroup::find('Europe')->unlocodes);
        // When we seed the unlocode groups with unlocodes
        $result = $this->artisan('db:seed', ['--class' => UnlocodeGroupUnlocodesTableSeeder::class]);
        // Then we successfully linked the unlocodes to some groups
        $this->assertLessThanOrEqual(0, $result);
        $this->assertGreaterThan(0, count(UnlocodeGroup::find('Europe')->unlocodes));
    }
}
