<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'guard_name'
    ];

    public function users()
    {
        return $this->morphedByMany(
            User::class,
            'model',
            'model_has_roles'
        );
    }
}