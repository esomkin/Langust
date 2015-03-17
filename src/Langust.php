<?php namespace Goodvin\Langust;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

trait Langust
{

/*
Okay, I've re-read your question and I think you have a few things wrong, so rather than leaving any further comments on the main post I figured I could have a go at an answer, so here goes.

First off, your relationship is the wrong type and the wrong way around. As I understand it (and as I implement these things in my own work) a product belongs to a brand - a brand may have multiple products, but a product can only have one brand. So first your DB schema - you mention you have a products table with the normal columns and then the foreign key brand_id. So far so good: this is consistent with the way I interpret the relationship.

However, you then go on to show us your model. You have the Product model as hasOne Brand - but actually it belongs to a brand. You also don't define the inverse of the relationship - you need both sides to make Laravel work well. In addition to that your naming is a bit out of whack - it'll possibly work, but if we follow Laravel conventions we get the following:

In the products model: Product.php

class Product extends Eloquent
{
    public function brand()
    {
        return $this->belongsTo('Brand');
    }
}
Now the brands model: Brand.php

class Brand extends Eloquent
{
    public function products()
    {
        return $this->hasMany('Product');
    }
}
Okay so far so good. You'll notice various conventions:

Table names are plural (products, brands)
Foreign keys use the singular (brand_id)
Model names are singular and StudlyCase (so Product for a table products, Brand for table brands)
The $table property doesn't have to be specified as long as you follow Laravel conventions (i.e. table name is the plural snake_case version of the model classname
You should define the relationship both ways (one in each model, belongsTo's inverse is hasMany, there's also the pairs hasOne/belongsTo and belongsToMany/belongsToMany)
The names of the methods to retrieve the relationship are sensible - if you expect one result, make it singular (brand in Product), if you expect multiple results make it plural (products in Brand)
Use the model classnames in your relationship definitions ($this->hasMany('Brand') not $this->hasMany('brands') or any other variation
If you stick to these rules, your models can be really concise but very powerful.

Now, as for how you actually define real data, I have a feeling the code you posted may work fine (it really depends on how clever Laravel is behind the scenes), but as I suggested in my first comment, I'd ensure that I saved the $brand before calling associate(), just so that Laravel doesn't get lost working out what to do. As such I'd go for:

// create new brand and save it
$brand = new Brand;
$brand->name = "Brand 1";
$brand->save();

// create new product and save it
$product = new Product;
$product->name = "Product 1";
$product->description = "Description 1";
$product->brand()->associate($brand);
$product->save();
This way, you know you have the brand in the database with its IDs already defined before you go and use it in a relationship.

You can also define the relationship in the opposite manner, and it may be less brain-hurting to do so:

// create new product and save it
$product = new Product;
$product->name = "Product 1";
$product->description = "Description 1";
$product->save();

// create new brand and save it
$brand = new Brand;
$brand->name = "Brand 1";
$brand->save();

// now add the product to the brand's list of products it owns
$brand->products()->save($product);
You don't need to call save() on either model after that last line, as it wil automatically take the $brand's id value, place it into the $product's brand_id field, and then save the $product.

For more information see the docs on how to do this relationship inserting: http://laravel.com/docs/eloquent#one-to-many

Anyway, hopefully this post clears up a good amount of what was wrong with your code. As I said above, these are conventions, and you can go against them, but to do so you have to start putting extra code in, and it can be quite unreadable for other developers. I say if you pick a given framework, you may as well follow its conventions.
*/

	public function translate($lang)
	{
		return $this->$lang;	
	}


	public function translations()
	{
		return $this->hasMany($this->getTranslationModelNameDefault());
	}


	public function save(array $options = [])
	{
		if (!empty($options)) {

			$this->fill($options);
		}

		if (!$this->exists) {

			parent::save($options);
		}

		foreach ($this->relations as $key => $value) {

			if (!$this->exists) {

				$value->setAttribute($this->getForeignKey(), $this->id);
			}

			var_dump($value);

			$value->save();
		}

		parent::save($options);
	}


	public function fill(array $attributes)
	{
		foreach ($attributes as $key => $value) {

			if (in_array($key, $this->getSupportLocales())) {

				if (!isset($this->relations[$key])) {

					$translation = $this->createNewTranslation($key);	
					$this->relations[$key] = $translation;
				}

				$translation->fill($value);

				unset($attributes[$key]);
			}
		}

		return parent::fill($attributes);
	}


	protected function createNewTranslation($lang)
	{
		$modelName 		= $this->getTranslationModelNameDefault();
        $translation 	= new $modelName();
        $translation->fillable($this->langust);

        foreach ($this->langust as $value) {

        	$translation->$value = '';
        }

        $translation->setAttribute('lang', $lang);
        $translation->setAttribute($this->getForeignKey(), $this->id);

        return $translation;
	}


	public function __get($key)
	{
		// If the relation has been loaded already, return it
		if (array_key_exists($key, $this->relations)) {

			return $this->relations[$key];
		}

		// If the model supports the locale, load, cache and return it
		if (in_array($key, $this->getSupportLocales())) {

			$relation = $this->hasOne($this->getTranslationModelNameDefault())->whereLang($key);
			if ($relation->getResults() === null) {

				$translation = $this->createNewTranslation($key);

       			return $this->relations[$key] = $translation;
			}

			return $this->relations[$key] = $relation->getResults();
		}

		// If the attribute is set to be automatically localized
		if ($this->langust) {

			if (in_array($key, $this->langust)) {

				if (isset($this->attributes[$key])) {

					return $this->attributes[$key];
				}

				$lang = Lang::getLocale();

				return $this->$lang ? $this->$lang->$key : null;
			}
		}

		return parent::__get($key);
	}


    public function getTranslationModelNameDefault()
    {
		$pattern = Config::get('langust.model_pattern');

		$model = get_called_class();
		$model = class_basename($model);
		return str_replace('{model}', $model, $pattern);
    }


	protected function getSupportLocales()
	{
		return Config::get('langust.locales');
	}

}
