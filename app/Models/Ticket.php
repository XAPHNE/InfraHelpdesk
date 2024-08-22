<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = ['ticket_number', 'created_by', 'closed_by', 'location', 'subject', 'serial_num', 'description', 'call_type', 'sla_overdue', 'time_taken', 'status', 'remarks', 'isClosedByEmployee', 'closedByEmployee_at', 'closed_at'];

    protected $casts = [
        'sla_overdue' => 'datetime',
        'closedByEmployee_at' => 'datetime',
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

    public function actionTakens()
    {
        return $this->hasMany(ActionTaken::class);
    }

    public function getTimeTakenHumanAttribute()
    {
        if (is_null($this->time_taken)) {
            return 'N/A';
        }

        return $this->convertMinutesToHumanReadable($this->time_taken);
    }

    private function convertMinutesToHumanReadable($minutes)
    {
        $years = floor($minutes / 525600); // 525600 minutes in a year
        $minutes -= $years * 525600;

        $months = floor($minutes / 43200); // 43200 minutes in a 30-day month
        $minutes -= $months * 43200;

        $days = floor($minutes / 1440); // 1440 minutes in a day
        $minutes -= $days * 1440;

        $hours = floor($minutes / 60); // 60 minutes in an hour
        $minutes -= $hours * 60;

        $result = [];
        if ($years > 0) {
            $result[] = "$years year" . ($years > 1 ? "s" : "");
        }
        if ($months > 0) {
            $result[] = "$months month" . ($months > 1 ? "s" : "");
        }
        if ($days > 0) {
            $result[] = "$days day" . ($days > 1 ? "s" : "");
        }
        if ($hours > 0) {
            $result[] = "$hours hour" . ($hours > 1 ? "s" : "");
        }
        if ($minutes > 0 || empty($result)) {
            $result[] = "$minutes minute" . ($minutes > 1 ? "s" : "");
        }

        return implode(" ", $result);
    }
}
