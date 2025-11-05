<?php

namespace App\Actions\GuaranteeLetter;

use Illuminate\Http\Request;
use App\Models\Operation\GLTemplate;

class HandleGLTemplateSignatures
{
    public function handleUpload(Request $request, ?GLTemplate $template = null): string
    {
        $existingSignatures = $template ? explode(',', $template->signatures) : [];
        $signaturePaths = [
            $existingSignatures[0] ?? '',
            $existingSignatures[1] ?? '',
        ];

        if ($request->hasFile('signature_file_1')) {
            if (!empty($signaturePaths[0]) && file_exists(public_path($signaturePaths[0]))) {
                unlink(public_path($signaturePaths[0]));
            }
            $file1 = $request->file('signature_file_1');
            $filename1 = 'signature_1_' . time() . '.' . $file1->getClientOriginalExtension();
            $file1->move(public_path('signatures'), $filename1);
            $signaturePaths[0] = 'signatures/' . $filename1;
        }

        if ($request->hasFile('signature_file_2')) {
            if (!empty($signaturePaths[1]) && file_exists(public_path($signaturePaths[1]))) {
                unlink(public_path($signaturePaths[1]));
            }
            $file2 = $request->file('signature_file_2');
            $filename2 = 'signature_2_' . time() . '.' . $file2->getClientOriginalExtension();
            $file2->move(public_path('signatures'), $filename2);
            $signaturePaths[1] = 'signatures/' . $filename2;
        }

        return implode(',', $signaturePaths);
    }
}
