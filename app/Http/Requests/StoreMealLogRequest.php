<?php

namespace App\Http\Requests;

use App\Enums\MealType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMealLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meal_type' => ['required', new Enum(MealType::class)],
            'photo' => ['required', 'image', 'max:5120'],
            'date' => ['nullable', 'date'],
        ];
    }
}
