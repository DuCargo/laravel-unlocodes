<?php

namespace Dc\Unlocodes\Tests\Feature;

use Dc\Unlocodes\Helpers\UnlocodeHelper;
use Dc\Unlocodes\Tests\UnlocodeTestCase;
use Dc\Unlocodes\Unlocode;

/**
 * Class CreateUnlocodeTest tests the working and validation when an unlocode gets updated
 * @package Dc\Unlocodes\Tests\Feature
 */
class UpdateUnlocodeTest extends UnlocodeTestCase
{
    /**
     * Sends a patch event to the unlocodes controller in order to update an unlocode
     * @param array $params Array of parameters to send
     * @return \Illuminate\Foundation\Testing\TestResponse The response object
     */
    private function updateUnlocode(string $unlocode, array $params)
    {
        return $this->json('PUT', "/api/unlocodes/{$unlocode}", $params);
    }

    /** @test */
    function user_can_update_unlocode()
    {
        // Given we have a unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update the unlocode
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'RR',
            'placecode' => 'RRR',
            'name' => 'Test coder',
        ]);

        // Then we should get a 'updated' response an an unlocode should exist afterwards
        $response->assertStatus(204);
        $unlocode = Unlocode::first();
        $this->assertEquals('RR', $unlocode->countrycode);
        $this->assertEquals('RRR', $unlocode->placecode);
        $this->assertEquals('Test coder', $unlocode->name);
    }

    /** @test */
    function unlocode_id_should_be_well_formatted()
    {
        // When we try to update an unlocode specifying a bad unlocode string
        $response = $this->updateUnlocode('bad', []);

        // Then we should get an error
        $this->assertFalse($response->isSuccessful());
    }

    /** @test */
    function countrycode_is_required()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we create an unlocode without specifying countrycode
        $response = $this->updateUnlocode('QQQQQ', [
            'placecode' => 'RRR',
            'name' => 'Test code',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode', 1);
    }

    /** @test */
    function countrycode_type_must_be_a_string()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we create an unlocode with a countrycode of the wrong type
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 42,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode', 1);
    }

    /** @test */
    function countrycode_must_be_capital_letters()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we create an unlocode with a countrycode of the wrong type
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'qq',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode', 1);

        // And when we create an unlocode with a countrycode of the wrong character types
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => '.2',
        ]);

        // Then we should also get a validation error
        $this->assertValidationError($response, 'countrycode', 1);
    }

    /** @test */
    function countrycode_must_be_long_enough()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a long countrycode
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'Q',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode', 1);
    }

    /** @test */
    function countrycode_must_not_be_too_long()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a long countrycode
        $countrycode = str_repeat('Q', 4);
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => $countrycode,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode', 1);
    }

    /** @test */
    function placecode_is_required()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it without specifying placecode
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'QQ',
            'name' => 'Test code',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode', 1);
    }

    /** @test */
    function placecode_type_must_be_a_string()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a placecode of the wrong type
        $response = $this->updateUnlocode('QQQQQ', [
            'placecode' => 42,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode', 1);
    }

    /** @test */
    function placecode_must_be_capital_letters()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a placecode of the wrong case
        $response = $this->updateUnlocode('QQQQQ', [
            'placecode' => 'qqq',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode', 1);

        // And when we update it with a placecode of the wrong character types (0 and 1 are not allowed)
        $response = $this->updateUnlocode('QQQQQ', [
            'placecode' => '101',
        ]);

        // Then we should also get a validation error
        $this->assertValidationError($response, 'placecode', 1);
    }

    /** @test */
    function placecode_must_be_long_enough()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a long placecode
        $placecode = 'Q';
        $response = $this->updateUnlocode('QQQQQ', [
            'placecode' => $placecode,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode', 1);
    }

    /** @test */
    function placecode_must_not_be_too_long()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a long placecode
        $placecode = str_repeat('Q', 4);
        $response = $this->updateUnlocode('QQQQQ', [
            'placecode' => $placecode,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode', 1);
    }

    /** @test */
    function name_is_required()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it without specifying name
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'name', 1);
    }

    /** @test */
    function name_type_must_be_a_string()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a name of the wrong type
        $response = $this->updateUnlocode('QQQQQ', [
            'name' => 42,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'name', 1);
    }

    /** @test */
    function name_must_not_be_too_long()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a long name
        $name = str_repeat('Qq ', 34);
        $response = $this->updateUnlocode('QQQQQ', [
            'name' => $name,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'name', 1);
    }

    /** @test */
    function subdivision_can_be_null()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a subdivision of null
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'subdivision' => null,
        ]);

        // Then we should have a new unlocode
        $response->assertStatus(204);
        $this->assertEquals(1, Unlocode::count());
        $this->assertNull(Unlocode::first()->subdivision);
    }

    /** @test */
    function subdivision_type_must_be_a_string()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a subdivision of the wrong type
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'subdivision' => 42,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'subdivision', 1);
    }

    /** @test */
    function subdivision_type_must_not_be_too_long()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a long subdivision
        $subdivision = str_repeat('.', 4);
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'subdivision' => $subdivision,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'subdivision', 1);
    }

    /** @test */
    function longitude_can_be_null()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a longitude of null
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'longitude' => null,
        ]);

        // Then we should have a new unlocode
        $response->assertStatus(204);
        $this->assertEquals(1, Unlocode::count());
        $this->assertNull(Unlocode::first()->longitude);
    }

    /** @test */
    function longitude_type_must_be_a_float()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a longitude of the wrong type
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'longitude' => [1, 2, 3],
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'longitude', 1);
    }

    /** @test */
    function latitude_can_be_null()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a latitude of null
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'latitude' => null,
        ]);

        // Then we should have a new unlocode
        $response->assertStatus(204);
        $this->assertEquals(1, Unlocode::count());
        $this->assertNull(Unlocode::first()->latitude);
    }

    /** @test */
    function latitude_type_must_be_a_float()
    {
        // Given we have an unlocode
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // When we update it with a latitude of the wrong type
        $response = $this->updateUnlocode('QQQQQ', [
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'latitude' => [1, 2, 3],
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'latitude', 1);
    }

    /** @test */
    function cannot_update_to_existing()
    {
        // Given two unlocodes exist
        factory(Unlocode::class)->create([
            'countrycode' => 'PP',
            'placecode' => 'QQQ',
            'name' => 'Pest coder',
        ]);
        factory(Unlocode::class)->create([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test coder',
        ]);
        // When we try to update one to the other
        $response = $this->updateUnlocode('PPQQQ', [
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test coder',
        ]);
        // Then we should get an error
        $response->assertStatus(422);
        $this->assertArrayHasKey('message', $response->json(), "Expected message in error response");
        $this->assertContains('QQQQQ', $response->json()['message'], "Expected unlocode in message");
    }

    /**
     * Cache clear is tested here in a Feature test because the route binding does the caching
     * @test
     */
    function cache_is_cleared_after_update()
    {
        // Given we have a unlocode and it's cached
        $unlocode = factory(Unlocode::class)->create();
        $this->json('GET', "/api/unlocodes/{$unlocode->unlocode}");
        $this->assertTrue(\Cache::has(UnlocodeHelper::cacheKey($unlocode)));
        // When we update it
        $this->updateUnlocode($unlocode->unlocode, [
            'countrycode' => 'NL',
            'placecode' => 'RTM',
            'name' => 'Rotjeknor'
        ]);
        // Then the cache is cleared
        $this->assertFalse(\Cache::has(UnlocodeHelper::cacheKey($unlocode)));
    }
}
