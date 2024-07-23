<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LikeIcons extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'like_icons';
    protected $guarded = [];
}
