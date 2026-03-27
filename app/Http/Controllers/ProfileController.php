<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        return view('app.profile.index');
    }

    public function update(Request $request)
    {
        try {
            $validated = Validator::make(
                $request->all(),
                [
                    'name'   => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                ],
                [],
                [
                    'name'   => 'Nama',
                    'email' => 'Email',
                ]
            );

            if ($validated->fails()) {
                return response()->json(['errors' => $validated->errors()], 422);
            } else {
                $user = User::findOrFail($request->id);

                $data = [
                    'name'   => $request->name,
                    'email' => $request->email,
                ];

                $user->update($data);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Data berhasil disimpan.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validated = Validator::make(
                $request->all(),
                [
                    'old_password' => 'required|string',
                    'password' => 'required|string|confirmed',
                    'password_confirmation' => 'required|string',
                ],
                [],
                [
                    'old_password' => 'Kata Sandi Lama',
                    'password' => 'Kata Sandi',
                    'password_confirmation' => 'Konfirmasi Kata Sandi',
                ]
            );

            if ($validated->fails()) {
                return response()->json(['errors' => $validated->errors()], 422);
            } else {
                $user = User::findOrFail($request->id);

                if (!password_verify($request->old_password, $user->password)) {
                    return response()->json([
                        'errors' => [
                            'old_password' => ['Kata sandi lama tidak sesuai.']
                        ]
                    ], 422);
                }

                $user->update([
                    'password' => bcrypt($request->password),
                ]);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Kata sandi berhasil diubah.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updateImage(Request $request)
    {
        try {
            $validated = Validator::make(
                $request->all(),
                [
                    'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                ],
                [],
                [
                    'image' => 'Foto Profil',
                ]
            );

            if ($validated->fails()) {
                return response()->json(['errors' => $validated->errors()], 422);
            } else {
                $user = User::findOrFail($request->id);

                if ($user->image && Storage::disk('public')->exists('users/' . $user->image)) {
                    Storage::disk('public')->delete('users/' . $user->image);
                }

                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('users', $filename, 'public');

                $user->update([
                    'image' => $filename,
                ]);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Foto profil berhasil diperbarui.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteImage(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            if ($user->image && Storage::disk('public')->exists('users/' . $user->image)) {
                Storage::disk('public')->delete('users/' . $user->image);
            }

            $user->update([
                'image' => null,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Foto profil berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
