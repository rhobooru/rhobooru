<?php

namespace App\Exceptions;

class MediaNotUploadedException extends \Exception
{
    protected $message = "Media is not finished uploading";
}
