<?php

namespace App\Actions\Archive;

use App\Models\Operation\Data;

class UnarchiveItem
{
    public function execute(string $id): bool
    {
        $data = Data::whereHas('applications', function ($q) use ($id) {
            $q->where('application_id', $id);
        })->orWhereHas('tariffLists', function ($q) use ($id) {
            $q->where('tariff_list_id', $id);
        })->orWhereHas('messageTemplates', function ($q) use ($id) {
            $q->where('msg_tmp_id', $id);
        })->orWhereHas('roles', function ($q) use ($id) {
            $q->where('role_id', $id);
        })->orWhereHas('occupations', function ($q) use ($id) {
            $q->where('occupation_id', $id);
        })->orWhereHas('services', function ($q) use ($id) {
            $q->where('service_id', $id);
        })->first();

        if ($data) {
            $data->update([
                'archive_status' => 'Unarchived',
                'archived_at' => null,
            ]);

            return true;
        }

        return false;
    }
}
