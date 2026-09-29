<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    protected $fillable = [
        'clave',
        'valor',
    ];

    public static function obtener(string $clave, ?string $defecto = null): ?string
    {
        $ajuste = self::where('clave', $clave)->first();

        return $ajuste->valor ?? $defecto;
    }

    public static function guardar(string $clave, ?string $valor): void
    {
        self::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }
}
