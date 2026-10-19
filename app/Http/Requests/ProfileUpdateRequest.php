<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Normalisasi input user sebelum divalidasi ke database
        if ($this->has('nomor_hp')) {
            $this->merge([
                'nomor_hp' => PhoneNumber::normalize($this->input('nomor_hp'))
            ]);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'nomor_hp' => [
                'required',
                'string',
                'max:20',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}