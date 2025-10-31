<?php

namespace App\Models\Operation;

use App\Actions\DatabaseTableIdGeneration\GenerateDataId;
use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\Authentication\Role;
use App\Models\Communication\MessageTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Data extends Model
{
    use HasFactory;

    protected $table = 'data';

    protected $primaryKey = 'data_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'data_id',
        'archive_status',
        'created_at',
        'updated_at',
        'archived_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($d) {
            if (empty($d->data_id)) {
                $d->data_id = GenerateDataId::execute();
            }
            if (empty($d->created_at)) {
                $d->created_at = Carbon::now();
            }
        });

        static::updating(function ($d) {
            $d->updated_at = Carbon::now();
        });
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'data_id', 'data_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'data_id', 'data_id');
    }

    public function messageTemplates()
    {
        return $this->hasMany(MessageTemplate::class, 'data_id', 'data_id');
    }

    public function guaranteeLetters()
    {
        return $this->hasMany(GuaranteeLetter::class, 'data_id', 'data_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'data_id', 'data_id');
    }

    public function tariffLists()
    {
        return $this->hasMany(TariffList::class, 'data_id', 'data_id');
    }

    public function occupations()
    {
        return $this->hasMany(Occupation::class, 'data_id', 'data_id');
    }
}
