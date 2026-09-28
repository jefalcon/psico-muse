<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaccion extends Model
{
    protected $table = 'interacciones';

    public const CANAL_EMAIL = 'email';

    public const CANAL_TELEFONO = 'teléfono';

    public const CANAL_WHATSAPP = 'whatsapp';

    public const CANAL_PRESENCIAL = 'presencial';

    public const CANAL_WEB = 'web';

    protected $fillable = [
        'lead_id',
        'fecha',
        'canal',
        'nota',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
        ];
    }

    /** @return BelongsTo<Lead, $this> */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public static function canales(): array
    {
        return [
            self::CANAL_TELEFONO => 'Teléfono',
            self::CANAL_EMAIL => 'Email',
            self::CANAL_WHATSAPP => 'WhatsApp',
            self::CANAL_PRESENCIAL => 'Presencial',
            self::CANAL_WEB => 'Web',
        ];
    }
}
