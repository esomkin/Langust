<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('articles', function(App\Models\Article $article){

	$temp = $article->where('url', '=', 'first')->first();
	$temp->en->name = 'Test test test';
	$temp->save();
});