<?php

namespace Tests\Browser\Nesting;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;

class NestedComponent extends BaseComponent
{
    public $output = '';

    public function render()
    {
        return View::file(__DIR__.'/view-nested.blade.php');
    }
}
