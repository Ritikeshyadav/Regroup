<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageGroup extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'manage_groups';
    protected $date = ['delted_at'];
    protected $guards = [];
}
