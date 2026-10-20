<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['number', 'price', 'status'];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
