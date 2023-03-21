<?php

namespace Livewire\ComponentConcerns;

trait PerformsRedirects
{
    public $redirectTo;

    public function redirect($url)
    {
        $this->redirectTo = $url;

        $this->shouldSkipRender = $this->shouldSkipRender ?? ! ws_config('livewire.render_on_redirect', false);
    }

    public function redirectRoute($name, $parameters = [], $absolute = true)
    {
        $this->redirectTo = ws_route($name, $parameters, $absolute);

        $this->shouldSkipRender = $this->shouldSkipRender ?? ! ws_config('livewire.render_on_redirect', false);
    }

    public function redirectAction($name, $parameters = [], $absolute = true)
    {
        $this->redirectTo = ws_action($name, $parameters, $absolute);

        $this->shouldSkipRender = $this->shouldSkipRender ?? ! ws_config('livewire.render_on_redirect', false);
    }
}
