<div>
    @if (ws_session('status'))
        <div class="alert-success mb-" role="alert">
            {{ ws_session('status') }}
        </div>
    @endif
</div>
