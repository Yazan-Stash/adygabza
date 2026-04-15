<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:complete_sentence_input,complete_sentence_mcq,reorder_translation'],
            'prompt' => ['required', 'string'],
            'answer' => ['required'],
            'options' => ['nullable', 'array'],
            'options.*' => ['string'],
            'explanation' => ['nullable', 'string'],
            'order' => ['required', 'integer', 'min:0'],
            'metadata' => ['nullable', 'array'],
            'metadata.word_tokens' => ['nullable', 'array'],
            'metadata.word_tokens.*.text' => ['required_with:metadata.word_tokens', 'string'],
            'metadata.word_tokens.*.translation' => ['nullable', 'string'],
            'metadata.word_tokens.*.parts' => ['nullable', 'array'],
            'metadata.word_tokens.*.parts.*.text' => ['required_with:metadata.word_tokens.*.parts', 'string'],
            'metadata.word_tokens.*.parts.*.translation' => ['nullable', 'string'],
        ];
    }
}
