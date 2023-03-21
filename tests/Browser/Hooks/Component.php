<?php

namespace Tests\Browser\Hooks;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    public $foo = false;

    public function showFoo()
    {
        $this->foo = true;
    }

    public function render()
    {
        return View::file(__DIR__.'/view.blade.php');
    }
}
