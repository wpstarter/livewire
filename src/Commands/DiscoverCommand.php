<?php

namespace Livewire\Commands;

use WpStarter\Console\Command;
use Livewire\LivewireComponentsFinder;

class DiscoverCommand extends Command
{
    protected $signature = 'livewire:discover';

    protected $description = 'Regenerate Livewire component auto-discovery manifest';

    public function handle()
    {
        ws_app(LivewireComponentsFinder::class)->build();

        $this->info('Livewire auto-discovery manifest rebuilt!');
    }
}
