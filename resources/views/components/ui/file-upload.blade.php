@props(['name','label'=>'Archivo','accept'=>null,'required'=>false,'helpText'=>null,'maxMb'=>10])
<div>
    <label for="{{ $name }}" class="form-label fw-medium" style="font-size:.875rem">
        {{ $label }}@if($required)<span class="text-danger ms-1">*</span>@endif
    </label>
    <input type="file"
           id="{{ $name }}"
           name="{{ $name }}"
           {{ $attributes->merge(['class'=>'form-control '.($errors->has($name)?'is-invalid':'')]) }}
           {{ $accept ? 'accept='.$accept : '' }}
           {{ $required ? 'required' : '' }}>
    @if($helpText)
        <div class="form-text">{{ $helpText }}</div>
    @else
        <div class="form-text">Tamaño máximo: {{ $maxMb }} MB. {{ $accept ? 'Formatos: '.$accept : '' }}</div>
    @endif
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
