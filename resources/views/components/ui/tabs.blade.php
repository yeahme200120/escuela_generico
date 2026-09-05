@props(['id' => 'tabs-'.Str::random(4), 'items' => [], 'pills' => false])
@php $nav = $pills ? 'nav-pills' : 'nav-tabs'; @endphp
<div>
    <ul class="nav {{ $nav }} mb-3" id="{{ $id }}" role="tablist">
        @foreach($items as $i => $tab)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $i===0?'active':'' }}"
                id="{{ $id }}-tab-{{ $i }}"
                data-bs-toggle="{{ $pills?'pill':'tab' }}"
                data-bs-target="#{{ $id }}-pane-{{ $i }}"
                type="button" role="tab">{{ $tab['label'] ?? "Tab $i" }}</button>
        </li>
        @endforeach
    </ul>
    <div class="tab-content" id="{{ $id }}Content">
        @foreach($items as $i => $tab)
        <div class="tab-pane fade {{ $i===0?'show active':'' }}"
             id="{{ $id }}-pane-{{ $i }}" role="tabpanel">
            {!! $tab['content'] ?? '' !!}
        </div>
        @endforeach
        {{ $slot }}
    </div>
</div>
