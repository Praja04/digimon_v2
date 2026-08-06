<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class UsersController extends Controller
{
    /**
     * Menampilkan halaman pengguna dan data untuk DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::query()
                ->where('id', '!=', auth()->id())
                ->orderBy('name', 'asc');

            return DataTables::of($users)
                ->addIndexColumn()

                /*
                 * Kolom foto.
                 *
                 * 
                 * Jika foto tersedia, tampilkan file dari storage.
                 * Jika tidak tersedia, tampilkan avatar inisial otomatis.
                 */
                ->addColumn('photo', function (User $user): string {
                    $photoUrl = $this->getUserPhotoUrl($user);

                    return '
                        <img
                            src="' . e($photoUrl) . '"
                            alt="Foto ' . e($user->name) . '"
                            class="rounded-circle border"
                            width="42"
                            height="42"
                            style="
                                width: 42px;
                                height: 42px;
                                min-width: 42px;
                                object-fit: cover;
                                display: block;
                                margin: 0 auto;
                                background-color: #e9ecef;
                            "
                        >
                    ';
                })

                /*
                 * Tombol aksi.
                 */
                ->addColumn('action', function (User $user): string {
                    return '
                        <button
                            type="button"
                            class="btn btn-sm btn-warning me-1"
                            id="btnEdit"
                            data-id="' . $user->id . '"
                        >
                            <span class="mdi mdi-pencil"></span>
                            Edit
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-danger"
                            id="btnDelete"
                            data-id="' . $user->id . '"
                        >
                            <span class="mdi mdi-trash-can"></span>
                            Hapus
                        </button>
                    ';
                })

                ->rawColumns([
                    'photo',
                    'action',
                ])

                ->make(true);
        }

        return view('app.users.index');
    }

    /**
     * Menyimpan pengguna baru atau memperbarui pengguna lama.
     */
    public function store(UsersRequest $request): JsonResponse
    {
        try {
            $isEdit = $request->filled('id');

            if ($isEdit) {
                $user = User::find($request->id);

                if (! $user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data pengguna tidak ditemukan.',
                    ], 404);
                }
            } else {
                $user = new User();
            }

            $user->name = trim($request->name);
            $user->email = trim($request->email);
            $user->role = $request->role;

            /*
             * Pengguna baru wajib memiliki password.
             * Saat edit, password hanya diganti jika field diisi.
             */
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            /*
             * Simpan foto baru.
             *
             * Jika sebelumnya sudah ada foto, foto lama akan dihapus.
             */
            if ($request->hasFile('photo')) {
                if (
                    ! empty($user->photo)
                    && Storage::disk('public')->exists($user->photo)
                ) {
                    Storage::disk('public')->delete($user->photo);
                }

                $photo = $request->file('photo');

                $extension = strtolower(
                    $photo->getClientOriginalExtension()
                );

                $fileName = Str::uuid()->toString()
                    . '.'
                    . $extension;

                $user->photo = $photo->storeAs(
                    'users',
                    $fileName,
                    'public'
                );
            }

            $user->save();

            return response()->json([
                'status' => 'success',

                'message' => $isEdit
                    ? 'Data pengguna berhasil diperbarui.'
                    : 'Data pengguna berhasil ditambahkan.',

                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRawOriginal('role'),
                    'photo' => $this->getUserPhotoUrl($user),
                ],
            ], $isEdit ? 200 : 201);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan pengguna.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengambil data pengguna untuk modal edit.
     */
    public function edit(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pengguna tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',

                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRawOriginal('role'),
                    'photo' => $this->getUserPhotoUrl($user),
                ],
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data pengguna.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus pengguna dan foto miliknya.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pengguna tidak ditemukan.',
                ], 404);
            }

            if ($user->id === auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun yang sedang digunakan tidak dapat dihapus.',
                ], 422);
            }

            if (
                ! empty($user->photo)
                && Storage::disk('public')->exists($user->photo)
            ) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengguna berhasil dihapus.',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus pengguna.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghasilkan URL foto pengguna.
     *
     * Jika file foto tidak tersedia, gunakan avatar SVG dengan
     * inisial nama pengguna. Dengan begitu tidak ada broken image.
     */
    private function getUserPhotoUrl(User $user): string
    {
        if (
            ! empty($user->photo)
            && Storage::disk('public')->exists($user->photo)
        ) {
            return Storage::disk('public')->url($user->photo);
        }

        return $this->generateInitialAvatar($user->name);
    }

    /**
     * Membuat avatar inisial berbentuk SVG Data URI.
     *
     * Contoh:
     * Analis Field -> AF
     * Foreman      -> F
     */
    private function generateInitialAvatar(?string $name): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            $initials = 'U';
        } else {
            $words = preg_split(
                '/\s+/',
                $name,
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            $initials = '';

            foreach (array_slice($words, 0, 2) as $word) {
                $initials .= mb_strtoupper(
                    mb_substr($word, 0, 1)
                );
            }

            if ($initials === '') {
                $initials = 'U';
            }
        }

        $safeInitials = htmlspecialchars(
            $initials,
            ENT_QUOTES,
            'UTF-8'
        );

        $svg = '
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="100"
                height="100"
                viewBox="0 0 100 100"
            >
                <rect
                    width="100"
                    height="100"
                    rx="50"
                    fill="#405189"
                />

                <text
                    x="50"
                    y="53"
                    dominant-baseline="middle"
                    text-anchor="middle"
                    font-family="Arial, sans-serif"
                    font-size="34"
                    font-weight="700"
                    fill="#ffffff"
                >
                    ' . $safeInitials . '
                </text>
            </svg>
        ';

        return 'data:image/svg+xml;base64,'
            . base64_encode($svg);
    }
}