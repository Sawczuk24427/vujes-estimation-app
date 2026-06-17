<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEstimationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

        'title' => [
                'required',
                'string',
                Rule::unique('estimations')
                    ->where('project_id', $request->project_id)
                    ->ignore($estimation->id),
            ],
            'type' => ['required', 'in:fixed,hourly'],
            'price' => ['required_if:type,fixed', 'nullable', 'numeric', 'min:0'],
            'hours' => ['required_if:type,hourly', 'nullable', 'integer', 'min:1'],
            'hourly-rate' => ['required_if:type,hourly', 'nullable', 'numeric', 'min:0'],
            'project_id' => ['required', 'exists:projects,id']
        
            
        ];
    }
}
