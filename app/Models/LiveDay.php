<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveDay extends Model
{
    use HasFactory;

    protected $table = 'live_days'; 
    protected $primaryKey = 'live_date'; 
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = ['live_date', 'gmv_target', 'day_type'];
}
