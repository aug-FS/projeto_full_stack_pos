<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::post('/contato', function (Request $request) {
    $request->validate([
        'nome' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'mensagem' => ['required', 'string', 'max:2000'],
    ]);

    return back()->with('success', 'Mensagem enviada com sucesso!');
})->name('landing.contact');
