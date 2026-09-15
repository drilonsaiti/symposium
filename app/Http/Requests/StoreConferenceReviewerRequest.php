<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\UserExistsByUsernameOrEmail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreConferenceReviewerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manageReviewers', $this->route('conference')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reviewer' => [
                'required',
                'string',
                new UserExistsByUsernameOrEmail(),
            ]
        ];
    }
}
