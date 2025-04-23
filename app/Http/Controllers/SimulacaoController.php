<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimulacaoController extends Controller
{
    private function lerArquivoJson(string $nomeArquivo): array
    {
        $caminhoArquivo = 'data/' . $nomeArquivo;
        if (!Storage::exists($caminhoArquivo)) {
            return [];
        }
        $conteudo = Storage::get($caminhoArquivo);
        return json_decode($conteudo, true) ?? [];
    }

    public function getInstituicoes()
    {
        $instituicoes = $this->lerArquivoJson('instituicoes.json');
        return response()->json($instituicoes);
    }

    public function getConvenios()
    {
        $convenios = $this->lerArquivoJson('convenios.json');
        return response()->json($convenios);
    }

    public function simularCredito(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'valor_emprestimo' => 'required|numeric|gt:0',
            'instituicoes' => 'required|array|min:1',
            'convenios' => 'array',
            'parcela' => 'numeric|gt:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $dadosSimulacao = $request->only(['valor_emprestimo', 'instituicoes', 'convenios', 'parcela']);

        $taxas = $this->lerArquivoJson('taxas_instituicoes.json');
        $resultados = [];

        foreach ($dadosSimulacao['instituicoes'] as $instituicao) {
            $resultados[$instituicao] = [];

            $taxasFiltradas = Arr::where($taxas, function ($taxa) use ($instituicao, $dadosSimulacao) {
                $matchInstituicao = $taxa['instituicao'] === $instituicao;
                $matchConvenio = empty($dadosSimulacao['convenios']) || in_array($taxa['convenio'], $dadosSimulacao['convenios']);
                $matchParcela = empty($dadosSimulacao['parcela']) || $taxa['parcelas'] == $dadosSimulacao['parcela'];

                return $matchInstituicao && $matchConvenio && $matchParcela;
            });

            foreach ($taxasFiltradas as $taxa) {
                $valorParcela = $dadosSimulacao['valor_emprestimo'] * $taxa['coeficiente'];
                $resultados[$instituicao][] = [
                    'taxa' => $taxa['taxaJuros'],
                    'parcelas' => $taxa['parcelas'],
                    'valor_parcela' => round($valorParcela, 2),
                    'convenio' => $taxa['convenio']
                ];
            }

            if (empty($resultados[$instituicao])) {
                unset($resultados[$instituicao]);
            }
        }

        return response()->json($resultados);
    }
}