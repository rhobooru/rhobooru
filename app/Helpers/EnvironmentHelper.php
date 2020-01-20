<?php

namespace App\Helpers;

use App\Helpers\HumanReadableHelper;

class EnvironmentHelper
{
    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
    
        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
        $str = substr($str, 0, -1);
    
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }

    public static function getMaxUploadSize()
    {
        return min(HumanReadableHelper::toBytes(ini_get('upload_max_filesize')),
            HumanReadableHelper::toBytes(ini_get('post_max_size')));
    }

    public static function verifyMaxUploadSize()
    {
        $max_post_size;
        $max_upload_size;

        try
        {
            $max_post_size = ini_get('post_max_size');
        }
        catch(\Exception $e)
        {
            throw new \Exception("Could not read `post_max_size`", 0, $e);
        }

        try
        {
            $max_upload_size = ini_get('upload_max_filesize');
        }
        catch(\Exception $e)
        {
            throw new \Exception("Could not read `upload_max_filesize`", 0, $e);
        }

        $max_post_size = HumanReadableHelper::toBytes($max_post_size);
        $max_upload_size = HumanReadableHelper::toBytes($max_upload_size);

        if($max_post_size < $max_upload_size)
        {
            throw new \Exception('`upload_max_filesize` is set higher than `post_max_size`. This is not allowed.');
        }

        if($max_upload_size < 10 * 1024 * 1024)
        {
            throw new \Exception('`upload_max_filesize` is set below 10M. This may prevent many files from being uploaded.');
        }

        return true;
    }
}