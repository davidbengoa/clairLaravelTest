<?php

namespace Tests\Unit;

use App\Models\Business;
use App\Services\SyncRoutine;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SyncRoutineTest extends TestCase
{
    use DatabaseMigrations;
    private SyncRoutine $syncRouting;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->syncRouting = new SyncRoutine();
    }

    public function test_example()
    {
        $business = Business::find(1);
        $this->assertNotNull($business);
        //$this->syncRouting->syncPayItems($business);
        $this->assertTrue(true);
    }

    // TODO: more unit tests..
}
