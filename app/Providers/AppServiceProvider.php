<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Route;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::resourceVerbs([
            'create' => 'adauga',
            'edit' => 'modifica'
        ]);
        Paginator::useBootstrap();

        // Salvarea tuturor cererilor catre baza de date
        // DB::listen(function($query) {
        //     File::append(
        //         storage_path('/logs/query.log'),
        //         '[' . date('Y-m-d H:i:s') . ']' .
        //         ' | IP: ' . \Request::ip() .
        //         PHP_EOL .
        //         $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL
        //    );
        // });
    }
}
