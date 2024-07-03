<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpaceDocs extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'space_docs';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'doc_name',
        'file',
        'is_active',
    ];
}
