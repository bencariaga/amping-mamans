<?php

namespace App\Http\Controllers\Core;

use App\Actions\GuaranteeLetter\DeleteGLTemplate;
use App\Actions\GuaranteeLetter\HandleGLTemplateSignatures;
use App\Actions\GuaranteeLetter\ListGLTemplates;
use App\Actions\GuaranteeLetter\StoreGLTemplate;
use App\Actions\GuaranteeLetter\UpdateGLTemplate;
use App\Actions\GuaranteeLetter\ValidateGLTemplateData;
use App\Http\Controllers\Controller;
use App\Models\Operation\GLTemplate;
use App\Models\User\Staff;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class GLTemplateController extends Controller
{
    public function listGLTemplates(Request $request, ListGLTemplates $action)
    {
        $templates = $action->execute($request);

        return view('pages.dashboard.templates.guarantee-letters.list', ['templates' => $templates]);
    }

    public function createGLTemplate()
    {
        $programHead = Staff::whereHas('role', function ($query) {
            $query->where('role', 'Program Head');
        })->first();

        return view('pages.dashboard.templates.guarantee-letters.create', ['programHead' => $programHead]);
    }

    public function storeGLTemplate(Request $request, ValidateGLTemplateData $validator, HandleGLTemplateSignatures $signatureHandler, StoreGLTemplate $action)
    {
        try {
            $validator->execute($request);
            $signatures = $signatureHandler->handleUpload($request);
            $action->execute(['gl_tmp_title' => $request->input('gl_tmp_title'), 'gl_content' => $request->input('gl_content'), 'signers' => $request->input('signers'), 'signatures' => $signatures]);

            return redirect()->route('gl-templates.list')->with('success', 'Guarantee letter template has been created successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (Exception $e) {
            Log::error('GL template creation failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Failed to create new guarantee letter template. Please try again.');
        }
    }

    public function viewGLTemplate(?GLTemplate $template = null)
    {
        if (!$template) {
            $template = GLTemplate::latest('gl_tmp_id')->first();
        }

        if (!$template) {
            return redirect()->route('gl-templates.list')->with('error', 'No GL templates found. Please create one first.');
        }

        $signersData = json_decode($template->signers, true);
        $signaturesArray = explode(',', $template->signatures);

        return view('pages.dashboard.templates.guarantee-letter', [
            'template' => $template,
            'signersData' => $signersData,
            'signature1' => $signaturesArray[0] ?? '',
            'signature2' => $signaturesArray[1] ?? ''
        ]);
    }

    public function editGLTemplate(GLTemplate $template)
    {
        $programHead = Staff::whereHas('role', function ($query) {
            $query->where('role', 'Program Head');
        })->first();

        return view('pages.dashboard.templates.guarantee-letters.edit', ['template' => $template, 'programHead' => $programHead]);
    }

    public function updateGLTemplate(Request $request, GLTemplate $template, ValidateGLTemplateData $validator, HandleGLTemplateSignatures $signatureHandler, UpdateGLTemplate $action)
    {
        try {
            $validator->execute($request);
            $signatures = $signatureHandler->handleUpload($request, $template);
            $action->execute($template, ['gl_tmp_title' => $request->input('gl_tmp_title'), 'gl_content' => $request->input('gl_content'), 'signers' => $request->input('signers'), 'signatures' => $signatures]);

            return redirect()->route('gl-templates.list')->with('success', 'Guarantee letter template has been updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (Exception $e) {
            Log::error('GL template update failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Failed to update guarantee letter template. Please try again.');
        }
    }

    public function destroyGLTemplate(GLTemplate $template, DeleteGLTemplate $action)
    {
        try {
            $action->execute($template);

            return redirect()->route('gl-templates.list')->with('success', 'Guarantee letter template has been deleted successfully.');
        } catch (Exception $e) {
            Log::error('GL template hard deletion failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to permanently delete guarantee letter template. Please try again.');
        }
    }
}
