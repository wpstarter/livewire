<?php

namespace Tests\Unit;

use WpStarter\Support\Facades\Blade;
use Livewire\Livewire;
use Livewire\Component;
use Livewire\LivewireBladeDirectives;

class LivewireJsDirectiveTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Register the @js directive to force replacement of the directive introduced in Laravel 8.71.0
        Blade::directive('js', [LivewireBladeDirectives::class, 'js']);
    }

    /** @test */
    public function single_quotes()
    {
        Livewire::test(ComponentForTestingJsDirective::class, [
            'string' => "@js('hey')",
        ])
            ->assertSee("'hey'", false);
    }

    /** @test */
    public function double_quotes_containing_single_quotes()
    {
        Livewire::test(ComponentForTestingJsDirective::class, [
            'string' => "@js(\"hey 'there'\")",
        ])
            ->assertSee("'hey \'there\''", false);
    }

    /** @test */
    public function double_quotes_turns_into_single_quotes()
    {
        Livewire::test(ComponentForTestingJsDirective::class, [
            'string' => '@js("hey")',
        ])
            ->assertSee("'hey'", false);
    }

    /** @test */
    public function objects_dont_contain_double_quotes()
    {
        Livewire::test(ComponentForTestingJsDirective::class, [
            'string' => '@js($data)',
            'data' => ['hey' => 'there'],
        ])
            ->assertSee("JSON.parse(atob('eyJoZXkiOiJ0aGVyZSJ9'))", false);
    }

    /** @test */
    public function arrays_dont_contain_double_quotes()
    {
        Livewire::test(ComponentForTestingJsDirective::class, [
            'string' => '@js($data)',
            'data' => ['hey', 'there'],
        ])
            ->assertSee("JSON.parse(atob('WyJoZXkiLCJ0aGVyZSJd'))", false);
    }
}

class ComponentForTestingJsDirective extends Component
{
    public $expression = '';

    public $data = [];

    public function mount($string, $data = null)
    {
        $this->expression = $string;
        $this->data = $data;
    }

    public function render()
    {
        return '<div>'.$this->expression.'</div>';
    }
}
