<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/account/login', 'AccountLoginController@login')->name('account.login');
Route::post('account/logout', 'AccountLoginController@logout')->name('account.logout');
Route::post('/account/create', 'AccountCreateController@register')->name('account.create');
Route::post('/email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify');
Route::post('/email/resend', 'VerificationController@resend')->name('verification.resend');
Route::post('/password/forgot', 'PasswordForgotController@sendResetLinkEmail')->name('password.forgot');
Route::post('/password/reset', 'PasswordResetController@reset')->name('password.reset');