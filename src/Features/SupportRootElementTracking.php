<?php

namespace Livewire\Features;

use Livewire\Livewire;

class SupportRootElementTracking
{
    static function init() { return new static; }

    function __construct()
    {
        Livewire::listen('component.dehydrate.initial', function ($component, $response) {
            if (! $html = ws_data_get($response, 'effects.html')) return;

            ws_data_set($response, 'effects.html', $this->addComponentEndingMarker($html, $component));
        });
    }

    public function addComponentEndingMarker($html, $component)
    {
        return $html."\n<!-- Livewire Component wire-end:".$component->id.' -->';
    }

    public static function stripOutEndingMarker($html)
    {
        return preg_replace('/<!-- Livewire Component wire-end:.*? -->/', '', $html);
    }
}
