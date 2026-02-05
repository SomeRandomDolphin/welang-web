@props(['message', 'color', 'link', 'classname', 'type', 'icons'])

@if ($link)
    <a href="{{ route($link) }}" type="{{ $type }}"
        class="{{ $color === 'Primary' ? 'buttonActive' : 'buttonInactive' }} {{ $classname }} rounded-md bg-white w-fit py-1 px-6 hover:bg-slate-950">
        {{ $message }}
    </a>
@else
    <button type="{{ $type }}"
        class="{{ $color === 'Primary' ? 'buttonActive' : 'buttonInactive' }} {{ $classname }} rounded-md bg-white w-fit hover:bg-slate-950">
        @if ($icons)
            <img src="{{ $icons }}" alt="icon" class="h-fit">
        @endif
        {{ $message }}
    </button>
@endif
