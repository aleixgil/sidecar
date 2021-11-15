<table class="{{$tableClasses}}">
        <thead class="sticky">
        <tr>
        @foreach($fields as $field)
            <th class="{{ $field->getTDClasses() }}">
                @if ($field->sortable)
                    <div class='sortableHeader '>{{ $field->getTitle() }}
                        <div class='sortArrows'>

                            <a href='{{ \Revo\Sidecar\Filters\Sort::queryUrlFor($field, 'ASC') }}' class='sortUp'>▲</a>
                            <a href='{{ \Revo\Sidecar\Filters\Sort::queryUrlFor($field, 'DESC') }}' class='sortDown'>▼</a>
                        </div>
                    </div>
                @else
                    {{ $field->getTitle() }}
                @endif
            </th>
        @endforeach
        </tr></thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
            @foreach($fields as $field)
                <td class="{{ $field->getTDClasses() }}">
                    {{ $field->getValue($row) }}
                </td>
            @endforeach
            </tr>
        @endforeach
        </tbody>
</table>