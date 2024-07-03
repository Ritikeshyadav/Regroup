<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "teams";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'iam_principal_xid',
        'name',
        'is_active',
    ];
}
