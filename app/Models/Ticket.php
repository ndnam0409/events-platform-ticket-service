<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $primaryKey = 'ticketID';

    protected $fillable = [
        'location',
        'area',
        'seat',
        'price',
        'isSold',
        'eventID',
        'userID',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'eventID', 'eventID');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }
}
