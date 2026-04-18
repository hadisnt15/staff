<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'vdates';

    public $timestamps = false;

    protected $guarded = [];
}
