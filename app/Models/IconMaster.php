<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IconMaster extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'icon_master';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'image',
        'is_active',
    ];
}
