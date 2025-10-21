<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'faculty');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|in:document,video,audio,image,link',
            'description' => 'nullable|string|max:1000',
            'is_downloadable' => 'boolean',
            'order' => 'integer|min:0',
        ];

        if ($this->type == 'link') {
            $rules['link_url'] = 'required|url|max:500';
        } else {
            $rules['file'] = 'required|file|max:51200'; // 50MB max

            // Add specific file type validation based on material type
            switch ($this->type) {
                case 'document':
                    $rules['file'] .= '|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,rtf';
                    break;
                case 'video':
                    $rules['file'] .= '|mimes:mp4,avi,mov,wmv,flv,webm';
                    break;
                case 'audio':
                    $rules['file'] .= '|mimes:mp3,wav,ogg,m4a,aac';
                    break;
                case 'image':
                    $rules['file'] .= '|mimes:jpg,jpeg,png,gif,bmp,svg,webp';
                    break;
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The material title is required.',
            'title.max' => 'The material title cannot exceed 255 characters.',
            'type.required' => 'Please select a material type.',
            'type.in' => 'Invalid material type selected.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'file.required' => 'Please select a file to upload.',
            'file.max' => 'The file size cannot exceed 50MB.',
            'file.mimes' => 'Invalid file type for the selected material type.',
            'link_url.required' => 'The URL is required for link materials.',
            'link_url.url' => 'Please enter a valid URL.',
            'link_url.max' => 'The URL cannot exceed 500 characters.',
            'order.integer' => 'The order must be a number.',
            'order.min' => 'The order cannot be negative.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'link_url' => 'URL',
            'is_downloadable' => 'downloadable option',
        ];
    }
}
