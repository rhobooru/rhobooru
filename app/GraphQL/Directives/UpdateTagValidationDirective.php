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
     *
     * @codeCoverageIgnore
     */
    public function name(): string
    {
        return 'updateTagValidation';
    }

    /**
     * Validation rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'name' => [
                'sometimes',
                Rule::unique('tags', 'name')
                    ->ignore($this->args['id'], 'id'),
            ],
        ];
    }
}
