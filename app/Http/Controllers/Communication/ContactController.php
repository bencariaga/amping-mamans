<?php

namespace App\Http\Controllers\Communication;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\User\Contact;

class ContactController extends Controller
{
    public function update(Request $request)
    {
        $updates = $request->input('contacts', []);
        $rules = [];
        $messages = [];

        foreach ($updates as $id => $data) {
            $rules["contacts.{$id}.phone_number"] = [
                'required',
                'string',
                'max:13',
                Rule::unique('contacts', 'phone_number')->ignore($id, 'contact_id'),
            ];

            $rules["contacts.{$id}.phone_number_other"] = [
                'nullable',
                'string',
            ];

            $messages["contacts.{$id}.phone_number.required"] = "The main phone number field of " . Str::upper($id) . " is required.";
            $messages["contacts.{$id}.phone_number.max"] = "The main phone number field of " . Str::upper($id) . " must not be more than 13 characters.";
        }

        $validated = $request->validate($rules, $messages);

        foreach ($validated['contacts'] as $id => $data) {
            $contact = Contact::findOrFail($id);

            $contact->update([
                'phone_number'       => $data['phone_number'],
                'phone_number_other' => $data['phone_number_other'] ?? null,
            ]);
        }

        return back()->with('success', 'Phone number/s of every specific contact individual has been updated.');
    }
}
