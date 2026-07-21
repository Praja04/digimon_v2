<?php

namespace App\Providers;

use App\Models\JenisIncoming;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view): void {
            try {
                if (! Schema::hasTable('jenis_incomings')) {
                    $view->with('jenisIncomingMenu', collect());

                    return;
                }

                /*
                 * Jangan pakai:
                 *
                 * ->where('kategori', 'Incoming')
                 *
                 * Karena isi kategori di database adalah:
                 * Inner, Outer, Karton, Others.
                 */
                $jenisIncomingMenu = JenisIncoming::query()
                    ->orderBy('id')
                    ->get();

                $view->with(
                    'jenisIncomingMenu',
                    $jenisIncomingMenu
                );
            } catch (Throwable $exception) {
                $view->with('jenisIncomingMenu', collect());
            }
        });
    }
}