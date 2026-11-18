<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Memeriksa apakah pengguna yang login memiliki role Supervisor ke atas
     * (Supervisor, Head Of Dapartement, Dept Head) yang berhak menghapus data master.
     */
    protected function canDeleteMaster(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $role = auth()->user()->role;
        $rawRole = auth()->user()->getRawOriginal('role');

        return in_array($role, ['Supervisor', 'Head Of Dapartement', 'Dept Head'])
            || (is_numeric($rawRole) && (int) $rawRole <= 1);
    }
}
