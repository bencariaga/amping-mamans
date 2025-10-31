<?php

namespace App\Models\User;

use App\Actions\DatabaseTableIdGeneration\GenerateStaffId;
use App\Models\Audit\AuditLog;
use App\Models\Audit\Report;
use App\Models\Authentication\Role;
use App\Models\Communication\Message;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $primaryKey = 'staff_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'member_id',
        'role_id',
        'file_name',
        'file_extension',
        'password',
    ];

    protected $casts = [
        'file_extension' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($staff) {
            if (empty($staff->staff_id)) {
                $staff->staff_id = GenerateStaffId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'staff_id', 'staff_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'staff_id', 'staff_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'staff_id', 'staff_id');
    }
}
