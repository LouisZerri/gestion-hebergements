<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest gérant la validation des données pour la création ou la mise à jour d’un hôtel.
 */
class HotelRequest extends FormRequest
{
    /**
     * Autorise la requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation appliquées aux données de la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'zip_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
            'description' => 'nullable|string|max:5000',
            'max_capacity' => 'required|integer|min:1|max:200',
            'price_per_night' => 'required|numeric|min:0|max:9999999.99',
        ];
    }

    /**
     * Messages d’erreur personnalisés pour les validations.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'hôtel est obligatoire',
            'address_1.required' => 'L\'adresse est obligatoire',
            'zip_code.required' => 'Le code postal est obligatoire',
            'city.required' => 'La ville est obligatoire',
            'country.required' => 'Le pays est obligatoire',
            'longitude.required' => 'La longitude est obligatoire',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180',
            'latitude.required' => 'La latitude est obligatoire',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90',
            'description.max' => 'La description ne peut pas dépasser 5000 caractères',
            'max_capacity.required' => 'La capacité maximale est obligatoire',
            'max_capacity.max' => 'La capacité maximale ne peut pas dépasser 200',
            'price_per_night.required' => 'Le prix par nuit est obligatoire',
            'price_per_night.min' => 'Le prix par nuit doit être positif',
        ];
    }
}
