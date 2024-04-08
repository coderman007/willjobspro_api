<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'skill_category_id' => 'required|exists:skill_categories,id',
            'name' => 'required|string|unique:skills,name,' . $this->route('skill')->id . ',id,skill_category_id,' . $this->input('skill_category_id'),
            'description' => 'nullable|string'
        ];
    }
}
