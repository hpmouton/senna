<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use HasFactory;

   
    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'linked_account_id', 
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
    ];

      public function linkedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'linked_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}