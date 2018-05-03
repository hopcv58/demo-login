<?php
/**
 * Created by ASUS.
 * Date: 7/18/2017
 * Time: 8:47 AM
 */

namespace App\Components\Utilities\Transformer;

use Route;

class TransformerHelper
{
    /**
     * @var string
     */
    protected static $class;

    /**
     * @var string
     */
    protected static $method;

    /**
     * Get method of child class.
     */
    public static function getTransformerMethod ()
    {
        return self::$method ?: (self::$method = Route::getCurrentRoute()->getActionMethod());
    }

    /**
     * Get transformer Class.
     */
    public static function getTransformerClass ()
    {
        if (empty(self::$class)) {
            // Get route action string. Ex: \App\Http\Controller\HomeController@index.
            list($class) = explode('@', Route::getCurrentRoute()->getActionName());

            $class = str_replace('App\Http\Controllers', 'App\Transformers', $class);

            self::$class =  str_replace('Controller', 'Transformer', $class);
        }

        return self::$class;
    }
}