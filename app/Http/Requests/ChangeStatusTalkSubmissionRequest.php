<?php

namespace App\Http\Requests;

use App\Enum\TalkSubmissionStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusTalkSubmissionRequest extends FormRequest
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
            'status' => ['required', new \Illuminate\Validation\Rules\Enum(TalkSubmissionStatus::class)],

        ];
    }
}
