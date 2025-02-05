<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Usuario;

class Userarios extends Controller
{
    public function view()
    {
        $usuarios = Usuario::all();
        
        return response()->json($usuarios);
    }

    public function create(Request $request)
    {
        $usuario = array(
            'nome' => $request->nome,
            'email' => $request->email,
            'descricao' => $request->descricao,
            'telefone' => $request->telefone,
        );

        Usuario::create($usuario);

        return 'Usuário criado';
    }
}
