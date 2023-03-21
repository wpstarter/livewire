<?php

namespace Tests\Unit;

use WpStarter\Support\Facades\File;
use Livewire\LivewireServiceProvider;
use WpStarter\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->makeACleanSlate();
        });

        $this->beforeApplicationDestroyed(function () {
            $this->makeACleanSlate();
        });

        parent::setUp();
    }

    public function makeACleanSlate()
    {
        Artisan::call('view:clear');

        File::deleteDirectory($this->livewireViewsPath());
        File::deleteDirectory($this->livewireClassesPath());
        File::deleteDirectory($this->livewireTestsPath());
        File::delete(ws_app()->bootstrapPath('cache/livewire-components.php'));
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('view.paths', [
            __DIR__.'/views',
            ws_resource_path('views'),
        ]);

        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('filesystems.disks.unit-downloads', [
            'driver' => 'local',
            'root' => __DIR__.'/fixtures',
        ]);
    }

    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('WpStarter\Contracts\Http\Kernel', 'Tests\HttpKernel');
    }

    protected function livewireClassesPath($path = '')
    {
        return ws_app_path('Http/Livewire'.($path ? '/'.$path : ''));
    }

    protected function livewireViewsPath($path = '')
    {
        return ws_resource_path('views').'/livewire'.($path ? '/'.$path : '');
    }

    protected function livewireTestsPath($path = '')
    {
        return ws_base_path('tests/Feature/Livewire'.($path ? '/'.$path : ''));
    }
}
