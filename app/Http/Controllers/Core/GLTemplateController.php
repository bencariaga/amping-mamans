<?php

namespace App\Http\Controllers\Core;

use App\Actions\GuaranteeLetter\ListGLTemplates;
use App\Actions\GuaranteeLetter\StoreGLTemplate;
use App\Actions\GuaranteeLetter\UpdateGLTemplate;
use App\Actions\GuaranteeLetter\DeleteGLTemplate;
use App\Http\Controllers\Controller;
use App\Models\Operation\GLTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class GLTemplateController extends Controller
{
    public function listGLTemplates(Request $request, ListGLTemplates $action)
    {
        $templates = $action->execute($request);
        return view('pages.dashboard.templates.guarantee-letters.list', compact('templates'));
    }

    public function createGLTemplate()
    {
        return view('pages.dashboard.templates.guarantee-letters.create');
    }

    public function storeGLTemplate(Request $request, StoreGLTemplate $action)
    {
        try {
            $request->validate([
                'gl_tmp_title' => 'required|string|max:30',
                'gl_content_hidden' => [
                    'required',
                    'string',
                    'max:5000',
                ],
                'signers' => 'required|string',
                'signatures' => 'required|string',
            ]);

            $action->execute([
                'gl_tmp_title' => $request->input('gl_tmp_title'),
                'gl_content' => $request->input('gl_content_hidden'),
                'signers' => $request->input('signers'),
                'signatures' => $request->input('signatures'),
            ]);

            return redirect()->route('gl-templates.list')->with('success', 'Guarantee letter template has been created successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            Log::error('GL template creation failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to create new guarantee letter template. Please try again.');
        }
    }

    public function editGLTemplate(GLTemplate $template)
    {
        return view('pages.dashboard.templates.guarantee-letters.edit', ['template' => $template]);
    }

    public function updateGLTemplate(Request $request, GLTemplate $template, UpdateGLTemplate $action)
    {
        try {
            $request->validate([
                'gl_tmp_title' => 'required|string|max:30',
                'gl_content_hidden' => [
                    'required',
                    'string',
                    'max:5000',
                ],
                'signers' => 'required|string',
                'signatures' => 'required|string',
            ]);

            $action->execute($template, [
                'gl_tmp_title' => $request->input('gl_tmp_title'),
                'gl_content' => $request->input('gl_content_hidden'),
                'signers' => $request->input('signers'),
                'signatures' => $request->input('signatures'),
            ]);

            return redirect()->route('gl-templates.list')->with('success', 'Guarantee letter template has been updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            Log::error('GL template update failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to update guarantee letter template. Please try again.');
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
