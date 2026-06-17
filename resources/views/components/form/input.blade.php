{{-- resources/views/components/form/input.blade.php --}}
@props([
    'name',
    'id',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autocomplete' => null,
    'error' => null,
    'class' => '',
    'labelHint' => null,   // 👈 small text near label
    'helpText' => null       // 👈 small text below input
])

<div class="form-group">
    @if($label)
        <label class="mb-0" for="{{ $name }}">
            {{ $label }} @if($required) <span class="text-danger">*</span> @endif
            @if($labelHint)
                <small class="text-muted">({{ $labelHint }})</small>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        autocomplete="{{ $autocomplete }}"
        {{ $required ? 'required' : '' }}
        class="form-control {{ $class }} @error($name) is-invalid @enderror"
        {{ $attributes }}
    >

   @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif


    @error($name)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
