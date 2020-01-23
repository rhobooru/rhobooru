<?php

namespace App\Helpers;

class EnvironmentHelper
{
    /**
     * Persists a KV pair in the .env file.
     *
     * @param string $envKey
     * @param string $envValue
     *
     * @return void
     */
    public static function saveEnvironmentValue(string $envKey, string $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
        $str = substr($str, 0, -1);

        $handle = fopen($envFile, 'w');
        fwrite($handle, $str);
        fclose($handle);
    }

    /**
     * Reads the maximum allowed upload filesize in
     * the php environment.
     *
     * @return void
     */
    public static function getMaxUploadSize()
    {
        return min(HumanReadableHelper::toBytes(ini_get('upload_max_filesize')),
            HumanReadableHelper::toBytes(ini_get('post_max_size')));
    }

    /**
     * Checks if the max allowed upload filesize in
     * the php environment is configured correctly.
     *
     * @return void
     */
    public static function verifyMaxUploadSize()
    {
        $max_post_size;
        $max_upload_size;

        try {
            $max_post_size = ini_get('post_max_size');
        } catch (\Exception $exception) {
            throw new \Exception('Could not read `post_max_size`', 0, $exception);
        }

        try {
            $max_upload_size = ini_get('upload_max_filesize');
        } catch (\Exception $exception) {
            throw new \Exception('Could not read `upload_max_filesize`', 0, $exception);
        }

        $max_post_size = HumanReadableHelper::toBytes($max_post_size);
        $max_upload_size = HumanReadableHelper::toBytes($max_upload_size);

        if ($max_post_size < $max_upload_size) {
            throw new \Exception('`upload_max_filesize` is set higher than `post_max_size`. This is not allowed.');
        }

        if ($max_upload_size < 10 * 1024 * 1024) {
            throw new \Exception('`upload_max_filesize` is set below 10M. This may prevent many files from being uploaded.');
        }

        return true;
    }
}
