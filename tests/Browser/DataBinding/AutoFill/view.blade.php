<div>
    <div>{{ $email }}</div>
    <div>{{ $password }}</div>

    <form action="{{ ws_url()->current() }}" method="GET">
        <label for="email">Email</label>

        <input name="email" id="email" type="email" autocomplete="on" dusk="email" wire:model.lazy="email">

        <label for="password">Password</label>

        <input name="password" id="password" type="password" autocomplete="on" dusk="password" wire:model="password">

        <button dusk="submit">Submit</button>
    </form>
</div>
