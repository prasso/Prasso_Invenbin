<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailsQueue extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'email_sent',
        'text_of_email',
        'date_sent',
        'date_queued',
    ];
}