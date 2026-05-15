<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Message is required only if no attachment is provided
            'message'    => ['nullable', 'string'],
            'attachment' => ['nullable', 'string'], 
        ];
    }

    public function messages(): array
    {
        return [
            'message.required_without'    => 'Please write a message or attach a file.',
            'attachment.required_without'  => 'Please write a message or attach a file.',
            'attachment.mimes'             => 'Only JPG, PNG, GIF and PDF files are allowed.',
            'attachment.max'               => 'Attachment must not exceed 5 MB.',
        ];
    }
}