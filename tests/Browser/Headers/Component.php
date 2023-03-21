<?php

namespace Tests\Browser\Headers;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    public $output = '';
    public $altoutput = '';

    public function setOutputToFooHeader()
    {
        $this->output = ws_request()->header('x-foo-header', '');
        $this->altoutput = ws_request()->header('x-bazz-header', '');
    }

    public function render()
    {
        return View::file(__DIR__.'/view.blade.php');
    }
}
