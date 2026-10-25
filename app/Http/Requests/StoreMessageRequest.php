<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Write a message before sending.',
        ];
    }
}
