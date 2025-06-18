<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $table = "tasks";
    protected $fillable = [
        "title",
        "description",
        "status",
        "priority",
        "order",
        "user_id"
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
