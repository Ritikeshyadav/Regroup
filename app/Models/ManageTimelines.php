<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageTimelines extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ='manage_timelines';
    protected $guarded =[];



    
}
