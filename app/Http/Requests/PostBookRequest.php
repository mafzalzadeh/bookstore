<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostBookRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'isbn'=>'required|digits:13|unique:books',
            'title'=>'required|string|min:3',
            'description'=>'required|string|min:5',
            'authors'=>'required|array|min:1',
            'authors.*'=>'required|integer|exists:authors,id',
        ];
    }
}
