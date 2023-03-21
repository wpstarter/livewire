<?php

namespace Livewire\Controllers;

use WpStarter\Http\Response;
use Livewire\Livewire;
use WpStarter\Support\Str;
use WpStarter\Pipeline\Pipeline;
use WpStarter\Support\Facades\Request;
use Livewire\Connection\ConnectionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HttpConnectionHandler extends ConnectionHandler
{
    public function __invoke()
    {
        $this->applyPersistentMiddleware();

        return $this->handle(
            ws_request([
                'fingerprint',
                'serverMemo',
                'updates',
            ])
        );
    }

    public function applyPersistentMiddleware()
    {
        try {
            $originalUrl = Livewire::originalUrl();

            // If the original path was the root route, updated the original URL to have
            // a suffix of '/' to ensure that the route matching works correctly when
            // a prefix is used (such as running Laravel in a subdirectory).
            if (Livewire::originalPath() == '/') {
                $originalUrl .= '/';
            }

            $request = $this->makeRequestFromUrlAndMethod(
                $originalUrl,
                Livewire::originalMethod()
            );
        } catch (NotFoundHttpException $e) {

            $originalUrl = Str::replaceFirst('/'.ws_request('fingerprint')['locale'], '', Livewire::originalUrl());

            // If the original path was the root route, updated the original URL to have
            // a suffix of '/' to ensure that the route matching works correctly when
            // a prefix is used (such as running Laravel in a subdirectory).
            if (Livewire::originalPath() == ws_request('fingerprint')['locale']) {
                $originalUrl .= '/';
            }

            $request = $this->makeRequestFromUrlAndMethod(
                $originalUrl,
                Livewire::originalMethod()
            );
        }

        // Gather all the middleware for the original route, and filter it by
        // the ones we have designated for persistence on Livewire requests.
        $originalRouteMiddleware = ws_app('router')->gatherRouteMiddleware($request->route());

        $persistentMiddleware = Livewire::getPersistentMiddleware();

        $filteredMiddleware = ws_collect($originalRouteMiddleware)->filter(function ($middleware) use ($persistentMiddleware) {
            // Some middlewares can be closures.
            if (! is_string($middleware)) return false;

            return in_array(Str::before($middleware, ':'), $persistentMiddleware);
        })->toArray();

        // Now run the faux request through the original middleware with a custom pipeline.
        (new Pipeline(ws_app()))
            ->send($request)
            ->through($filteredMiddleware)
            ->then(function() {
                return new Response();
            });
    }

    protected function makeRequestFromUrlAndMethod($url, $method = 'GET')
    {
        // Ensure the original script paths are passed into the fake request incase Laravel is running in a subdirectory
        $request = Request::create($url, $method, [], [], [], [
            'SCRIPT_NAME' => ws_request()->server->get('SCRIPT_NAME'),
            'SCRIPT_FILENAME' => ws_request()->server->get('SCRIPT_FILENAME'),
            'PHP_SELF' => ws_request()->server->get('PHP_SELF'),
        ]);

        if (ws_request()->hasSession()) {
            $request->setLaravelSession(ws_request()->session());
        }

        $request->setUserResolver(ws_request()->getUserResolver());

        $route = ws_app('router')->getRoutes()->match($request);

        // For some reason without this octane breaks the route parameter binding.
        $route->setContainer(ws_app());

        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        return $request;
    }
}
