<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Core system data
            ['data_id' => 'DATA-2025-AUG-000000001', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000003', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000004', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000005', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000006', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000007', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000008', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000009', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000010', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000011', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000012', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000013', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000014', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000015', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000016', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000017', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000018', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000019', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000020', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000021', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000022', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000023', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000024', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000025', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000026', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000033', 'archive_status' => 'Unarchived', 'created_at' => '2025-10-09 19:59:15', 'updated_at' => '2025-10-28 08:10:52', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000037', 'archive_status' => 'Unarchived', 'created_at' => '2025-10-18 15:55:24', 'updated_at' => '2025-10-18 15:55:24', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000038', 'archive_status' => 'Unarchived', 'created_at' => '2025-08-01 04:00:00', 'updated_at' => '2025-08-01 04:00:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000041', 'archive_status' => 'Unarchived', 'created_at' => '2025-10-28 11:40:00', 'updated_at' => '2025-10-28 11:40:00', 'archived_at' => null],
            ['data_id' => 'DATA-2025-AUG-000000046', 'archive_status' => 'Unarchived', 'created_at' => '2025-10-28 20:18:13', 'updated_at' => '2025-10-28 20:18:13', 'archived_at' => null],
        ];

        DB::table('data')->insert($data);
    }
}
