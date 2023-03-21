<?php

namespace Tests\Browser\DataBinding\Defer;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    public $foo = '';
    public $bar = [];

    public function render()
    {
        return View::file(__DIR__.'/view.blade.php');
    }
}
