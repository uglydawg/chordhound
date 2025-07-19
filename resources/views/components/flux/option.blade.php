@props(['value' => ''])

<option value="{{ $value }}" {{ $attributes }}>
    {{ $slot }}
</option>