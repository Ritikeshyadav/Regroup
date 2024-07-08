<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageInterest extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'manage_interests';
    protected $dates = ['deleted_at'];
    protected $fillable = ['name','image'];
}
