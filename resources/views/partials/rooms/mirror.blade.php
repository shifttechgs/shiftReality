{{-- Stranger in the Mirror — three panes reflect your cursor, and each one disobeys:
     one mirrors you, one lags behind, one moves upside-down. --}}
<div class="mirror" data-mirror role="img" aria-label="Three mirrors whose reflections move differently from you">
    @foreach (['mirrored' => 'Mirrors you', 'delayed' => 'Lags behind', 'inverted' => 'Upside down'] as $mode => $label)
        <div class="mirror__pane" data-mirror-pane="{{ $mode }}">
            <div class="mirror__figure" data-mirror-figure>
                <span class="mirror__head"></span>
                <span class="mirror__body"></span>
            </div>
            <span class="mirror__label">{{ $label }}</span>
        </div>
    @endforeach
</div>
