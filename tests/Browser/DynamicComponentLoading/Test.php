<?php

namespace Tests\Browser\DynamicComponentLoading;

use WpStarter\Support\Facades\File;
use Laravel\Dusk\Browser;
use Tests\Browser\TestCase;

class Test extends TestCase
{
    public function test_that_component_loaded_dynamically_via_post_action_causes_no_method_not_allowed()
    {
        File::makeDirectory($this->livewireClassesPath('App'), 0755, true);

        $this->browse(function (Browser $browser) {
            $browser->visit(ws_route('load-dynamic-component', [], false))
                ->waitForText('Step 1 Active')
                ->waitFor('#click_me')
                ->click('#click_me')
                ->waitForText('Test succeeded')
                ->assertSee('Test succeeded');
        });
    }
}
