# Langust

This is a Laravel package for translatable models.

##Demo

Creating new translations

```php
App\Models\Article::create([

	'url' => 'your-url',
	'en' => [

		'name' 	=> 'Article english name',
		'title' => 'Article english title',
	],
	'fr' => [

		'name' 	=> 'Article french name',
		'title' => 'Article french title',
	],
]);
```

Getting translated attributes

```php
$article = App\Models\Article::where('url', '=', 'your-url')->first();

App::setLocale('en');
echo $article->name;

echo $article->translate('en')->name;
echo $article->en->name;
```

Setting translated attributes

```php
$article->url 			= 'your-url-change';

$article->name			= 'Article english name change';
$article->en->name		= 'Article english name change';
$article->translate('fr')->title= 'Article french title change';

$article->save();
```

or

```php
$article->save([

	'es' => [

		'name' 	=> 'Article spain name',
		'title' => 'Article spain title',
	],
]);
```

or 

```php
$article->fill([

	'en' => [

		'name' 	=> 'Article english name',
		'title' => 'Article english title',
	],
	'es' => [

		'name' 	=> 'Article spain name',
		'title' => 'Article spain title',
	],	
		
])->save();
```

##Installation in 4 steps

###Step 1: Install package

Add the package in your composer.json by executing the command.

```
composer require goodvin/langust:dev-master
```
Next, add the service provider to ```config/app.php```

```
'Goodvin\Langust\LangustServiceProvider',
```

###Step 2: Migrations

For example, we need to translate ```Article``` model. It is require an extra ```ArticleLang``` model.

```php
Schema::create('articles', function(Blueprint $table){

    $table->increments('id');
    $table->string('url', 200);
    $table->timestamps();
});
```

```php
Schema::create('article_langs', function(Blueprint $table){

    $table->increments('id');
    $table->string('name', 200);
    $table->string('title', 200);
    $table->integer('article_id')->unsigned();
    $table->enum('lang', [
    
        'en',
        'fr',
        'es',
    ])->index();
    
    $table->unique([
    
        'article_id',
        'lang'
    ]);
    
    $table->foreign('article_id')
        ->references('id')
        ->on('articles')
        ->onDelete('cascade');
});
```

###Step 3: Models

1. The translatable model ```Article``` should use the trait ```Goodvin\Langust\Langust```.
2. The convention for the translation model is ```ArticleLang```.

```php
// /app/Models/Article.php
<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
	use \Goodvin\Langust\Langust;

	protected $fillable = [

		'url',
	];

	protected $langust = [

		'name',
		'title',
	];

}

// /app/Models/ArticleLang.php
<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleLang extends Model
{
	public $timestamps = false;
}
```

It is no need to set fillable fields in translatable model :)

###Step 4: Configuration

Laravel 5.*

```
php artisan vendor:publish 
```
