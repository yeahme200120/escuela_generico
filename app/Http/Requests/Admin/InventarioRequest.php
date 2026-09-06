<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InventarioRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('inventario.gestionar'); }

    public function rules(): array
    {
        return [
            'sede_id'         => ['required','exists:sedes,id'],
            'categoria_id'    => ['nullable','exists:categorias_inventario,id'],
            'nombre'          => ['required','string','max:200'],
            'codigo'          => ['nullable','string','max:50'],
            'descripcion'     => ['nullable','string','max:400'],
            'unidad_medida'   => ['nullable','string','max:30'],
            'stock_actual'    => ['integer','min:0'],
            'stock_minimo'    => ['integer','min:0'],
            'precio_unitario' => ['nullable','numeric','min:0'],
        ];
    }
}
