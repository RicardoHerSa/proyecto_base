<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Página de inicio
Route::get('/home', 'HomeController@index')->name('home')->middleware('authorization')->middleware('auth');
Route::get('/', 'HomeController@index')->name('home')->middleware('authorization');
Route::get('inicio/', 'HomeController@index')->name('home')->middleware('authorization');

Route::get('lang/{lang}', function ($lang) {
    session(['lang' => $lang]);
    return Redirect::back();
})->where([
    'lang' => 'en|es'
]);


//login
Auth::routes(['register' => false]);

//Grupo
Route::resource('usergroup', 'Usergroup\UsergroupController')->middleware('authorization');

//buscador gropos select
Route::get('/select', 'Usergroup\UsergroupController@selectBox');

//Niveles
Route::resource('viewlevels', 'Viewlevel\ViewlevelsController')->middleware('authorization');

//Usuarios ->middleware('authorization')
Route::resource('users', 'RelationUserGroup\RelationUserGroupController')->middleware('authorization');
Route::get('/user/block', 'RelationUserGroup\RelationUserGroupController@updateStatus')->name('users.block')->middleware('authorization');
Route::get('/usermassive/block', 'RelationUserGroup\RelationUserGroupController@UpdateStatusmassive')->name('users.blockmassive')->middleware('authorization');
Route::get('/usermassive/unblock', 'RelationUserGroup\RelationUserGroupController@UpdateStatusmassive')->name('users.unblockmassive')->middleware('authorization');
Route::get('/user/pagination', 'RelationUserGroup\RelationUserGroupController@pagination')->name('users.pagination');
Route::get('/users/destroy/{id}', 'RelationUserGroup\RelationUserGroupController@Destroy')->name('users.destroy')->middleware('authorization');
Route::get('/users/{id}/edit', 'RelationUserGroup\RelationUserGroupController@Destroy')->name('users.edit')->middleware('authorization');


//Menu
Route::resource('menu', 'Menu\MenuController')->middleware('authorization');
Route::get('/menus/blocks', 'Menu\MenuController@getMenuChange')->name('menus.blocks')->middleware('authorization');

//Reset
Route::resource('resetemail', 'ResetEmail\PasswordResetController');
Route::get('/reset/mail', 'ResetEmail\PasswordResetController@create')->name('reset.mail');
Route::get('/reset/token', 'ResetEmail\PasswordResetController@reset')->name('reset.token');
Route::get('/reset/view', 'ResetEmail\PasswordResetController@returnViewResetPassword')->name('reset.view');
Route::get('/reset/verify', 'ResetEmail\PasswordResetController@verifyPassword')->name('reset.verify');
Route::get('/reset/gh', 'ResetEmail\PasswordResetController@ResetGH')->name('reset.gh')->middleware('authorization');
Route::get('/reset/getUser','ResetEmail\PasswordResetController@getUser')->name('reset.getUser')->middleware('authorization');
Route::get('/reset/save','ResetEmail\PasswordResetController@save')->name('reset.save')->middleware('authorization');
Route::get('/reset/sendmail','ResetEmail\PasswordResetController@sendmail')->name('reset.sendmail')->middleware('authorization');
//Route::get('/reset/passEmp', 'ResetEmail\PasswordResetController@passEmp')->name('reset.passEmp')->middleware('authorization');


//Manager
Route::get('/manager', 'Manager\ManagerController@index')->name('manager');
Route::get('api/user/login','Manager\ManagerController@getUserLogin')->middleware('authorization');

//Generador de contraseña 
Route::get('p/{key}', function ($key) {return  response(array( 'auto' => Hash::make($key)),200 );})->name('p')->middleware('authorization');


//Creacion de usuarios
//Route::get('createuser',  'Services\ManagerUserController@UserCreate')->name('createuser')->middleware('authorization');
//Route::get('updatepas',   'Services\ManagerUserController@updatepas')->name('createuser')->middleware('authorization');
