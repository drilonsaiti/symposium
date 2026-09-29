<?php

namespace App\Http\Requests;

use App\Enum\ReviewRecommendation;
use App\Models\ConferenceTalkReview;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreConferenceTalkReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [ConferenceTalkReview::class, $this->route('conference')]) ?? false;
    }

    public function rules(): array
    {
        return [
            'score' => ['required', 'integer', 'between:1,5'],
            'recommendation' => ['required', new Enum(ReviewRecommendation::class)],
            'note' => ['nullable', 'string'],
        ];
    }
}
