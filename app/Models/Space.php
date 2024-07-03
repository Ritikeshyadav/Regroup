<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Space extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'space';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'icon_xid',
        'color_xid',
        'name',
        'description',
        'is_private',
    ];
}
