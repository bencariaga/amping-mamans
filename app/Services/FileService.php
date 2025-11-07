<?php

namespace App\Services;

use App\Models\Storage\Data;
use App\Models\Storage\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Models\User\Member;

class FileService
{
    public function storeImage(UploadedFile $uploadedFile, Member $member)
    {
        $path = 'profile_pictures';
        $fileName = Str::random(40) . '.' . $uploadedFile->getClientOriginalExtension();
        $filePath = $uploadedFile->storeAs($path, $fileName, 'public');

        $data = Data::create([
            'data_status' => 'Unarchived',
        ]);

        $file = File::create([
            'data_id' => $data->data_id,
            'member_id' => $member->member_id,
            'file_type' => 'Image',
            'filename' => $filePath,
            'file_extension' => $uploadedFile->getClientOriginalExtension(),
        ]);

        return $file;
    }
}
