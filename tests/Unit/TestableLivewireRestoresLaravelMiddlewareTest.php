<?php

namespace Tests\Unit;

use WpStarter\Support\Facades\Route;
use Livewire\Component;
use Livewire\Livewire;

class TestableLivewireRestoresLaravelMiddlewareTest extends TestCase
{
    /** @test */
    public function it_restores_laravel_middleware_after_livewire_test()
    {
        // Run a basic Livewire test first to ensure Livewire has disabled
        // trim strings and convert empty strings to null middleware
        Livewire::test(BasicComponent::class)
            ->set('name', 'test')
            ->assertSet('name', 'test');

        // Then make a standard wpstarter test and ensure that the input has
        // had trim strings re-applied
        Route::post('wpstarter', function() {
            return 'wpstarter' . ws_request()->input('name') . 'wpstarter';
        });

        $this->post('wpstarter', ['name' => '    aaa    '])
        ->assertSee('laravelaaalaravel');
    }
}

class BasicComponent extends Component
{
    public $name;

    public function render()
    {
        return ws_view('null-view');
    }
}
