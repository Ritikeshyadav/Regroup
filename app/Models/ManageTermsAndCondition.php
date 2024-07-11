<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageTermsAndCondition extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ='manage_terms_and_conditions';
    protected $guarded =[];
}
