<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusMaster extends Model
{
    use HasFactory;
    protected $table = 'status_master';
    protected $fillable = [
        'colors_xid',
        'title',
        'is_active',
        'created_by',
        'modified_by',
    ];
}
