@extends('layouts.app-for-normal-views')

@section('content')
    <div>
        <h1>Step 1 Active</h1>

        <div id="load_target"></div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            fetch('{{ ws_route("dynamic-component") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ ws_csrf_token() }}'
                }
            })
                .then(res => res.text())
                .then(res => document.getElementById('load_target').innerHTML = res)
                .then(x => window.livewire.rescan());
        });
    </script>
@endpush
