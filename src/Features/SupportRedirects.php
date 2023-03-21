<?php

namespace Livewire\Features;

use Livewire\Livewire;
use Livewire\Redirector;

class SupportRedirects
{
    static function init() { return new static; }

    public static $redirectorCacheStack = [];

    function __construct()
    {
        Livewire::listen('component.hydrate', function ($component, $request) {
            // Put Laravel's redirector aside and replace it with our own custom one.
            static::$redirectorCacheStack[] = ws_app('redirect');

            ws_app()->bind('redirect', function () use ($component) {
                $redirector = ws_app(Redirector::class)->component($component);

                if (ws_app()->has('session.store')) {
                    $redirector->setSession(ws_app('session.store'));
                }

                return $redirector;
            });
        });

        Livewire::listen('component.dehydrate', function ($component, $response) {
            // Put the old redirector back into the container.
            ws_app()->instance('redirect', array_pop(static::$redirectorCacheStack));

            if (empty($component->redirectTo)) {
                return;
            }

            $response->effects['redirect'] = $component->redirectTo;
        });

        Livewire::listen('component.dehydrate.subsequent', function ($component, $response) {
            // If there was no redirect. Clear flash session data.
            if (empty($component->redirectTo) && ws_app()->has('session.store')) {
                ws_session()->forget(ws_session()->get('_flash.new'));

                return;
            }
        });

        Livewire::listen('flush-state', function() {
            static::$redirectorCacheStack = [];
        });
    }
}
