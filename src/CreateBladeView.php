<?php

namespace Livewire;

use WpStarter\View\Component;

class CreateBladeView extends Component
{
    public static function fromString($contents)
    {
        return (new static)->createBladeViewFromString(ws_app('view'), $contents);
    }

    public function render() {}
}
