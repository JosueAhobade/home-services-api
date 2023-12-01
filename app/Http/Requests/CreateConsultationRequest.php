<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateConsultationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'motif'=> 'required',
            'duree_mal' => 'required',
            'treatment' => 'required',
            'consulter_medecin_sepe' => 'required',
            'current_treatment' => 'required',
        ];
    }

    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Erreur de validation',
            'errorList' => $validator->errors()
        ], 401));
    }

    public function messages(){
        return [
            'motif.required' => 'Un motif de consultation est requis',
            'duree_mal.required' => 'La durrée du mal est requis',
            'treatment.required' => 'treatment  est requis',
            'consulter_medecin_sepe.required' => 'consulter_medecin_sepe est requis',
            'current_treatment.required' => 'current_treatment  est requis',
        ];
    }
}
