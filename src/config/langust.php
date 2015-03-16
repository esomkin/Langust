<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | The default locale
    |
    */
    'default'       => 'en',

    // The fallback locale for translations
    // If null, the default locale is used
    'fallback'      => 'en',

    //The available locales
    //Contains an array with the applications available locales
    'locales'       => ['en', 'fr', 'es'],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | The pattern Polyglot should follow to find the Lang classes
    | Examples are "Lang\{model}", "{model}Lang", where {model}
    | will be replaced by the model's name
    |
    */

    'model_pattern' => 'App\Models\{model}Lang',

];