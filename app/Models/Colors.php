<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colors extends Model
{
    use HasFactory;
    protected $table = 'colors';
    protected $fillable = [
        'name',
        'image',
        'is_active',
        'created_by',
        'modified_by',
    ];
}
