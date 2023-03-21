<div>
    <button wire:click.prefetch="$refresh" dusk="button">inc</button>

    <span dusk="count">{{ ws_app('session')->get('count') }}</span>
</div>
