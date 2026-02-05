@props(['label', 'placeholder', 'name', 'type', 'classname', 'value'])

<div class="my-1 flex flex-col items-start {{ $classname }}">
    <label for="{{ $label }}" class="block mb-2 pFormActive">{{ $label }}</label>
    @if ($name === 'search')
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <img src={{ secure_asset('/search.svg') }} alt="icon" class="h-6">
            </div>
            <input type="{{ $type }}" placeholder="{{ $placeholder }}" name="{{ $name }}"
                id="{{ $name }}" value="{{ $value }}"
                class="border border-gray-200 pFormActive font-light rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full py-2 pl-10">
        </div>
    @elseif ($type === 'date')
        <div class="relative w-full">
            <div class="absolute inset-y-0 flex items-center left-0 pointer-events-none">
                <img src={{ secure_asset('/calendar.svg') }} alt="icon" class="h-fit mx-4">
            </div>
            <input datepicker datepicker-format="yyyy-mm-dd" type="date" name="{{ $name }}"
                id="{{ $name }}"
                class="border border-gray-200 pFormActive font-light rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10"
                placeholder="Select date" value="{{ $value }}">
        </div>
    @else
        <input type="{{ $type }}" placeholder="{{ $placeholder }}" name="{{ $name }}"
            id="{{ $name }}" value="{{ $value }}" min="0" max="250"
            class="border w-full border-gray-200 pFormActive font-light rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2">
    @endif
</div>
