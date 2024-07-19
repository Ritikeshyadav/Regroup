<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalCertifications extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_certifications';

    protected $guarded = [];

    
}
