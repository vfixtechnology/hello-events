@props([
    'name',
    'options' => [],
    'selected' => []
])

@php
    // Ensure $selected is an array for easy checking, even if it's a Collection
    $selected = is_array($selected) ? $selected : $selected->toArray();
@endphp

<div class="mb-3" wire:ignore>
    <label for="{{ $name }}">{{ $slot }}</label>
    <select name="{{ $name }}[]" id="{{ $name }}" class="form-control" multiple>
        @foreach($options as $key => $value)
            <option value="{{ $key }}" @if(in_array($key, old($name, $selected))) selected @endif>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#{{ $name }}').select2({
            placeholder: 'Select options...'
        });
    });
</script>
@endpush
