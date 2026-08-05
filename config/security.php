<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Native Security (C++ FFI)
    |--------------------------------------------------------------------------
    | Path ke shared library hasil kompilasi native/compile.bat (Windows/MSVC)
    | atau native/compile.sh (Linux/macOS, ngsecurity.so).
    */

    'dll' => env('NGSECURITY_DLL', base_path('native/ngsecurity.dll')),

    /*
    |--------------------------------------------------------------------------
    | Session Binding (anti session hijacking)
    |--------------------------------------------------------------------------
    | Middleware ng-hardening mengikat sesi admin ke sidik jari perangkat
    | (User-Agent + Accept-Language, opsional IP) menggunakan HMAC-SHA256
    | native dan perbandingan constant-time.
    */

    'session_binding' => env('NGSECURITY_SESSION_BINDING', true),

    'bind_ip' => env('NGSECURITY_BIND_IP', false),

];
