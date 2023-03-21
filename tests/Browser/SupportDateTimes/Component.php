<?php

namespace Tests\Browser\SupportDateTimes;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    public $native;
    public $nativeImmutable;
    public $carbon;
    public $carbonImmutable;
    public $wpstarter;

    public function mount()
    {
        $this->native = new \DateTime('01/01/2001');
        $this->nativeImmutable = new \DateTimeImmutable('01/01/2001');
        $this->carbon = \Carbon\Carbon::parse('01/01/2001');
        $this->carbonImmutable = \Carbon\CarbonImmutable::parse('01/01/2001');
        $this->wpstarter = \WpStarter\Support\Carbon::parse('01/01/2001');
    }

    public function addDay()
    {
        $this->native->modify('+1 day');
        $this->nativeImmutable = $this->nativeImmutable->modify('+1 day');
        $this->carbon->addDay(1);
        $this->carbonImmutable = $this->carbonImmutable->addDay(1);
        $this->wpstarter->addDay(1);
    }

    public function render()
    {
        return View::file(__DIR__.'/view.blade.php');
    }
}
