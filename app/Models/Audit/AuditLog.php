<?php

namespace App\Models\Audit;

use App\Actions\DatabaseTableIdGeneration\GenerateAuditLogId;
use App\Models\User\Staff;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $primaryKey = 'al_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'al_id',
        'staff_id',
        'al_type',
        'al_text',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->al_id)) {
                $log->al_id = GenerateAuditLogId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }
}
