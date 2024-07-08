<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageCommunityType extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ='manage_community_types';

    protected $fillable = ['name','image','description','is_active','created_by','modified_by'];
}
