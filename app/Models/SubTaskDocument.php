<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTaskDocument extends Model
{
    use HasFactory;
    protected $table = "sub_task_documents";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'iam_principal_xid',
        'sub_task_xid',
        'doc_name',
        'file',
        'is_active',
    ];
}
