<?php

namespace App\Models\Authentication;

use App\Actions\DatabaseTableIdGeneration\GenerateRoleId;
use App\Models\Operation\Data;
use App\Models\User\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'role_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'data_id',
        'role',
        'allowed_actions',
        'access_scope',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            if (empty($role->role_id)) {
                $role->role_id = GenerateRoleId::execute();
            }
        });
    }

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
