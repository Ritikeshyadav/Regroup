<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalSource extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_principal_source';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'principal_source_title',
        'is_active'
    ];
}
