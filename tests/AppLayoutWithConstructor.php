<?php

namespace Tests;

use WpStarter\View\Component;

class AppLayoutWithConstructor extends Component
{
    public $foo;

    public function __construct($foo = 'bar')
    {
        $this->foo = $foo;
    }

    public function render()
    {
        return ws_view('layouts.app-from-class-component');
    }
}
