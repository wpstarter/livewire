<?php

namespace Tests\Browser\Pagination;

use Tests\Browser\Pagination\Post;
use WpStarter\Support\Facades\View;
use Livewire\Component as BaseComponent;
use Livewire\WithPagination;

class Bootstrap extends BaseComponent
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return View::file(__DIR__.'/view.blade.php', [
            'posts' => Post::paginate(3),
        ]);
    }
}
