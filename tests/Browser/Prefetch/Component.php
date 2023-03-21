<?php

namespace Tests\Browser\Prefetch;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    public function render()
    {
        ws_app('session')->put('count', ws_app('session')->get('count') + 1);

        return View::file(__DIR__.'/view.blade.php');
    }
}
