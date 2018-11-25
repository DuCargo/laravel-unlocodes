<?php

namespace Dc\Unlocodes\Tests\Feature;

use Dc\Unlocodes\Tests\UnlocodeTestCase;
use Dc\Unlocodes\Unlocode;
use Illuminate\Cache\ArrayStore;
use Illuminate\Support\Facades\Cache;

/**
 * Class ViewUnlocodeTest tests the retrieval, listing and search for/of unlocodes
 * @package Dc\Unlocodes\Tests\Feature
 */
class ViewUnlocodeTest extends UnlocodeTestCase
{
    /**
     * Sends a get event to the unlocodes controller in order to retrieve an unlocode
     * @param string $unlocode e.g. NLRTM
     * @return \Illuminate\Foundation\Testing\TestResponse The response object
     * @internal param array $params Array of parameters to send
     */
    private function getUnlocode(string $unlocode)
    {
        return $this->json('GET', "/api/unlocodes/{$unlocode}");
    }

    /**
     * Sends a get event to the unlocodes controller in order to retrieve an unlocodes listing
     * @return \Illuminate\Foundation\Testing\TestResponse The response object
     */
    private function getUnlocodes()
    {
        return $this->json('GET', "/api/unlocodes");
    }

    /**
     * Sends a post event with a search term to the unlocodes controller in order to search for matching unlocodes
     * @param string $term e.g. Rott would result in returned: ['Rotterdam, NL']
     * @return \Illuminate\Foundation\Testing\TestResponse The response object
     */
    private function searchUnlocodes(string $term)
    {
        return $this->json('GET', "/api/unlocodes/search/{$term}");
    }

    /** @test */
    function user_can_retrieve_an_unlocode()
    {
        // Given we have a unlocode
        factory(Unlocode::class)->create();

        // When we retrieve it
        $response = $this->getUnlocode('NLRTM');

        // Then we should get a response and a unlocode should be passed
        $response->assertSuccessful()
            ->assertJson([
                'countrycode' => 'NL',
                'placecode' => 'RTM',
                'name' => 'Rotterdam',
            ]);
    }

    /** @test */
    function result_is_being_cached()
    {
        $this->markTestSkipped('Cache mock doesnt work anymore');
        // Given we have a unlocode
        factory(Unlocode::class)->create();
        $object = [
            'countrycode' => 'NL',
            'placecode' => 'RTM',
            'name' => 'Rotterdam',
            'longitude' => null,
            'latitude' => null,
            'subdivision' => '',
            'status' => '',
            'date' => '',
            'IATA' => '',
        ];

        // Then below, the cache class should call rememberForever() twice
        \Cache::spy()
            ->shouldReceive('rememberForever')
            ->twice()
            ->with('unlocode_NLRTM', \Mockery::type('Closure'))
            ->andReturn($object);
        // FIXME
        // And it should call forever() only once (second time it returns from cache)
//        \Dc\Unlocodes\Facades\Unlocode::shouldReceive('where')
//            ->once();

        // When we retrieve it
        $this->getUnlocode('NLRTM');
        $unlocode = $this->getUnlocode('NLRTM');

        $this->assertEquals($unlocode, $object);
    }

    /** @test */
    function user_can_retrieve_an_unlocodes_listing()
    {
        // Given we have a unlocode
        factory(Unlocode::class)->create();

        // When we retrieve it
        $response = $this->getUnlocodes();

        // Then we should get a paginated response and unlocodes should be passed
        $this->assertGreaterThan(0, count($response->json()));
        $response->assertSuccessful()
            ->assertJson(
                [
                    'per_page' => 15,
                    'data' => [
                        [
                            'countrycode' => 'NL',
                            'placecode' => 'RTM',
                            'name' => 'Rotterdam',
                        ],
                    ],
                ]);
    }

    /** @test */
    function user_can_search_for_an_unlocode()
    {
        // Given we have a unlocode
        factory(Unlocode::class)->create(['name' => 'Rotterdam']);

        // When we search for a matching term
        $response = $this->searchUnlocodes('Rot');

        // Then we should get a list of matching unlocodes
        $this->assertGreaterThan(0, count($response->json()));
        $response->assertSuccessful()
            ->assertJson(
                [
                    [
                        'countrycode' => 'NL',
                        'placecode' => 'RTM',
                        'name' => 'Rotterdam',
                    ],
                ]);
    }
}
