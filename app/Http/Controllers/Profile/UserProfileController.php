<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Storage\Data;
use App\Models\Storage\File;
use App\Models\User\Member;

class UserProfileController extends Controller
{
    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $maxId = DB::table($table)->where($primaryKey, 'like', "{$base}%")->max($primaryKey);
        $lastNum = $maxId ? (int) Str::substr(Str::replace('-', '', $maxId), -9) : 0;
        $next = Str::padLeft($lastNum + 1, 9, '0');
        return $base . '-' . $next;
    }

    public function show(?Member $user = null)
    {
        if (!$user || Auth::id() === $user->member_id) {
            $user = Auth::user();
            $view = 'pages.sidebar.profiles.profile.self';
        } else {
            $view = 'pages.sidebar.profiles.profile.users';
        }

        $user->load(['files', 'staff.role', 'account.data']);
        return view($view)->with('user', $user);
    }

    public function update(Request $request, ?Member $user = null)
    {
        $target = $user && Auth::id() !== $user->member_id ? $user->load('account.data', 'files', 'staff') : Auth::user()->load('account.data', 'files', 'staff');

        if ($request->input('action') === 'change_password') {
            $request->validate([
                'username_confirmation_change' => ['required', 'string', function ($a, $v, $f) use ($target) {
                    if ($v !== $target->first_name . ' ' . $target->last_name) {
                        $f('Your confirmation input does not match with the actual one. Please try again.');
                    }
                }],
                'new_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            ]);

            $staff = $target->staff;
            $staff->password = Hash::make($request->new_password);
            $staff->save();

            return back()->with('success', 'The user password has been updated.');
        }

        $val = $request->validate([
            'first_name'                 => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ]+$/',
                Rule::unique('members')->where(fn ($q) =>
                    $q->where('member_type', 'Staff')->where('first_name', $request->first_name)->where('middle_name', $request->middle_name)->where('last_name', $request->last_name)->where('suffix', $request->suffix)->where('member_id', '!=', $target->member_id)
                )
            ],
            'middle_name'                => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z ]*$/'],
            'last_name'                  => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ]+$/'],
            'suffix'                     => ['nullable', 'string', Rule::in(['Sr.', 'Jr.', 'II', 'III', 'IV', 'V'])],
            'profile_picture'            => ['nullable', 'image', 'max:8192', 'mimes:jpg,jpeg,jfif,png,webp'],
            'remove_profile_picture_flag' => ['boolean'],
        ], [
            'first_name.unique' => 'This user account already exists with the same name.',
        ]);

        $target->fill($val);

        if ($request->boolean('remove_profile_picture_flag')) {
            $picture = $target->files()->where('file_type', 'Image')->first();

            if ($picture) {
                Storage::disk('public')->delete($picture->filename);
                $fileDataId = $picture->data_id;
                $picture->delete();

                if ($fileDataId !== $target->account->data_id) {
                    Data::where('data_id', $fileDataId)->delete();
                }
            }
        } elseif ($request->hasFile('profile_picture')) {
            $picture = $target->files()->where('file_type', 'Image')->first();

            if ($picture) {
                Storage::disk('public')->delete($picture->filename);
                $fileDataId = $picture->data_id;
                $picture->delete();

                if ($fileDataId !== $target->account->data_id) {
                    Data::where('data_id', $fileDataId)->delete();
                }
            }

            $fileRaw = $request->file('profile_picture');
            $path = $fileRaw->store('profile_pictures', 'public');
            $ext = $fileRaw->extension();
            $fileDataId = $this->generateNextId('DATA', 'data', 'data_id');
            $fileId = $this->generateNextId('FILE', 'files', 'file_id');

            Data::create([
                'data_id'     => $fileDataId,
                'data_status' => 'Unarchived',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            File::create([
                'file_id'        => $fileId,
                'data_id'        => $fileDataId,
                'member_id'      => $target->member_id,
                'file_type'      => 'Image',
                'filename'       => $path,
                'file_extension' => $ext,
            ]);
        }

        $target->save();

        return back()->with('success', 'User profile has been updated.');
    }

    public function deactivate(Request $request, Member $user)
    {
        $auth = Auth::user();
        $target = $user->member_id !== $auth->member_id ? $user : $auth;

        $target->account->account_status = $target->account->account_status == 'Deactivated' ? 'Active' : 'Deactivated';
        $target->account->last_deactivated_at = now();
        $target->account->save();

        if ($auth->member_id === $target->member_id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json(['message' => 'Your account has been deactivated successfully.'], 200);
        }

        return response()->json(['message' => 'Your account has been deactivated successfully.'], 200);
    }

    public function destroy(Request $request, Member $user)
    {
        $auth = Auth::user();
        $target = $user->member_id !== $auth->member_id ? $user : $auth;

        $request->validate([
            'username_confirmation_delete' => ['required', 'string', function ($a, $v, $f) use ($target) {
                $expectedRaw = Str::of($target->first_name . ' ' . $target->last_name)->trim();

                $normalize = function (string $s): string {
                    return Str::of($s)->trim()->replace('/\s+/', ' ')->lower();
                };

                $match = $normalize($v) === $normalize($expectedRaw);
                Log::debug('Delete Confirmation', ['submitted' => $v, 'expected' => $expectedRaw, 'match' => $match]);

                if (! $match) {
                    $f("Your confirmation input \"{$v}\" does not match \"{$expectedRaw}\".");
                }
            }],
        ]);

        DB::beginTransaction();

        try {
            $mainMemberDataId = $target->account->data_id;

            foreach ($target->files as $file) {
                if ($file->file_type === 'Image') {
                    Storage::disk('public')->delete($file->filename);
                }

                $fileDataId = $file->data_id;
                $file->delete();
                Data::where('data_id', $fileDataId)->delete();
            }

            $target->staff()->delete();
            $target->delete();
            $target->account()->delete();
            Data::where('data_id', $mainMemberDataId)->delete();
            DB::commit();

            if ($auth->member_id === $target->member_id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/')->with('success', 'Your account has been deleted successfully.');
            }

            return redirect()->route('profiles.users.list')->with('success', 'User account has been deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete user account: ' . $e->getMessage());
        }
    }
}
