<?php

namespace Tests\Unit;

use App\Models\Business;
use App\Services\SyncRoutine;
use App\Utils\Apis;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class SyncRoutineTest extends TestCase
{
    use DatabaseMigrations;


    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_run_successfully()
    {
        $business = Business::find(1);
        $this->assertNotNull($business);
        $syncRouting = new SyncRoutine();

//        $reflector = new \ReflectionProperty(SyncRoutine::class, 'api');
//        $reflector->setAccessible(true);
//        $reflector->setValue();

//        $this->mock(Apis::class, function (MockInterface $mock) {
//            $mock->shouldReceive('clairPayItemSync')
//                ->once()
//                ->andReturn([]);
//        });


        $syncRouting->syncPayItems($business);
        $this->assertTrue(true);
    }

    // TODO: more unit tests..
}
