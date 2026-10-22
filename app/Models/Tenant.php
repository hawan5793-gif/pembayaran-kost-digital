<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['room_id', 'price', 'name', 'phone', 'entry_date', 'due_date', 'unpaid_months', 'status', 'last_billed_at'];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'due_date' => 'date',
            'last_billed_at' => 'date',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
