<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessTypes extends Model
{
    use HasFactory;



    use SoftDeletes;

    protected $table = 'business_types';
    protected $dates = ['deleted_at'];

    protected $guarded =
        [];

}
