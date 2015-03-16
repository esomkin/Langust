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