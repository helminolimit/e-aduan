<?php

namespace App\Models;

use Database\Factories\ComplaintFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    /** @use HasFactory<ComplaintFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'aduan_no', 'user_id', 'category_id', 'officer_id',
        'title', 'description', 'location', 'status', 'priority',
    ];

    public function complainant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
