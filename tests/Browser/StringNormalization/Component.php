<?php

namespace Tests\Browser\StringNormalization;

use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    public $string = 'â';
    public $number = 0;
    public $array = ['â'];
    public $recursiveArray = ['â', ['â']];
    public $collection;
    public $recursiveCollection;
    public $model;
    public $modelCollection;

    public function mount()
    {
        $this->collection = ws_collect(['â']);
        $this->recursiveCollection = ws_collect(['â', ['â']]);
        $this->model = Model::find(1);
        $this->modelCollection = Model::all();
    }

    public function render()
    {
        return View::file(__DIR__.'/view.blade.php');
    }

    public function addNumber()
    {
        $this->number++;
    }
}
