<?php

namespace App\Models\Authentication;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Storage\Data;
use App\Models\User\Staff;

class Role extends Model
{
    use HasFactory;

    protected $table        = 'roles';
    protected $primaryKey   = 'role_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = false;

    protected $fillable = [
        'role_id',
        'data_id',
        'role',
        'allowed_actions',
        'access_scope',
    ];

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class, 'role_id');
    }
}
