<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services;

use Meius\LaravelFilter\Exceptions\InvalidFilterBindingException;
use Meius\LaravelFilter\Factories\FilterManagerFactory;
use Meius\LaravelFilter\Tests\Support\Models\User;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery\MockInterface;

class ControllerManagerTest extends TestCase
{
    public function testAppliesFiltersSuccessfully(): void
    {
        $this->call('GET', '/users', $this->request->all())
            ->assertOk()
            ->assertJson([]);

        $this->assertTrue(User::hasGlobalScope('filter:users-by-id'));
    }

    public function testDoesNotApplyFiltersSuccessfullyDuringRedirect(): void
    {
        $this->call('GET', '/', $this->request->all())
            ->assertRedirect('/users');

        $this->assertFalse(User::hasGlobalScope('filter:users-by-id'));
    }

    public function testDoesNotApplyFiltersWhenNoAttributes(): void
    {
        $this->post('/users', $this->request->all())
            ->assertOk()
            ->assertJson([]);

        $this->assertFalse(User::hasGlobalScope('filter:users-by-id'));
    }

    public function testDoesNotApplyFiltersWhenAttributeDoesNotHaveModels(): void
    {
        $this->call('GET', '/users/12', $this->request->all())
            ->assertOk()
            ->assertJson([]);

        $this->assertFalse(User::hasGlobalScope('filter:users-by-id'));
    }

    public function testInvalidFilterBindingException(): void
    {
        $this->mock(FilterManagerFactory::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')
                ->andThrow(new InvalidFilterBindingException());
        });

        $this->withoutExceptionHandling();
        $this->expectException(InvalidFilterBindingException::class);

        $this->call('GET', '/users', $this->request->all());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->addToRequest([
            'users' => [
                'id' => 1,
            ]
        ]);
    }
}
