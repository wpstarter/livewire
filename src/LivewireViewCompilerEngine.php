<?php

namespace Livewire;

use WpStarter\View\Engines\CompilerEngine;
use Livewire\ComponentConcerns\RendersLivewireComponents;

class LivewireViewCompilerEngine extends CompilerEngine
{
    use RendersLivewireComponents;
}
