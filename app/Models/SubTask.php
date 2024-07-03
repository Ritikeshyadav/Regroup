<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubTask extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sub_tasks';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'space_folder_list_task_link_xid',
        'iam_principal_xid',
        'name',
        'description',
        'start_date',
        'due_date',
        'priority_master_xid',
        'comment',
        'status_master_xid',
        'is_group_assignee',
        'is_active',
    ];

    public function statusmaster()
    {
        return $this->hasOne(StatusMaster::class, 'id', 'status_master_xid');
    }

    public function priority()
    {
        return $this->hasOne(PriorityMaster::class, 'id', 'priority_master_xid');
    }

    public function delete_space_folder_list_task_link()
    {
        return $this->hasOne(SpaceFolderListTaskLink::class, 'id', 'space_folder_list_task_link_xid');
    }

    public function delete_iam_principal()
    {
        return $this->hasOne(IamPrincipal::class, 'id', 'iam_principal_xid');
    }
}