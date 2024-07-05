<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalManageAcivitiesLink extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "iam_principal_manage_acivities_links";
    protected $guarded = [];
}
