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
                'max: 3062',
                'mimes: pdf,jpeg,png'
            ]
        ];
    }

    public function messages()
    {
        return [
            'arquivo.required' => 'O campo arquivo é obrigatorio',
            'arquivo.file' => 'Deve ser enviado um arquivo',
            'arquivo.max' => 'O arquivo deve ter no máximo 3mb',
            'arquivo.mimes' => 'Suportamos apenas os arquivos do tipo pdf,jpeg ou png',
        ];
    }
}
