<?php

namespace App\Actions\GuaranteeLetter;

use App\Models\Operation\GLTemplate;
use Illuminate\Support\Facades\DB;

class DeleteGLTemplate
{
    public function execute(GLTemplate $template): bool
    {
        DB::beginTransaction();

        $data = $template->data;
        $template->delete();

        if ($data) {
            $data->delete();
        }

        DB::commit();

        return true;
    }
}
