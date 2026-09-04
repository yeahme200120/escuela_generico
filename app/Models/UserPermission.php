<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermission extends Model
{
    protected $table = 'user_permissions';

    protected $fillable = [
        'user_id', 'permission_id', 'sede_id', 'alcance', 'concedido',
    ];

    protected $casts = ['concedido' => 'boolean'];

    public function user(): BelongsTo       { return $this->belongsTo(User::class);       }
    public function permission(): BelongsTo { return $this->belongsTo(Permission::class); }
    public function sede(): BelongsTo       { return $this->belongsTo(Sede::class);        }
}
