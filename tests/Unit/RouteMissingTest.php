<?php

namespace Tests\Unit;

use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Eloquent\ModelNotFoundException;
use WpStarter\Http\Request;
use Livewire\Component;
use WpStarter\Support\Facades\Route;

class RouteMissingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped('Only applies to PHP 7.4 and above.');
        }

        if (! method_exists(\WpStarter\Routing\Route::class, 'missing')) {
            $this->markTestSkipped('Need Laravel >= 8');
        }
    }

    /** @test */
    public function route_supports_laravels_missing_fallback_function(): void
    {
        $class = <<<'PHP'
namespace Tests\Unit;
use Livewire\Component;
class ComponentWithModel extends Component
{
    public FrameworkModel $framework;
}
PHP;
        eval($class);

        Route::get('awesome-js/{framework}', ComponentWithModel::class)
             ->missing(function (Request $request) {
                 $this->assertEquals(ws_request(), $request);
                 return ws_redirect()->to('awesome-js/alpine');
             });

        $this->get('/awesome-js/jquery')->assertRedirect('/awesome-js/alpine');
    }
}

class FrameworkModel extends Model
{
    public function resolveRouteBinding($value, $field = null)
    {
        throw new ModelNotFoundException;
    }
}
