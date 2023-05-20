<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->user()->id
        ]);
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'string'],
            'title' => ['required', 'string', 'max:225'],
            'user_id' => ['exists:users,id'],
            'status' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
            'expire_date' => ['nullable', 'date', 'after:tomorrow'],
            'questions' => ['nullable' , 'array']
        ];
    }
}
