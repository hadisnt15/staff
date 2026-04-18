<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'announcement_title',
        'announcement_content',
        'announcement_start_date',
        'announcement_end_date',
    ];
}
