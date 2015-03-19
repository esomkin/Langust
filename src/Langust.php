<?php namespace Goodvin\Langust;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

trait Langust 
{

	/**
	 * Get the relation with translation model
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	protected function translations() 
	{
		return $this->hasOne($this->getTranslationModelName());
	}


	/**
	 * Check if translation exists in relations array
	 *
	 * @param $locale
	 *
	 * @return boolean
	 */
	public function hasTranslation($locale = null) 
	{
        $locale = $locale ? : Lang::getLocale();

        if (isset($this->relations[$locale])) {

        	return true;
        }

        return false;
    }


	/**
	 * Formate translation model name from pattern
	 *
	 * @return string Model name
	 */
    protected function getTranslationModelName() 
    {
		$pattern 	= Config::get('langust.model_pattern');
		$model 		= get_called_class();
		$model 		= class_basename($model);

		return str_replace('{model}', $model, $pattern);
    }


	/**
	 * Get the langust supported locales
	 *
	 * @return array
	 */
	protected function getSupportLocales() 
	{
		return Config::get('langust.locales');
	}


	/**
	 * Get the locale field name
	 *
	 * @return string
	 */
	protected function getLocaleFieldName()
	{
		return Config::get('langust.locale_field');
	}


	/**
	 * Create new empty translation & save it
	 *
	 * @param $locale
	 *
	 * @return string
	 */
	protected function createEmptyTranslation($locale) 
	{
		$modelName 		= $this->getTranslationModelName();
        $translation 	= new $modelName();
        $translation->fillable($this->langust);

        foreach ($this->langust as $value) {

        	$translation->setAttribute($value, '');
        }

        $translation->setAttribute($this->getLocaleFieldName(), $locale);

        if (!$this->exists) {

        	$this->save();
        }

        $this->translations()->save($translation);

        return $translation;
	}	


	/**
	 * Get a localized attribute
	 *
	 * @param string $key The attribute
	 *
	 * @return mixed
	 */
	public function __get($key) 
	{
		if ($this->hasTranslation($key)) {

			return $this->relations[$key];
		}

		if (in_array($key, $this->getSupportLocales())) {

			$relation = $this->translations()->whereLang($key);

			if ($relation->getResults() === null) {

				$translation = $this->createEmptyTranslation($key);

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


    /**
     * Alias for __get()
     */
	public function translate($locale) 
	{
		return $this->$locale;	
	}
	

	/**
	 * Set a localized attribute
	 *
	 * @param string $key The attribute
	 * @param string $value The value of attribute
	 *
	 * @return mixed
	 */
	public function __set($key, $value)
	{
		if ($this->langust) {

			if (in_array($key, $this->langust)) {

				$locale = Lang::getLocale();
				return $this->$locale->$key = $value;
			}
		}

		return parent::__set($key, $value);
	}


	/**
	 * Fill the model with localized attributes
	 *
	 * @param array $attributes
	 *
	 * @return mixed
	 */
	public function fill(array $attributes) 
	{
		foreach ($attributes as $key => $value) {

			if (in_array($key, $this->getSupportLocales())) {

				foreach ($value as $fieldName => $fieldValue) {
				
					if (in_array($fieldName, $this->langust)) {

						$this->$key->$fieldName = $fieldValue;
					}
				}

				unset($attributes[$key]);
			}
		}

		return parent::fill($attributes);
	}


	/**
	 * Save the model with localized attributes
	 *
	 * @param array $attributes
	 *
	 * @return mixed
	 */
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

}
