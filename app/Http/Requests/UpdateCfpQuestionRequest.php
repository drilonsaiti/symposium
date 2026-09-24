<?php

namespace App\Http\Requests;

use App\Enum\QuestionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCfpQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(
            'manageQuestions',
            $this->route('question')
        ) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => ['required','string','max:255'],
            'type' => ['required',new \Illuminate\Validation\Rules\Enum(QuestionType::class)],
            'required' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'required' => $this->boolean('required'),
        ]);
    }
}
