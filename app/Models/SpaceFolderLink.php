<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpaceFolderLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'space_folder_link';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'space_xid',
        'folder_name',
        'is_active',
    ];
}
