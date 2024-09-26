<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Unit\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Meius\LaravelFilter\Factories\FilterManagerFactory;
use Meius\LaravelFilter\Providers\FilterServiceProvider;
use Meius\LaravelFilter\Tests\Support\Http\Controllers\UserController;
use Meius\LaravelFilter\Tests\Support\Http\Models\User;
use Meius\LaravelFilter\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

class ControllerManagerTest extends TestCase
{
    public function testAppliesFiltersSuccessfully(): void
    {
        /** @var UserController $controller */
        $controller = $this->app->make(UserController::class);
        $controller->callAction('index', []);

        $this->assertTrue(User::hasGlobalScope('filter:users-by-id'));
    }

    public function testDoesNotApplyFiltersWhenNoAttributes(): void
    {
        /** @var UserController $controller */
        $controller = $this->app->make(UserController::class);
        $controller->callAction('store', []);

        $this->assertFalse(User::hasGlobalScope('filter:users-by-id'));
    }

    public function testDoesNotApplyFiltersWhenAttributeDoesNotHaveModels(): void
    {
        /** @var UserController $controller */
        $controller = $this->app->make(UserController::class);
        $controller->callAction('edit', []);

        $this->assertFalse(User::hasGlobalScope('filter:users-by-id'));
    }

    public function testLogsErrorOnException(): void
    {
        Log::expects('error')
            ->with(
                'Failed to apply filters.',
                Mockery::on(function (array $context) {
                    return isset($context['exception']) && $context['exception'] instanceof \Throwable;
                })
            );

        $this->partialMock(FilterManagerFactory::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')
                ->andThrow(new BindingResolutionException('Test exception'));
        });

        /** @var UserController $controller */
        $controller = $this->app->make(UserController::class);

        $controller->callAction('index', []);

        $this->assertFalse(User::hasGlobalScope('filter:users-by-id'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('filter.path', __DIR__.'/../../Support/Filters');

        $this->app->register(FilterServiceProvider::class);

        Request::instance()
            ->merge([
                'filter' => [
                    'users' => [
                        'id' => 1,
                    ]
                ]
            ]);
    }
}