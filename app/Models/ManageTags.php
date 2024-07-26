<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageTags extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'manage_tags';

    protected $guarded = [];

    public function community()
    {
        return $this->belongsTo(ManageCommunity::class,'manage_community_xid');
    }
}
