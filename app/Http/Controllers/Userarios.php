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
        try {
            $validateRequest = $request->validate([
                'nome' => 'required|string|min:5',
                'email' => 'required|email|unique:usuarios,email',
                'telefone' => 'required|string|min:7',
                'descricao' => 'nullable|string',
            ]);

            Usuario::create($validateRequest);

            return response()->json(['message' => 'Usuário cadastrado com sucesso'], 201);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'erros' => $error->errors()
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            
            $validateRequest = $request->validate([
                'nome' => 'required|string|min:5',
                'email' => 'required|email|unique:usuarios,email,' . $id,
                'telefone' => 'required|string|min:7',
                'descricao' => 'nullable|string',
            ]);

            $usuario->update($validateRequest);

            return response()->json(['message' => 'Usuário atualizado com sucesso'], 201);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'erros' => $error->errors()
            ], 422);
        } catch (\Exception $error) {
            return response()->json(['error' => 'Usuário não encontrado'], 201);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            return response()->json(['message' => 'Usuário deletado com sucesso'], 201);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'erros' => $error->errors()
            ], 422);
        } catch (\Exception $error) {
            return response()->json(['error' => 'Usuário não encontrado'], 201);
        }
    }
}
