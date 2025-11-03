<?php

namespace App\Actions\GuaranteeLetter;

use App\Models\Operation\GLTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateGLTemplate
{
    public function execute(GLTemplate $template, array $data): GLTemplate
    {
        DB::beginTransaction();

        $template->update([
            'gl_tmp_title' => $data['gl_tmp_title'],
            'gl_content' => $data['gl_content'],
            'signers' => $data['signers'],
            'signatures' => $data['signatures'],
        ]);

        $template->data->update([
            'updated_at' => Carbon::now(),
        ]);

        DB::commit();

        return $template->fresh();
    }
}
