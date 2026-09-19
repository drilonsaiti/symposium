<?php

namespace App\Http\Requests;

use App\Models\CfpQuestion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTalkSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'bio_id' => ['nullable', 'integer',
                Rule::exists('bios', 'id')
                    ->where(fn($query) => $query->where('user_id', $this->user()->id)),],
            'answers' => ['array'],
            ...$this->cfpAnswerRules(),
        ];
    }


    public function cfpAnswerRules(): array
    {
        $conference = $this->route('conference');

        return $conference->cfpQuestions()
            ->active()
            ->get()
            ->mapWithKeys(function (CfpQuestion $question) {
               return [
                   "answers.$question->id" => $question->required
                   ? ['required','string'] : ['nullable', 'string'],
               ];
            })
            ->toArray();
    }
}
