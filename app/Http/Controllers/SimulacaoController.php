<?php

namespace App\Http\Controllers;

use App\Services\SimulacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SimulacaoController
{
    public function getInstituicoes(SimulacaoService $service)
    {
        $resultado = $service->lerArquivoJson('instituicoes.json');
        return response()->json($resultado);
    }

    public function getConvenios(SimulacaoService $service)
    {
        $resultado = $service->lerArquivoJson('convenios.json');
        return response()->json($resultado);
    }

    public function simularCredito(Request $request, SimulacaoService $service)
    {
        $validator = Validator::make($request->all(), [
            'valor_emprestimo' => 'required|numeric|gt:0',
            'instituicoes' => 'required|array|min:1',
            'convenios' => 'array',
            'parcela' => 'numeric|gt:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        $dados = $request->only(['valor_emprestimo', 'instituicoes', 'convenios', 'parcela']);
        $resultado = $service->simular($dados);

        return response()->json($resultado);
    }
}
