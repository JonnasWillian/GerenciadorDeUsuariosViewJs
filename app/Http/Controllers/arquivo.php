<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ArquivoRequest;
use App\Service\ArquivoService;
use App\Models\arquivo AS ArquivoModel;

class arquivo extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $arquivoService;

    public function __construct(ArquivoService $arquivoService){
        $this->arquivoService = $arquivoService;
    }

    public function index()
    {
        $arquivos = ArquivoModel::where('user_id', $request->user_id)->get();
        
        return response()->json($arquivos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArquivoRequest $request)
    {
        $caminho = $this->arquivoService->salveFile($request->file('arquivo'));

        return response()->json([
            'success' => true,
            'Arquivo salvo:'  => $caminho
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
