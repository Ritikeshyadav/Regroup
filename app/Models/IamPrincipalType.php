<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_principal_type';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'principal_type_title',
        'is_active'
    ];
}
