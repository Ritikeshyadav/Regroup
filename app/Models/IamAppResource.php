<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamAppResource extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_app_resource';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'app_resource_title',
        'is_active'
    ];
}
