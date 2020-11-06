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

Route::get('todolist','ToDoListController@index')->name('todolist');
Route::post('todolist','ToDoListController@create')->name('todolist.create');
Route::delete('todolist','ToDoListController@destroy')->name('todolist.destroy');
Route::post('todolist/change','ToDoListController@change')->name('todolist.change');
Route::patch('todolist','ToDoListController@update')->name('todolist.update');
Route::get('todolist/status','ToDoListController@status')->name('todolist.status');
