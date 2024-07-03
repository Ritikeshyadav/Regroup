<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpaceIamPrincipalLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'space_iam_principal_link';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'space_xid',
        'iam_principal_xid',
        'date',
        'is_active',
    ];
}
