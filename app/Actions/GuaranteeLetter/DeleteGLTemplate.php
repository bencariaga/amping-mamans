<?php

namespace App\Actions\GuaranteeLetter;

use App\Models\Operation\GLTemplate;
use Illuminate\Support\Facades\DB;
use Exception;

class DeleteGLTemplate
{
    public function execute(GLTemplate $template): bool
    {
        try {
            DB::beginTransaction();

            $data = $template->data;
            $template->delete();

            if ($data) {
                $data->delete();
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

