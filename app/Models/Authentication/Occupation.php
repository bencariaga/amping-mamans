<?php

namespace App\Models\Authentication;

use App\Actions\DatabaseTableIdGeneration\GenerateOccupationId;
use App\Models\Operation\Data;
use App\Models\User\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    use HasFactory;

    protected $table = 'occupations';

    protected $primaryKey = 'occupation_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'occupation_id',
        'data_id',
        'occupation',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($o) {
            if (empty($o->occupation_id)) {
                $o->occupation_id = GenerateOccupationId::execute();
            }
            if (empty($o->data_id)) {
                $o->data_id = Data::create()->data_id;
            }
        });
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id', 'data_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'occupation_id', 'occupation_id');
    }
}
