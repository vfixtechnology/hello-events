@props([
    'type' => 'submit',       // button, submit, reset
    'variant' => 'primary',   // Bootstrap variant
    'size' => '',             // sm, lg
    'disabled' => false,
    'icon' => null,           // optional icon class
    'label' => null           // optional label instead of slot
])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "btn btn-{$variant}" . ($size ? " btn-{$size}" : "") . ($disabled ? " disabled" : "")
    ]) }}
    @if($disabled) disabled @endif
>
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif

    {{ $label ?? $slot }}
</button>
