<?php

namespace Livewire\Features;

use WpStarter\Database\Eloquent\Collection as EloquentCollection;
use WpStarter\Support\Collection;
use Livewire\Livewire;

class SupportCollections
{
    static function init() { return new static; }

    function __construct()
    {
        Livewire::listen('property.dehydrate', function ($name, $value, $component, $response) {
            if (! $value instanceof Collection || $value instanceof EloquentCollection) return;


        });

        Livewire::listen('property.hydrate', function ($name, $value, $component, $request) {
            $collections = ws_data_get($request->memo, 'dataMeta.collections', []);

            foreach ($collections as $name) {
                ws_data_set($component, $name, ws_collect(ws_data_get($component, $name)));
            }
        });
    }
}
