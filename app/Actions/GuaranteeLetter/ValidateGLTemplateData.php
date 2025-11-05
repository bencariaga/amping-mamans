<?php

namespace App\Actions\GuaranteeLetter;

use Illuminate\Http\Request;

class ValidateGLTemplateData
{
    public function execute(Request $request): array
    {
        return $request->validate([
            'gl_tmp_title' => ['required', 'string', 'max:30'],
            'gl_content' => ['required', 'string', 'max:5000'],
            'signers' => ['required', 'string'],
            'signature_file_1' => ['nullable', 'image', 'mimes:jpg,jpeg,jfif,png', 'max:2048'],
            'signature_file_2' => ['nullable', 'image', 'mimes:jpg,jpeg,jfif,png', 'max:2048'],
        ]);
    }
}
