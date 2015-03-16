<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleLang extends Model
{
	public $timestamps = false;

	protected $fillable = [

		'name',
	];
}