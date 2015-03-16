<?php namespace Goodvin\Langust;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

trait Langust
{


	/**
	 * Handle dynamic method calls for locale relations.
	 *
	 * @param  string $method
	 * @param  array  $parameters
	 *
	 * @return mixed
	 */
	/*
	public function __call($method, $parameters)
	{
		// If the model supports the locale, load it
		if (in_array($method, $this->getSupportLocales())) {

			return $this->hasOne($this->getTranslationModelNameDefault())->whereLang($method);
		}
		return parent::__call($method, $parameters);
	}
	*/

	public function save(array $options = [])
	{
		foreach ($this->relations as $key => $value) {

			var_dump($value);
		}

		parent::save();
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

				$relation = $this->hasOne($this->getTranslationModelNameDefault())->whereLang(Config::get('langust.fallback'));
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
