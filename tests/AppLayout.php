<?php

namespace Tests;

use WpStarter\View\Component;

class AppLayout extends Component
{
    public $foo = 'bar';

    public function render()
    {
        return ws_view('layouts.app-from-class-component');
    }
}
