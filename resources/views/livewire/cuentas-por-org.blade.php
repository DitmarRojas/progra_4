<div>
    <flux:select wire:model="organizacion_id">
        @foreach($organizaciones as $org)
            <flux:select.option value="{{ $org->id }}">{{ $org->nombre }}</flux:select.option>
        @endforeach
    </flux:select>

@foreach($cuentas as $cuenta)
    <flux:checkbox.group wire:model="cuentas_seleccionadas" label="Cuentas">
        <flux:checkbox label="{{$cuenta->nombre}}" value="{{$cuenta->id}}" checked />
    </flux:checkbox.group>
@endforeach

<flux:button wire:click="asociarCuentas">Asociar seleccionadas</flux:button>
</div>
