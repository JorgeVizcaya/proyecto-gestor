<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacentarea extends Model
{
    protected $table = 'almacentarea';
    protected $fillable =['titulo', 'descripcion', 'estado','prioridad','fecha_limite','completada_el'];
}
