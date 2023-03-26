<?php

namespace Tests\Browser;

use Closure;
use Exception;
use Psy\Shell;
use Tests\Browser\Stubs\AllowListedMiddleware;
use Tests\Browser\Stubs\AllowListedMiddlewareTyped;
use Tests\Browser\Stubs\BlockListedMiddleware;
use Tests\Browser\Stubs\BlockListedMiddlewareTyped;
use Throwable;
use Sushi\Sushi;
use Livewire\Livewire;
use Livewire\Component;
use Laravel\Dusk\Browser;
use function Livewire\str;
use WpStarter\Support\Facades\Auth;
use WpStarter\Support\Facades\File;
use WpStarter\Support\Facades\Gate;
use WpStarter\Support\Facades\Route;
use Livewire\LivewireServiceProvider;
use Livewire\Macros\DuskBrowserMacros;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Support\Facades\Artisan;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use WpStarter\Foundation\Auth\User as AuthUser;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use WpStarter\Support\Facades\View;
use Orchestra\Testbench\Dusk\Options as DuskOptions;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use SupportsSafari;

    public static $useSafari = false;
    public static $useAlpineV3 = false;

    public function setUp(): void
    {
        if (isset($_SERVER['CI'])) {
            DuskOptions::withoutUI();
        }

        Browser::mixin(new DuskBrowserMacros);

        $this->afterApplicationCreated(function () {
            $this->makeACleanSlate();
        });

        $this->beforeApplicationDestroyed(function () {
            $this->makeACleanSlate();
        });

        parent::setUp();

        // $thing = get_class($this);

        $isUsingAlpineV3 = static::$useAlpineV3;

        $this->tweakApplication(function () use ($isUsingAlpineV3) {
            // Autoload all Livewire components in this test suite.
            ws_collect(File::allFiles(__DIR__))
                ->map(function ($file) {
                    return 'Tests\\Browser\\'.str($file->getRelativePathname())->before('.php')->replace('/', '\\');
                })
                ->filter(function ($computedClassName) {
                    return class_exists($computedClassName);
                })
                ->filter(function ($class) {
                    return is_subclass_of($class, Component::class);
                })->each(function ($componentClass) {
                    ws_app('livewire')->component($componentClass);
                });

            Route::get(
                '/livewire-dusk/tests/browser/sync-history-without-mount/{id}',
                \Tests\Browser\SyncHistory\ComponentWithMount::class
            )->middleware('web')->name('sync-history-without-mount');

            // This needs to be registered for Dusk to test the route-parameter binding
            // See: \Tests\Browser\SyncHistory\Test.php
            Route::get(
                '/livewire-dusk/tests/browser/sync-history/{step}',
                \Tests\Browser\SyncHistory\Component::class
            )->middleware('web')->name('sync-history');

            Route::get(
                '/livewire-dusk/tests/browser/sync-history-without-query-string/{step}',
                \Tests\Browser\SyncHistory\ComponentWithoutQueryString::class
            )->middleware('web')->name('sync-history-without-query-string');

            Route::get(
                '/livewire-dusk/tests/browser/sync-history-with-optional-parameter/{step?}',
                \Tests\Browser\SyncHistory\ComponentWithOptionalParameter::class
            )->middleware('web')->name('sync-history-with-optional-parameter');

            // The following two routes belong together. The first one serves a view which in return
            // loads and renders a component dynamically. There may not be a POST route for the first one.
            Route::get('/livewire-dusk/tests/browser/load-dynamic-component', function () {
                return View::file(__DIR__ . '/DynamicComponentLoading/view-load-dynamic-component.blade.php');
            })->middleware('web')->name('load-dynamic-component');

            Route::post('/livewire-dusk/tests/browser/dynamic-component', function () {
                return View::file(__DIR__ . '/DynamicComponentLoading/view-dynamic-component.blade.php');
            })->middleware('web')->name('dynamic-component');

            if (version_compare(PHP_VERSION, '7.4', '>=')) {
                $middleware = ['web', AllowListedMiddlewareTyped::class, BlockListedMiddlewareTyped::class];
            } else {
                $middleware = ['web', AllowListedMiddleware::class, BlockListedMiddleware::class];
            }

            Route::get('/force-login/{userId}', function ($userId) {
                Auth::login(User::find($userId));

                return 'You\'re logged in.';
            })->middleware('web');

            Route::get('/force-logout', function () {
                Auth::logout();

                return 'You\'re logged out.';
            })->middleware('web');

            Route::get('/with-authentication/livewire-dusk/{component}', function ($component) {
                $class = urldecode($component);

                return ws_app()->call(new $class);
            })->middleware(['web', 'auth']);

            Gate::policy(Post::class, PostPolicy::class);

            Route::get('/with-authorization/{post}/livewire-dusk/{component}', function (Post $post, $component) {
                $class = urldecode($component);

                return ws_app()->call(new $class);
            })->middleware(['web', 'auth', 'can:update,post']);

            Route::middleware('web')->get('/entangle-turbo', function () {
                return ws_view('turbo', [
                    'link' => '/livewire-dusk/' . urlencode(\Tests\Browser\Alpine\Entangle\ToggleEntangledTurbo::class),
                ]);
            })->name('entangle-turbo');

            Route::get('/livewire-dusk/{component}', function ($component) {
                $class = urldecode($component);

                return ws_app()->call(new $class);
            })->middleware($middleware);

            Route::get('/{locale}/livewire-dusk/{component}', function ($locale, $component) {
                $class = urldecode($component);

                return ws_app()->call(new $class);
            })->middleware($middleware);

            ws_app('session')->put('_token', 'this-is-a-hack-because-something-about-validating-the-csrf-token-is-broken');

            ws_app('config')->set('view.paths', [
                __DIR__.'/views',
                ws_resource_path('views'),
            ]);

            ws_config()->set('app.debug', true);

            if (version_compare(PHP_VERSION, '7.4', '>=')) {
                Livewire::addPersistentMiddleware(AllowListedMiddlewareTyped::class);
            } else {
                Livewire::addPersistentMiddleware(AllowListedMiddleware::class);
            }

            ws_app('config')->set('use_alpine_v3', $isUsingAlpineV3);
        });
    }

    protected function tearDown(): void
    {
        $this->removeApplicationTweaks();

        parent::tearDown();
    }

    // We don't want to deal with screenshots or console logs.
    protected function storeConsoleLogsFor($browsers) {}
    protected function captureFailuresFor($browsers) {}

    public function makeACleanSlate()
    {
        Artisan::call('view:clear');

        File::deleteDirectory($this->livewireViewsPath());
        File::cleanDirectory(__DIR__.'/downloads');
        File::deleteDirectory($this->livewireClassesPath());
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

        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('filesystems.disks.dusk-downloads', [
            'driver' => 'local',
            'root' => __DIR__.'/downloads',
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

    protected function driver(): RemoteWebDriver
    {
        $options = DuskOptions::getChromeOptions();
        //$options->addArguments(['--headless','no-sandbox','--window-size=1920,1080','--disable-gpu','--start-maximized']);
        $options->setExperimentalOption('prefs', [
            'download.default_directory' => __DIR__.DIRECTORY_SEPARATOR.'downloads',
            //'download.prompt_for_download'=>false,
            //"download.directory_upgrade"=> true,
            //"safebrowsing.enabled"=> false,
        ]);

        return static::$useSafari
            ? RemoteWebDriver::create(
                'http://localhost:9515', DesiredCapabilities::safari()
            )
            : RemoteWebDriver::create(
                'http://localhost:9515',
                DesiredCapabilities::chrome()->setCapability(
                    ChromeOptions::CAPABILITY,
                    $options
                )
            );
    }

    public function browse(Closure $callback)
    {
        parent::browse(function (...$browsers) use ($callback) {
            try {
                $callback(...$browsers);
            } catch (Exception $e) {
                if (DuskOptions::hasUI()) $this->breakIntoATinkerShell($browsers, $e);

                throw $e;
            } catch (Throwable $e) {
                if (DuskOptions::hasUI()) $this->breakIntoATinkerShell($browsers, $e);

                throw $e;
            }
        });
    }

    public function breakIntoATinkerShell($browsers, $e)
    {
        $sh = new Shell();

        $sh->add(new DuskCommand($this, $e));

        $sh->setScopeVariables([
            'browsers' => $browsers,
        ]);

        $sh->addInput('dusk');

        $sh->setBoundObject($this);

        $sh->run();

        return $sh->getScopeVariables(false);
    }
}

class User extends AuthUser
{
    use Sushi;

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    protected $rows = [
        [
            'name' => 'First User',
            'email' => 'first@wpstarter-livewire.com',
            'password' => '',
        ],
        [
            'name' => 'Second user',
            'email' => 'second@wpstarter-livewire.com',
            'password' => '',
        ],
    ];
}

class Post extends Model
{
    use Sushi;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $rows = [
        ['title' => 'First', 'user_id' => 1],
        ['title' => 'Second', 'user_id' => 2],
    ];
}

class PostPolicy
{
    public function update(User $user, Post $post)
    {
        return (int) $post->user_id === (int) $user->id;
    }
}
