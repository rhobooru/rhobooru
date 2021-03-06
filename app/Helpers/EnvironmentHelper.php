<?php

namespace App\Helpers;

class EnvironmentHelper
{
    /**
     * Persists a KV pair in the .env file.
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public static function saveEnvironmentValue(string $key, string $value)
    {
        $env_file = app()->environmentFilePath();
        $str = file_get_contents($env_file);

        $str .= "\n"; // In case the key is the last line without \n
        $key_position = strpos($str, "{$key}=");
        $end_of_line_position = strpos($str, PHP_EOL, $key_position);
        $old_line = substr($str, $key_position, $end_of_line_position - $key_position);
        $str = str_replace($old_line, "{$key}={$value}", $str);
        $str = substr($str, 0, -1);

        $handle = fopen($env_file, 'w');
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
     * @return bool
     */
    public static function verifyMaxUploadSize(): bool
    {
        $max_post_size;
        $max_upload_size;

        try {
            $max_post_size = HumanReadableHelper::toBytes(ini_get('post_max_size'));
            $max_upload_size = HumanReadableHelper::toBytes(ini_get('upload_max_filesize'));
        } catch (\Exception $exception) {
            throw new \Exception('Could not read PHP configuration', 0, $exception);
        }

        if ($max_post_size < $max_upload_size) {
            throw new \Exception('`upload_max_filesize` is set higher than `post_max_size`. This is not allowed.');
        }

        if ($max_upload_size < 10 * 1024 * 1024) {
            throw new \Exception('`upload_max_filesize` is set below 10M. This may prevent many files from being uploaded.');
        }

        return true;
    }
}
