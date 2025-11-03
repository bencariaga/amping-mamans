<?php

namespace App\Actions\GuaranteeLetter;

use App\Models\Operation\GLTemplate;
use App\Models\Operation\Data;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StoreGLTemplate
{
    public function execute(array $data): GLTemplate
    {
        DB::beginTransaction();

        $dataRecord = Data::create([
            'archive_status' => 'Unarchived',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $template = GLTemplate::create([
            'data_id' => $dataRecord->data_id,
            'gl_tmp_title' => $data['gl_tmp_title'],
            'gl_content' => $data['gl_content'],
            'signers' => $data['signers'],
            'signatures' => $data['signatures'],
        ]);

        DB::commit();

        return $template;
    }
}
