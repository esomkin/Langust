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

	/*
	Ready
	$article::create([

		'url' => 'fourth',
		'en' => [

			'name' 	=> 'Fourth name en',
			'title' => 'Fourth title en',
		],
		'fr' => [

			'name' 	=> 'Fourth name fr',
			'title' => 'Fourth title fr',
		],
	]);
	*/
	
	/*
	Ready
	$fourth = $article->where('url', '=', 'fourth-change')->first();

	echo $fourth->url. '<br/>';
	echo $fourth->name. '<br/>';
	echo $fourth->translate('fr')->name. '<br/>';
	echo $fourth->fr->name. '<br/>';

	$fourth->url = 'fourth-change';
	$fourth->name = 'Fourth locale not set name';
	$fourth->fr->name = 'Fourth fr locale set name';
	$fourth->translate('fr')->title = 'Fourth fr locale set title with translate';

	$fourth->save();
	*/

	/*
	Ready
	$third = $article->where('url', '=', 'third')->first();
	$third->save([

		'es' => [

			'name' 	=> 'Third name es',
			'title' => 'Third title es',
		],
	]);
	*/

	/*
	// Not working! Fix it!
	$third = $article->where('url', '=', 'third')->first();
	$third->fill([

		'en' => [

			'name' 	=> 'Third name en',
			'title' => 'Third title en',
		],
		'es' => [

			'name' 	=> 'Third name es change',
			'title' => 'Third title es change',
		],	
		
	])->save();
	*/
});
