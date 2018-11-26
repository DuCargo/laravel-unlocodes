<?php

namespace Dc\Unlocodes\Tests\Feature;

use Dc\Unlocodes\Helpers\UnlocodeHelper;
use Dc\Unlocodes\Tests\UnlocodeTestCase;
use Dc\Unlocodes\Unlocode;

/**
 * Class ViewUnlocodeTest tests the retrieval, listing and search for/of unlocodes
 * @package Dc\Unlocodes\Tests\Feature
 */
class DeleteUnlocodeTest extends UnlocodeTestCase
{
    /** @test */
    function user_can_delete_an_unlocode()
    {
        // Given we have a unlocode and we cache it
        factory(Unlocode::class)->create();
        $this->json('GET', "/api/unlocodes/NLRTM");
        $this->assertNotEmpty(\Cache::get(UnlocodeHelper::cacheKey('NL', 'RTM')));

        // When we delete it
        $response = $this->json('DELETE', "/api/unlocodes/NLRTM");

        // Then we should get a response and a success boolean should be returned
        $response->assertSuccessful();
        $this->assertTrue((bool) $response->getContent());
        // And then the cache is cleared
        $this->assertEmpty(\Cache::get(UnlocodeHelper::cacheKey('NL', 'RTM')));

    }

    /** @test */
    function user_can_not_delete_a_non_existing_unlocode()
    {
        // Given we have no unlocode
        // When we try to delete it
        $response = $this->json('DELETE', "/api/unlocodes/NLRTM");

        // Then we should get a 404 response
        $response->assertNotFound();
    }
}