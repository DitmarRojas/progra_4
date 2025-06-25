<?php

namespace App\Livewire;

use App\Models\CuentasOrgs;
use App\Models\Organizacion;
use Livewire\Component;

class CuentasPorOrg extends Component
{
    public $organizacion_id;
    public $cuentas_selecionadas = [];

    public function asociarCuentas():void
    {
        $this->validate([
            'organizacion_id' => 'required|exists:organizaciones,id',
            'cuentas_selecionadas' => 'required|array',
            'cuentas_selecionadas.*' => 'exists:cuentas,id',
        ]);

        foreach($this->cuentas_selecionadas as $cuenta_id)
        {
            CuentasOrgs::create([
                'organizacion_id' => $this->organizacion_id,
                'cuenta_id' => $cuenta_id,
            ]);
        }

        $this->dispatch('alertaCuentaOrg');
        session()->flash('message', 'Cuenta asociada a la organización exitosamente.');
    }

    public function vaciarFormulario(): void
    {
        $this->reset([
            'organizacion_id',
            'cuenta_id',
        ]);
    }

    public function render()
    {
        $organizaciones = Organizacion::all();
        $cuentas = CuentasOrgs::all();
        return view('livewire.cuentas-por-org',[
            'organizaciones' => $organizaciones,
            'cuentas' => $cuentas,
        ]);
    }
}
