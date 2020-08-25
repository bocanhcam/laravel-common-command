<?php

namespace Hora\LaravelCommonCommand;

use Hora\LaravelCommonCommand\Commands\MakeRepository;
use Illuminate\Support\ServiceProvider;

class LaravelCommonCommandServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected $commands = [
        MakeRepository::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
        $this->mergeConfig();
        $this->publishConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     *
     */
    private function publishConfig(): void
    {
        $path = $this->getConfigPath();
        $this->publishes([
            $path => config_path('laravel-common-command.php'),
        ]);
    }

    /**
     * @return string
     */
    private function getConfigPath(): string
    {
        return __DIR__ . '/config/laravel-common-command.php';
    }

    /**
     *
     */
    private function mergeConfig(): void
    {
        $path = $this->getConfigPath();
        $this->mergeConfigFrom($path, 'laravel-common-command');
    }
}
