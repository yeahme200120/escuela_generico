<table {{ $attributes->merge(['class' => 'table table-striped table-hover']) }}>
    @if(isset($thead))
        <thead>
            {{ $thead }}
        </thead>
    @endif
    <tbody>
        {{ $slot }}
    </tbody>
</table>
