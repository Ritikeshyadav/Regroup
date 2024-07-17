<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abilities extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ='abilities';
    protected $guarded = [];
}
