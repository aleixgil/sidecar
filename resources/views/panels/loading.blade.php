<div id="{{ $panel->slug() }}" class="flex-1 min-w-sm">
    <div class="flex-1 bg-white m-4 p-4 rounded shadow">
        <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
    </div>
</div>

@push(config('sidecar.scripts-stack'))
    <script>
        $("#{{$panel->slug()}}").load("{{ route('sidecar.panel', get_class($panel)) }}");
    </script>
@endpush