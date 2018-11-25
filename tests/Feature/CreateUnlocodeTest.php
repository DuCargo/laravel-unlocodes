<?php

namespace Dc\Unlocodes\Tests\Feature;

use Dc\Unlocodes\Seeds\UnlocodeDatapackageSeeder;
use Dc\Unlocodes\Tests\UnlocodeTestCase;
use Dc\Unlocodes\Unlocode;

/**
 * Class CreateUnlocodeTest tests the working and validation when an unlocode gets created/inserted
 * @package Dc\Unlocodes\Tests\Feature
 */
class CreateUnlocodeTest extends UnlocodeTestCase
{
    /**
     * Sends a post event to the unlocodes controller in order to create an unlocode
     * @param array $params Array of parameters to send
     * @return \Illuminate\Foundation\Testing\TestResponse The response object
     */
    private function createUnlocode(array $params)
    {
        return $this->json('POST', '/api/unlocodes', $params);
    }

    /** @test */
    function unlocodes_can_be_seeded() {
        $result = $this->artisan('db:seed', ['--database' => 'testing', '--class' => UnlocodeDatapackageSeeder::class]);
        $this->assertLessThanOrEqual(0, $result);
        $this->assertNotEmpty(Unlocode::where('countrycode', 'ZW'));
    }

    /** @test */
    function user_can_create_unlocode()
    {
        // When we create an unlocode
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'subdivision' => '01',
            'longitude' => 52.2,
            'latitude' => -0.2,
        ]);

        // Then we should get a 'created' response an an unlocode should exist afterwards
        $response->assertStatus(201);
        $this->assertEquals(1, Unlocode::count());
        $unlocode = Unlocode::first();
        $this->assertEquals('QQ', $unlocode->countrycode);
        $this->assertEquals('QQQ', $unlocode->placecode);
        $this->assertEquals('Test code', $unlocode->name);
    }

    /** @test */
    function countrycode_is_required()
    {
        // When we create an unlocode without specifying countrycode
        $response = $this->createUnlocode([
            'placecode' => 'QQQ',
            'name' => 'Test code',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode');
    }

    /** @test */
    function countrycode_type_must_be_a_string()
    {
        // When we create an unlocode with a countrycode of the wrong type
        $response = $this->createUnlocode([
            'countrycode' => 42,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode');
    }

    /** @test */
    function countrycode_must_be_capital_letters()
    {
        // When we create an unlocode with a countrycode of the wrong case
        $response = $this->createUnlocode([
            'countrycode' => 'qq',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode');

        // And when we create an unlocode with a countrycode of the wrong character types
        $response = $this->createUnlocode([
            'countrycode' => '.2',
        ]);

        // Then we should also get a validation error
        $this->assertValidationError($response, 'countrycode');
    }

    /** @test */
    function countrycode_must_be_long_enough()
    {
        // When we create an unlocode with a long countrycode
        $response = $this->createUnlocode([
            'countrycode' => 'Q',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode');
    }

    /** @test */
    function countrycode_must_not_be_too_long()
    {
        // When we create an unlocode with a long countrycode
        $countrycode = str_repeat('Q', 4);
        $response = $this->createUnlocode([
            'countrycode' => $countrycode,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'countrycode');
    }

    /** @test */
    function placecode_is_required()
    {
        // When we create an unlocode without specifying placecode
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'name' => 'Test code',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode');
    }

    /** @test */
    function placecode_type_must_be_a_string()
    {
        // When we create an unlocode with a placecode of the wrong type
        $response = $this->createUnlocode([
            'placecode' => 42,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode');
    }

    /** @test */
    function placecode_must_be_capital_letters()
    {
        // When we create an unlocode with a placecode of the wrong case
        $response = $this->createUnlocode([
            'placecode' => 'qqq',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode');

        // And when we create an unlocode with a placecode of the wrong character types (0 and 1 are not allowed)
        $response = $this->createUnlocode([
            'placecode' => '101',
        ]);

        // Then we should also get a validation error
        $this->assertValidationError($response, 'placecode');
    }

    /** @test */
    function placecode_must_be_long_enough()
    {
        // When we create an unlocode with a long placecode
        $placecode = 'Q';
        $response = $this->createUnlocode([
            'placecode' => $placecode,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode');
    }

    /** @test */
    function placecode_must_not_be_too_long()
    {
        // When we create an unlocode with a long placecode
        $placecode = str_repeat('Q', 4);
        $response = $this->createUnlocode([
            'placecode' => $placecode,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'placecode');
    }

    /** @test */
    function name_is_required()
    {
        // When we create an unlocode without specifying name
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'name');
    }

    /** @test */
    function name_type_must_be_a_string()
    {
        // When we create an unlocode with a name of the wrong type
        $response = $this->createUnlocode([
            'name' => 42,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'name');
    }

    /** @test */
    function name_must_not_be_too_long()
    {
        // When we create an unlocode with a long name
        $name = str_repeat('Qq ', 34);
        $response = $this->createUnlocode([
            'name' => $name,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'name');
    }

    /** @test */
    function subdivision_can_be_null()
    {
        // When we create an unlocode with a subdivision of null
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'subdivision' => null,
        ]);

        // Then we should have a new unlocode
        $response->assertStatus(201);
        $this->assertEquals(1, Unlocode::count());
        $this->assertNull(Unlocode::first()->subdivision);
    }

    /** @test */
    function subdivision_type_must_be_a_string()
    {
        // When we create an unlocode with a subdivision of the wrong type
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'subdivision' => 42,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'subdivision');
    }

    /** @test */
    function subdivision_type_must_not_be_too_long()
    {
        // When we create an unlocode with a long subdivision
        $subdivision = str_repeat('.', 4);
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'subdivision' => $subdivision,
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'subdivision');
    }

    /** @test */
    function longitude_can_be_null()
    {
        // When we create an unlocode with a longitude of null
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'longitude' => null,
        ]);

        // Then we should have a new unlocode
        $response->assertStatus(201);
        $this->assertEquals(1, Unlocode::count());
        $this->assertNull(Unlocode::first()->longitude);
    }

    /** @test */
    function longitude_type_must_be_a_float()
    {
        // When we create an unlocode with a longitude of the wrong type
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'longitude' => [1, 2, 3],
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'longitude');
    }

    /** @test */
    function latitude_can_be_null()
    {
        // When we create an unlocode with a latitude of null
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'latitude' => null,
        ]);

        // Then we should have a new unlocode
        $response->assertStatus(201);
        $this->assertEquals(1, Unlocode::count());
        $this->assertNull(Unlocode::first()->latitude);
    }

    /** @test */
    function latitude_type_must_be_a_float()
    {
        // When we create an unlocode with a latitude of the wrong type
        $response = $this->createUnlocode([
            'countrycode' => 'QQ',
            'placecode' => 'QQQ',
            'name' => 'Test code',
            'latitude' => [1, 2, 3],
        ]);

        // Then we should get a validation error
        $this->assertValidationError($response, 'latitude');
    }

}
