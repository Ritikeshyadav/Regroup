<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpaceFolderListTaskLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'space_folder_list_task_links';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'space_folder_list_link_xid',
        'tasks_id',
        'space_xid',
        'space_list_link_xid',
        'iam_principal_xid',
        'name',
        'description',
        'start_date',
        'due_date',
        'number',
        'cover_image',
        'priority_master_xid',
        'comment',
        'status_master_xid',
        'custom_field_date',
        'custom_field_text',
        'custom_field_email',
        'custom_field_1',
        'custom_field_2',
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

    public function delete_space()
    {
        return $this->hasOne(Space::class, 'id', 'space_xid');
    }

    public function delete_space_folder_list_link()
    {
        return $this->hasOne(SpaceFolderListLink::class, 'id', 'space_folder_list_link_xid');
    }

    public function delete_space_list_link()
    {
        return $this->hasOne(SpaceListLink::class, 'id', 'space_list_link_xid');
    }

    public function delete_iam_principal()
    {
        return $this->hasOne(IamPrincipal::class, 'id', 'iam_principal_xid');
    }
}
