<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InviteCard extends Model
{
    protected $fillable = [
        'event_id',
        'template_style',
        'show_agenda',
        'show_venue',
        'show_qr',
        'custom_message',
        'file_path',
    ];

    protected $casts = [
        'show_agenda' => 'boolean',
        'show_venue'  => 'boolean',
        'show_qr'     => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
