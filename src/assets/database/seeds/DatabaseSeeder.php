<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('ArticlesTableSeeder');
	}

}

class ArticlesTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('articles')->insert([

			[
				'url' => 'first',
			],
			[
				'url' => 'second',
			],
			[
				'url' => 'third'
			]
		]);

		DB::table('article_langs')->insert([

			[
				'name' 			=> 'First name en',
				'title' 		=> 'First title en',
				'article_id' 	=> 1,
				'lang'			=> 'en'
			],
			[
				'name' 			=> 'Second name en',
				'title' 		=> 'Second title en',
				'article_id' 	=> 2,
				'lang'			=> 'en'
			],
			[
				'name' 			=> 'Second name fr',
				'title' 		=> 'Second title fr',
				'article_id' 	=> 2,
				'lang'			=> 'fr'
			],				
		]);
	}	
}
