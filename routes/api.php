<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/account/login', 'AccountLoginController@login')->name('account.login');
