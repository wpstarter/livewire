<?php

namespace Tests\Browser\Redirects;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;
use Livewire\Livewire;

class Component extends BaseComponent
{
    public $message = 'foo';
    public $foo;

    public $disableBackButtonCache = true;

    protected $queryString = [
        'disableBackButtonCache',
    ];

    protected $rules = [
        'foo.name' => '',
    ];

    public function mount()
    {
        $this->foo = Foo::first();

        // Set "disable back button cache" flag based off of query string
        $this->disableBackButtonCache ? Livewire::disableBackButtonCache() : Livewire::enableBackButtonCache();
    }

    public function flashMessage()
    {
        ws_session()->flash('message', 'some-message');
    }

    public function redirectWithFlash()
    {
        ws_session()->flash('message', 'some-message');

        return $this->redirect('/livewire-dusk/Tests%5CBrowser%5CRedirects%5CComponent');
    }

    public function redirectPage()
    {
        $this->message = 'bar';

        return $this->redirect('/livewire-dusk/Tests%5CBrowser%5CRedirects%5CComponent?abc');
    }

    public function redirectPageWithModel()
    {
        $this->foo->update(['name' => 'bar']);

        return $this->redirect('/livewire-dusk/Tests%5CBrowser%5CRedirects%5CComponent?abc&disableBackButtonCache='. ($this->disableBackButtonCache ? 'true' : 'false'));
    }

    public function render()
    {
        return View::file(__DIR__.'/view.blade.php');
    }
}
