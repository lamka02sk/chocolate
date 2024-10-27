<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'title',
        'partner',
        'type',
        'notes',
        'tags',
        'attachments',
        'attachments_file_names',
        'date_issue',
        'date_paid',
        'user_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(static function ($document) {
            $document->user_id = Auth::id();
        });
    }

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'attachments' => 'array',
            'attachments_file_names' => 'array',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
