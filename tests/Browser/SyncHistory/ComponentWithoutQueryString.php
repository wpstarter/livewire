<?php

namespace Tests\Browser\SyncHistory;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;

class ComponentWithoutQueryString extends BaseComponent
{
    public $step;

    public function mount(Step $step)
    {
        $this->step = $step;
    }

    public function setStep($id)
    {
        $this->step = Step::findOrFail($id);
    }

    public function render()
    {
        return View::file(__DIR__.'/view-without-subcomponent.blade.php')->with([ 'id' => $this->id]);
    }
}
