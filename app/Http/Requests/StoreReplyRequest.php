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
            'message'    => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required'  => 'Reply message is required.',
            'attachment.mimes'  => 'Only JPG, PNG, GIF and PDF files are allowed.',
            'attachment.max'    => 'Attachment must not exceed 5 MB.',
        ];
    }
}