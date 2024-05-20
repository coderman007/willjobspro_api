<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateBenefitRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
        ];
    }
}

