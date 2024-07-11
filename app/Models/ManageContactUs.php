<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageContactUs extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'manage_contact_us';
    protected $guarded = [];
}
