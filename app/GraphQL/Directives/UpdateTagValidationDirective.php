<?php

namespace App\GraphQL\Directives;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Schema\Directives\ValidationDirective;

class UpdateTagValidationDirective extends ValidationDirective
{
    /**
     * Name of the directive.
     *
     * @return string
     */
    public function name(): string
    {
        return 'updatetagValidation';
    }

    /**
     * @return mixed[]
     */
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'name' => ['sometimes', Rule::unique('tags', 'name')->ignore($this->args['id'], 'id')],
        ];
    }
}