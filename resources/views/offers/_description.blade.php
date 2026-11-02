@php
    $lines = preg_split('/\R/u', trim((string) $description)) ?: [];
    $inList = false;
@endphp

<div class="offer-description">
    @foreach($lines as $line)
        @php
            $line = trim($line);
        @endphp

        @if($line === '')
            @continue
        @endif

        @if(str_starts_with($line, '- '))
            @if(! $inList)
                <ul class="offer-description-list">
                @php $inList = true; @endphp
            @endif
            <li>{{ ltrim(substr($line, 2)) }}</li>
        @else
            @if($inList)
                </ul>
                @php $inList = false; @endphp
            @endif

            @if(str_starts_with($line, '## '))
                <h3 class="offer-description-heading">{{ ltrim(substr($line, 3)) }}</h3>
            @else
                <p>{{ $line }}</p>
            @endif
        @endif
    @endforeach

    @if($inList)
        </ul>
    @endif
</div>
