<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
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
            'resumeText' => ['nullable', 'required_without:resumeFile', 'string', 'min:50', 'max:10000'],
            'resumeFile' => ['nullable', 'required_without:resumeText', 'file', 'mimes:pdf,doc,docx,txt', 'max:5120'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'resumeText.required_without' => 'Please provide resume text or upload a file.',
            'resumeText.min' => 'Resume text must be at least 50 characters.',
            'resumeText.max' => 'Resume text must not exceed 10,000 characters.',
            'resumeFile.required_without' => 'Please provide resume text or upload a file.',
            'resumeFile.mimes' => 'Resume file must be a PDF, DOC, DOCX, or TXT file.',
            'resumeFile.max' => 'Resume file must not exceed 5MB.',
        ];
    }
}
