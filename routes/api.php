<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// AUTHENTICATION
Route::post('/account/login', 'AccountLoginController@login')->name('account.login');
Route::post('account/logout', 'AccountLoginController@logout')->name('account.logout');
Route::post('/account/create', 'AccountCreateController@register')->name('account.create');
Route::post('/email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify');
Route::post('/email/resend', 'VerificationController@resend')->name('verification.resend');
Route::post('/password/forgot', 'PasswordForgotController@sendResetLinkEmail')->name('password.forgot');
Route::post('/password/reset', 'PasswordResetController@reset')->name('password.reset');

// PROFILE
Route::get('/profile', 'ProfileController@user')->name('profile.user');
Route::put('/profile', 'ProfileController@update')->name('profile.update');
Route::put('/profile/password', 'ProfileController@updatePassword')->name('profile.password');

// TODOS
Route::get('/todos', 'TodosController@index')->name('todos.index');
Route::post('/todos', 'TodosController@create')->name('todos.create');