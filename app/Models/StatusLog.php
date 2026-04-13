<?php

namespace App\Models;

use Database\Factories\StatusLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusLog extends Model
{
    /** @use HasFactory<StatusLogFactory> */
    use HasFactory;

    protected $fillable = [
        'complaint_id', 'changed_by', 'old_status', 'new_status', 'remarks',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
