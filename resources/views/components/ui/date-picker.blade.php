@props(['name','label'=>null,'value'=>null,'required'=>false,'min'=>null,'max'=>null,'placeholder'=>'dd/mm/aaaa'])
<div>
    @if($label)
    <label for="{{ $name }}" class="form-label fw-medium" style="font-size:.875rem">
        {{ $label }}@if($required)<span class="text-danger ms-1">*</span>@endif
    </label>
    @endif
    <input type="date"
           id="{{ $name }}"
           name="{{ $name }}"
           {{ $attributes->merge(['class'=>'form-control '.($errors->has($name)?'is-invalid':'')]) }}
           value="{{ old($name, $value) }}"
           {{ $required ? 'required' : '' }}
           {{ $min ? 'min='.$min : '' }}
           {{ $max ? 'max='.$max : '' }}>
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
