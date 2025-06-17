<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArquivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'arquivo' => [
                'required',
                'file',
            ]
        ];
    }

    public function messages()
    {
        return [
            'arquivo.required' => 'O campo arquivo é obrigatorio',
            'arquivo.file' => 'Deve ser enviado um arquivo',
        ];
    }
}
