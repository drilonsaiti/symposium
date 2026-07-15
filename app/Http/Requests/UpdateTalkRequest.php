<?php

namespace App\Http\Requests;

use App\Enum\TalkType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTalkRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'length' => ['required', 'integer', 'min:20'],
            'type' => ['required', Rule::enum(TalkType::class)],
            'abstract' => ['nullable', 'string'],
            'organizer_notes' => ['nullable', 'string'],
        ];
    }
}
