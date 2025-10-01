<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\Member;
use App\Models\Authentication\Role;
use App\Models\Communication\Message;
use App\Models\Audit\Log;
use App\Models\Audit\Report;

class Staff extends Model
{
    use HasFactory;

    protected $table        = 'staff';
    protected $primaryKey   = 'staff_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = false;

    protected $fillable = [
        'staff_id',
        'member_id',
        'role_id',
        'password',
    ];

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'staff_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'staff_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'staff_id');
    }
}
