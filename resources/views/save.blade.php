<div class="float-right mt-4 mr-2" x-data="{open:false}">
    <a class="button secondary relative" x-on:click="open=!open">
        <i class="fa fa-clone" aria-hidden="true"></i>
        {{ __(config('sidecar.translationsPrefix').'save') }}
    </a>

    <div class="dropdown m-4 p-4 right-0" x-on:click.away="open=!open" x-show="open" x-transition x-cloak>
        <div class="mb-4">
        {{ __(config('sidecar.translationsPrefix').'saveReportTitle') }}
        </div>
        <form action="{{ route('sidecar.report.store') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="url" value="{{ request()->fullUrl() }}">
            <input name="name" placeholder="{{ __(config('sidecar.translationsPrefix').'myReport') }}" required>
            <br>
            <div class="mb-2 text-gray-400 text-sm mt-1">
                {{ __(config('sidecar.translationsPrefix').'saveReportDesc') }}
            </div>
            <button class="button primary mt-2">{{ __(config('sidecar.translationsPrefix').'save') }}</button>
        </form>
    </div>
</div>