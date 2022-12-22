<?php

namespace Tests\Unit;

use App\Utils\Apis;
use Exception;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApisTest extends TestCase
{
    private Apis $api;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->api = new Apis();
    }

    public function test_invalid_x_api_key()
    {
        Http::fake(['*' => Http::response([], 401)]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("API - 401 received");
        $response = $this->api->clairPayItemSync("external-id", 1);
        $this->assertThrows($response);
    }

    public function test_business_not_found()
    {
        Http::fake(['*' => Http::response([], 404)]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("API - 404 received");
        $response = $this->api->clairPayItemSync("external-id", 1);
        $this->assertThrows($response);
    }

    /**
     * @throws Exception
     */
    public function test_successful_call()
    {
        $expected_response = ["good_response"];
        Http::fake(['*' => Http::response($expected_response)]);
        $response = $this->api->clairPayItemSync("external-id", 1);
        $this->assertEquals($response, $expected_response);
    }
}