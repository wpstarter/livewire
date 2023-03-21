<?php

namespace Livewire\HydrationMiddleware;

use WpStarter\Support\Arr;

class HashDataPropertiesForDirtyDetection implements HydrationMiddleware
{
    protected static $propertyHashesByComponentId = [];

    public static function hydrate($instance, $request)
    {
        $data = ws_data_get($request, 'memo.data', []);

        ws_collect($data)->each(function ($value, $key) use ($instance) {
            if (is_array($value)) {
                foreach (Arr::dot($value, $key.'.') as $dottedKey => $value) {
                    static::rehashProperty($dottedKey, $value, $instance);
                }
            } else {
                static::rehashProperty($key, $value, $instance);
            }
        });
    }

    public static function dehydrate($instance, $response)
    {
        $data = ws_data_get($response, 'memo.data', []);

        $dirtyProps = ws_collect(static::$propertyHashesByComponentId[$instance->id] ?? [])
            ->filter(function ($hash, $key) use ($data) {
                // Only return the propertyHashes/props that have changed.
                return static::hash(ws_data_get($data, $key)) !== $hash;
            })
            ->keys()
            ->toArray();

        ws_data_set($response, 'effects.dirty', $dirtyProps);
    }

    public static function rehashProperty($name, $value, $component)
    {
        static::$propertyHashesByComponentId[$component->id][$name] = static::hash($value);
    }

    public static function hash($value)
    {
        if (! is_null($value) && ! is_string($value) && ! is_numeric($value) && ! is_bool($value)) {
            if (is_array($value)) {
                return json_encode($value);
            }
            $value = method_exists($value, '__toString')
                ? (string) $value
                : json_encode($value);
        }

        // Using crc32 because it's fast, and this doesn't have to be secure.
        return crc32($value ?? '');
    }
}
