<?php namespace Goodvin\Langust;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

trait Langust 
{

	public function translations() 
	{
		return $this->hasOne($this->getTranslationModelName());
	}


	public function hasTranslation($locale = null) 
	{
        $locale = $locale ? : Lang::getLocale();

        if (isset($this->relations[$locale])) {

        	return true;
        }

        return false;
    }


    public function getTranslationModelName() 
    {
		$pattern 	= Config::get('langust.model_pattern');
		$model 		= get_called_class();
		$model 		= class_basename($model);

		return str_replace('{model}', $model, $pattern);
    }


	protected function getSupportLocales() 
	{
		return Config::get('langust.locales');
	}


	public function __get($key) 
	{
		if (array_key_exists($key, $this->relations)) {

			return $this->relations[$key];
		}

		if (in_array($key, $this->getSupportLocales())) {

			$relation = $this->translations()->whereLang($key);
			if ($relation->getResults() === null) {

				$translation = $this->createNewTranslation($key);

       			return $this->relations[$key] = $translation;
			}

			return $this->relations[$key] = $relation->getResults();
		}

		if ($this->langust) {

			if (in_array($key, $this->langust)) {

				if (isset($this->attributes[$key])) {

					return $this->attributes[$key];
				}

				$locale = Lang::getLocale();

				return $this->$locale ? $this->$locale->$key : null;
			}
		}

		return parent::__get($key);
	}


	public function translate($locale) 
	{
		return $this->$locale;	
	}


	public function save(array $attributes = []) 
	{
		if (!empty($attributes)) {

			$this->fill($attributes);
		}

		foreach ($this->relations as $value) {

			$value->save();
		}

		parent::save($attributes);
	}


	public function fill(array $attributes) 
	{
		foreach ($attributes as $key => $value) {

			if (in_array($key, $this->getSupportLocales())) {

				if (!isset($this->relations[$key])) {

					$translation = $this->createNewTranslation($key);
				}

				foreach ($value as $fieldName => $fieldValue) {

					$translation->setAttribute($fieldName, $fieldValue);
				}

				$this->relations[$key] = $translation;
				unset($attributes[$key]);
			}
		}

		return parent::fill($attributes);
	}

	protected function createNewTranslation($locale) 
	{
		$modelName 		= $this->getTranslationModelName();
        $translation 	= new $modelName();
        $translation->fillable($this->langust);

        foreach ($this->langust as $value) {

        	$translation->setAttribute($value, '');
        }

        $translation->setAttribute('lang', $locale);

        if (!$this->exists) {

        	$this->save();
        }
        $this->translations()->save($translation);

        return $translation;
	}

}
