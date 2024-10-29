<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services;

use Meius\LaravelFilter\Exceptions\InvalidControllerMethodException;
use Meius\LaravelFilter\Exceptions\InvalidFilterBindingException;
use Meius\LaravelFilter\Factories\FilterManagerFactory;
use Meius\LaravelFilter\Tests\Support\Http\Controllers\UserController;
use Meius\LaravelFilter\Tests\Support\Models\User;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery\MockInterface;

class ControllerManagerTest extends TestCase
{
    /**
     * @throws InvalidFilterBindingException
     * @throws InvalidControllerMethodException
     */
    public function testAppliesFiltersSuccessfully(): void
    {
        $this->callControllerMethod('index');
        $this->assertTrue(User::hasGlobalScope('filter:users-by-id'));
    }

    /**
     * @throws InvalidFilterBindingException
     * @throws InvalidControllerMethodException
     */
    public function testDoesNotApplyFiltersWhenNoAttributes(): void
    {
        $this->callControllerMethod('store');
        $this->assertFalse(User::hasGlobalScope('filter:users-by-id'));
    }

    /**
     * @throws InvalidFilterBindingException
     * @throws InvalidControllerMethodException
     */
    public function testDoesNotApplyFiltersWhenAttributeDoesNotHaveModels(): void
    {
        $this->callControllerMethod('edit');
        $this->assertFalse(User::hasGlobalScope('filter:users-by-id'));
    }

    /**
     * @throws InvalidFilterBindingException
     */
    public function testInvalidMethodCallThrowsException(): void
    {
        $this->expectException(InvalidControllerMethodException::class);
        $this->callControllerMethod('update');
    }

    /**
     * @throws InvalidControllerMethodException
     */
    public function testInvalidFilterBindingException(): void
    {
        $this->expectException(InvalidFilterBindingException::class);
        $this->mock(FilterManagerFactory::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')
                ->andThrow(new InvalidFilterBindingException());
        });
        $this->callControllerMethod('index');
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


    /**
     * @throws InvalidFilterBindingException
     * @throws InvalidControllerMethodException
     */
    private function callControllerMethod(string $method): void
    {
        /** @var UserController $controller */
        $controller = $this->app->make(UserController::class);
        $controller->callAction($method, []);
    }
}
