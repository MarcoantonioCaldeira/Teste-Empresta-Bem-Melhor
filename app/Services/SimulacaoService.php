<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class SimulacaoService
{
    public function lerArquivoJson(string $nomeArquivo): array
    {
        $caminho = storage_path('app/data/' . $nomeArquivo);

        if (!file_exists($caminho)) {
            return [];
        }
    
        $conteudo = file_get_contents($caminho);
        return json_decode($conteudo, true) ?? [];
    }

    public function simular(array $dadosSimulacao): array
    {
        $taxas = $this->lerArquivoJson('taxas_instituicoes.json');
        $resultados = [];

        foreach ($dadosSimulacao['instituicoes'] as $instituicao) {
            $taxasFiltradas = $this->filtrarTaxas(
                $taxas,
                $instituicao,
                $dadosSimulacao['convenios'] ?? [],
                $dadosSimulacao['parcela'] ?? null
            );

            foreach ($taxasFiltradas as $taxa) {
                $valorParcela = $this->calcularValorParcela($dadosSimulacao['valor_emprestimo'], $taxa['coeficiente']);
                $resultados[$instituicao][] = [
                    'taxa' => $taxa['taxaJuros'],
                    'parcelas' => $taxa['parcelas'],
                    'valor_parcela' => $valorParcela,
                    'convenio' => $taxa['convenio']
                ];
            }

            if (empty($resultados[$instituicao])) {
                unset($resultados[$instituicao]);
            }
        }

        return $resultados;
    }

    private function filtrarTaxas(array $taxas, string $instituicao, array $convenios, ?int $parcela): array
    {
        return Arr::where($taxas, function ($taxa) use ($instituicao, $convenios, $parcela) {
            return $taxa['instituicao'] === $instituicao
                && (empty($convenios) || in_array($taxa['convenio'], $convenios))
                && (empty($parcela) || $taxa['parcelas'] == $parcela);
        });
    }

    private function calcularValorParcela(float $valor, float $coeficiente): float
    {
        return round($valor * $coeficiente, 2);
    }
}
