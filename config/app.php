<?php
return [
    'name'     => env('APP_NAME','SmartBarangay'),
    'env'      => env('APP_ENV','production'),
    'debug'    => (bool) env('APP_DEBUG',false),
    'url'      => env('APP_URL','http://localhost'),
    'timezone' => 'Asia/Manila',
    'locale'   => 'en',
    'fallback_locale' => 'en',
    'faker_locale'    => 'en_US',
    'key'      => env('APP_KEY'),
    'cipher'   => 'AES-256-CBC',
    'providers' => Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),
    'aliases'  => [
        'App'     => Illuminate\Support\Facades\App::class,
        'Auth'    => Illuminate\Support\Facades\Auth::class,
        'DB'      => Illuminate\Support\Facades\DB::class,
        'Route'   => Illuminate\Support\Facades\Route::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Str'     => Illuminate\Support\Str::class,
        'View'    => Illuminate\Support\Facades\View::class,
        'PDF'     => Barryvdh\DomPDF\Facade\Pdf::class,
    ],
];
