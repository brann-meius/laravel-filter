<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Filters;

use Illuminate\Database\Eloquent\Model;
use Meius\LaravelFilter\Filters\FilterInterface;
use Meius\LaravelFilter\Tests\Support\Filters\IdFilter;
use Meius\LaravelFilter\Tests\Support\Models\User;
use Meius\LaravelFilter\Tests\TestCase;

class FilterTest extends TestCase
{
    public function testInitializesModelSuccessfully(): void
    {
        $this->invokeInitializeModel(User::class);

        $this->assertTrue(User::hasGlobalScope('filter:users-by-id'));
    }

    public function testInitializesModelFailsForNonExistentClass(): void
    {
        $this->invokeInitializeModel('App\Models\NonExistentModel');

        $this->assertEmpty(Model::getAllGlobalScopes());
    }

    protected function invokeInitializeModel(string $model): void
    {
        $this->getFilter()($model, $this->request);
    }

    protected function getFilter(): FilterInterface
    {
        return new IdFilter();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->addToRequest(['users' => [
            'id' => 1,
        ]]);
    }
}