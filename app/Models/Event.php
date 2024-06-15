<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'eventID';

    protected $fillable = [
        'eventName',
        'startDate',
        'endDate',
        'location',
        'capacity',
        'eventType',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'eventID', 'eventID');
    }
}
