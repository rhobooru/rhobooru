<?php

namespace App\Helpers;

class MetaHelper
{
    /**
     * Returns a list of all classes in the models directory.
     * 
     * @return  Array
     */
    public static function getModels()
    {
        $dir = app_path('Models');
        $files = scandir($dir);

        $models = array();

        foreach($files as $file) {
            if ($file == '.' || $file == '..' || !preg_match('|\.php|', $file)) 
                continue;

            $models[] = preg_replace('|\.php$|', '', $file);
        }

        return $models;
    }
}