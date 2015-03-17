<?php namespace Goodvin\Langust;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

trait Langust
{

	// http://stackoverflow.com/questions/21814049/laravel-eloquent-example-insert-data-to-database

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
