<?php

namespace Dc\Unlocodes\Tests;

use Dc\Unlocodes\Unlocode;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnlocodeTestCase extends TestCase
{
    use RefreshDatabase;
    //use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $db = app()->make('db');
        $db->getSchemaBuilder()->enableForeignKeyConstraints();
    }

    /**
     * Asserts that the response status was 422, checks that the given fields
     * were in the error response and asserts that no contract was created
     * @param \Illuminate\Foundation\Testing\TestResponse $response The response object
     * @param array|string $fields The field(s) to check for in the JSON response
     * @param int $expected_count The number of Unlocodes to expect to exist now
     */
    protected function assertValidationError($response, $fields, $expected_count = 0)
    {
        parent::assertValidationError($response, $fields);
        $this->assertEquals($expected_count, Unlocode::count());
    }
}
