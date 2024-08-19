<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = ['ticket_number', 'created_by', 'closed_by', 'location', 'subject', 'serial_num', 'description', 'call_type', 'sla_overdue', 'status', 'remarks', 'closed_at'];

    protected $casts = [
        'sla_overdue' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Relationship with User as the creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with User as the closer
    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function actionsTaken()
    {
        return $this->hasMany(ActionTaken::class);
    }
}
