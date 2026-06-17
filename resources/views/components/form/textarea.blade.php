@props([
    'name',
    'id' => null, // Change this from empty string to null
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'rows' => 3
])

<div class="mb-3">
    <label class="mb-0" for="{{ $id ?: $name }}">{{ $label ?: $slot }}</label>
    <textarea
        name="{{ $name }}"
        id="{{ $id ?: $name }}" {{-- Use id if provided, otherwise fallback to name --}}
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        class="form-control @error($name) is-invalid @enderror"
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
