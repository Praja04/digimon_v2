<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsersRequest extends FormRequest
{
    /**
     * Tentukan apakah user boleh melakukan request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi.
     */
    public function rules(): array
    {
        $userId = $this->input('id');

        return [
            'id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($userId),
            ],

            'role' => [
                'required',
                'string',
                'max:100',
            ],

            'password' => [
                $userId ? 'nullable' : 'required',
                'string',
                'confirmed',
                Password::min(8),
            ],

            'password_confirmation' => [
                $userId ? 'nullable' : 'required',
                'string',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    /**
     * Pesan validasi.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan pengguna lain.',

            'role.required' => 'Hak akses wajib dipilih.',

            'password.required' => 'Password wajib diisi untuk pengguna baru.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min' => 'Password minimal 8 karakter.',

            'password_confirmation.required' =>
                'Konfirmasi password wajib diisi.',

            'photo.image' => 'File photo harus berupa gambar.',
            'photo.mimes' =>
                'Photo harus berformat JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran photo maksimal 2 MB.',
        ];
    }
}