<?php

namespace App\GraphQL\Mutations;

use \App\Models\Record;

class Upload
{
    /**
     * Upload a file, store it on the server and return the path.
     *
     * @param  mixed  $root
     * @param  mixed[]  $args
     * @return string|null
     */
    public function __invoke($root, array $args): ?string
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $args['file'];
        $record = Record::withoutGlobalScopes()->find($args['id']);

        $path = $record->uploadFile($file);

        return $path;
    }
}