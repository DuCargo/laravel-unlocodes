<?php

namespace Dc\Unlocodes\Tests;

use Orchestra\Testbench\TestCase as TestBenchTestCase;

class TestCase extends TestBenchTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->withFactories($this->getPackagePath().'/database/factories');
    }

    public function testExcelClass()
    {
        $unlocode = app()->make( 'Dc\Unlocodes\Unlocode' );
        $this->assertInstanceOf( 'Dc\Unlocodes\Unlocode', $unlocode );
    }

    protected function getPackageProviders($app)
    {
        return [
            \Dc\Unlocodes\UnlocodesServiceProvider::class
        ];
    }

    protected function getPackagePath()
    {
        return realpath( implode( DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            'src'
        ] ) );
    }

    /**
     * Asserts that the response status was 422, checks that the given fields
     * were in the error response
     * @param \Illuminate\Foundation\Testing\TestResponse $response The response object
     * @param array|string $fields The field(s) to check for in the JSON response
     */
    protected function assertValidationError($response, $fields)
    {
        $response->assertStatus(422);
        $fields = array_wrap($fields);
        foreach ($fields as $field) {
            $this->assertArrayHasKey($field, $response->json()['errors'], "Expected \"{$field}\" in error response");
        }
    }
}
