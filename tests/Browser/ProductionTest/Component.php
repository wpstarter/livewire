<?php

namespace Tests\Browser\ProductionTest;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    public $foo = 'squishy';

    public function mount()
    {
        ws_config()->set('app.debug', false);
    }

    public function render()
    {
        return <<< 'HTML'
<div>
    <input type="text" wire:model="foo" dusk="foo">
</div>
HTML;
    }
}
