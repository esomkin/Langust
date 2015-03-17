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

DB::listen(

	function ($query, $bindings, $time) {
        
		$data = compact('bindings', 'time');

		// Format binding data for sql insertion
        foreach ($bindings as $i => $binding)
        {   
            if ($binding instanceof \DateTime)
            {   
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            }
            else if (is_string($binding))
            {   
                $bindings[$i] = "'$binding'";
            }   
        } 

        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
        $query = vsprintf($query, $bindings); 

        Log::info($query, $data);   
    }
); 

Route::get('/', 'WelcomeController@index');

Route::get('articles', function(App\Models\Article $article){

	$temp = $article->where('url', '=', 'second')->first();

	$temp->translate('fr')->name 	= 'Test fr name';
	$temp->translate('fr')->title 	= 'Test fr title';
	$temp->save();

	/*
	$article::create([

		'url' => 'fourth',
		'en' => [

			'name' 	=> 'Fourth name en',
			'title' => 'Fourth title en',
		],
	]);
	*/

	/*
	$temp = $article->where('url', '=', 'first')->first();
	$temp->save([

		'es' => [

			'name' 	=> 'First name es1',
			'title' => 'First title es1',
		],
	]);
	*/
});