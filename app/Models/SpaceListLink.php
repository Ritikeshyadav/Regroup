<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpaceListLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'space_list_link';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'space_xid',
        'color_xid',
        'priority_xid',
        'list_name',
        'description',
        'start_date',
        'end_date',
        'is_active',
    ];
}
