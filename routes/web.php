<?php


use Illuminate\Support\Facades\Route;

Route::view('/{any?}', 'vuetify')->where('any', '^(?!api).*$');
