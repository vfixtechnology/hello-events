@props([
    'name',
    'label' => '',
    'checked' => false,
    'value' => 1,
    'helpText' => '',
])

<div class="flex items-start mb-3">
    <div class="custom-control custom-switch mr-3">
        {{-- This hidden input ensures a value of 0 is sent when the box is unchecked --}}
        <input type="hidden" name="{{ $name }}" value="0">

        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            class="custom-control-input"
            @if(old($name, $checked)) checked @endif
            {{ $attributes }}
        >

        @if($label)
            <label class="custom-control-label" for="{{ $name }}">
                {{ $label }}
            </label>
        @endif
    </div>

    <div class="flex-1">
        @if($helpText)
            <small class="text-muted">{{ $helpText }}</small>
        @endif
    </div>
</div>
