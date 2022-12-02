@if(count($report->getWidgets()) > 0 && (!$graph || !$graph->doesApply()) && !$compare->isComparing())

    <div id="sidecar-widgets"  style="height:140px">
        <div class="m-4 p-4 flex justify-center text-gray-400">
            <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
        </div>
    </div>
    @push('edit-scripts')
        <script>
            SidecarHtmlLoader.load("{{route('sidecar.report.widgets', $model)}}?{!! request()->getQueryString() !!}", 'sidecar-widgets')
        </script>
    @endpush
@endif