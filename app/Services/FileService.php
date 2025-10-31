<?php

namespace App\Services;

use App\Models\Operation\Data;
use App\Models\User\Member;
use App\Models\User\Staff;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileService
{
    public function storeImage(UploadedFile $uploadedFile, Member $member)
    {
        $path = 'profile_pictures';
        $fileName = Str::random(40).'.'.$uploadedFile->getClientOriginalExtension();
        $filePath = $uploadedFile->storeAs($path, $fileName, 'public');

        $data = Data::create([
            'data_status' => 'Unarchived',
        ]);

        $file = Staff::create([
            'data_id' => $data->data_id,
            'member_id' => $member->member_id,
            'file_name' => $filePath,
            'file_extension' => $uploadedFile->getClientOriginalExtension(),
        ]);

        return $file;
    }
}
