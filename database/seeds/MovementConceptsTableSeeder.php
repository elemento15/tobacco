<?php

use Illuminate\Database\Seeder;
use App\MovementConcept;

class MovementConceptsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $concepts = [
            ['name' => 'INVENTARIO INICIAL',        'type' => 'E',  'is_automatic' => 0],
            ['name' => 'ENTRADA POR AJUSTE',        'type' => 'E',  'is_automatic' => 0],
            ['name' => 'SALIDA POR AJUSTE',         'type' => 'S',  'is_automatic' => 0],
            ['name' => 'ENTRADA DE MERCANCIA',      'type' => 'E',  'is_automatic' => 0],
            ['name' => 'TRASPASO ENTRE ALMACENES',  'type' => 'T',  'is_automatic' => 0],
            ['name' => 'RECEPCION DE TRASPASO',     'type' => 'E',  'is_automatic' => 1,  'code' => 'REC-TRASP'],
            ['name' => 'SALIDA POR ENTREGA',        'type' => 'S',  'is_automatic' => 1,  'code' => 'ENTREGA'],
            ['name' => 'DEVOLUCION DE ENTREGA',     'type' => 'E',  'is_automatic' => 1,  'code' => 'DEVOLUC'],
        ];


        if (! MovementConcept::count()) {
            foreach ($concepts as $key => $item) {
                MovementConcept::create($item);
            }
        }
    }
}
