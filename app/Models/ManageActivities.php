<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageActivities extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "manage_activities";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'iam_principal_xid',
        'space_folder_list_task_link_xid',
        'title',
        'date_time',
        'is_active',
    ];
}
