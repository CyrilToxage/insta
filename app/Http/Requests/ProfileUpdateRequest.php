<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'username' => [
                'required',
                'string',
                'lowercase',
                'max:30',
                'alpha_dash',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Le nom d\'utilisateur est requis.',
            'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
            'username.alpha_dash' => 'Le nom d\'utilisateur ne peut contenir que des lettres, des chiffres, des tirets et des underscores.',
            'username.max' => 'Le nom d\'utilisateur ne peut pas dépasser 30 caractères.',
            'bio.max' => 'La biographie ne peut pas dépasser 1000 caractères.',
            'profile_photo.image' => 'Le fichier doit être une image.',
            'profile_photo.mimes' => 'L\'image doit être de type : jpeg, png, jpg.',
            'profile_photo.max' => 'L\'image ne doit pas dépasser 2Mo.',
        ];
    }
}
