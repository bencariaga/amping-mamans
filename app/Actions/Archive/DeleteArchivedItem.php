<?php

namespace App\Actions\Archive;

use App\Models\Authentication\Occupation;
use App\Models\Authentication\Role;
use App\Models\Communication\MessageTemplate;
use App\Models\Operation\Application;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use Illuminate\Support\Facades\DB;

class DeleteArchivedItem
{
    public function execute(string $id): bool
    {
        DB::beginTransaction();
        try {
            Application::where('application_id', $id)->delete();
            TariffList::where('tariff_list_id', $id)->delete();
            MessageTemplate::where('msg_tmp_id', $id)->delete();
            Role::where('role_id', $id)->delete();
            Occupation::where('occupation_id', $id)->delete();
            Service::where('service_id', $id)->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
