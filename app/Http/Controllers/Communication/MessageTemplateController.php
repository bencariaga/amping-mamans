<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Communication\MessageTemplate;
use App\Models\Storage\Data;

class MessageTemplateController extends Controller
{   
    public function index()
    {
        $templates = MessageTemplate::orderBy('msg_tmp_id', 'desc')->get();
        return view('pages.sidebar.message-template.index', compact('templates'));
    }

    public function create()
    {
        return view('pages.sidebar.message-template.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'msg_tmp_title' => 'required|string|max:255',
            'msg_tmp_text' => 'required|string|max:1000',
        ]);
        $templateData = Data::create([
            'data_status' => 'Unarchived',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $template = MessageTemplate::create([
            'msg_tmp_title' => $request->input('msg_tmp_title'),
            'msg_tmp_text' => $request->input('msg_tmp_text'),
            'data_id' => $templateData->data_id,
        ]);

        return redirect()->route('message-templates.create')->with('success', 'Message Template created!');
    }

     public function edit($id)
    {
        $template = MessageTemplate::findOrFail($id);
        return view('pages.sidebar.message-template.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'msg_tmp_title' => 'required|string|max:255',
            'msg_tmp_text' => 'required|string|max:1000',
        ]);

        $template = MessageTemplate::findOrFail($id);
        $template->update([
            'msg_tmp_title' => $request->input('msg_tmp_title'),
            'msg_tmp_text' => $request->input('msg_tmp_text'),
        ]);

        return redirect()->route('message-templates.index')->with('success', 'Message Template updated!');
    }

    public function destroy($id)
    {
        $template = MessageTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('message-templates.index')->with('success', 'Message Template deleted!');
    }
}