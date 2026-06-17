@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
])

<div class="form-group">
    @if($label)
        <label class="mb-0" for="{{ $name }}">{{ $label }} @if($required) * @endif</label>
    @endif

    <select name="{{ $name }}" id="{{ $name }}" class="form-control @error($name) is-invalid @enderror" {{ $required ? 'required' : '' }} {{ $attributes }}>

        <option value="">-- Please select --</option>

        @foreach($options as $key => $value)
            <option value="{{ $key }}"
                {{-- This simple check works perfectly for single selects --}}
                @if(old($name, $selected) == $key) selected @endif>
                {{ $value }}
            </option>
        @endforeach
    </select>

    @error($name)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
