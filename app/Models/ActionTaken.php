<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionTaken extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['ticket_id', 'action_taken'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
