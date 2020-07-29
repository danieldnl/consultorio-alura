<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class ExtratorDadosRequest
{
    private function buscarDadosRequest(Request $request)
    {
        $queryString = $request->query->all();
        $ordenacao = array_key_exists("sort", $queryString) ? $queryString["sort"] : null;
        unset($queryString["sort"]);
        $paginaAtual = array_key_exists("page", $queryString) ? $queryString["page"] : 1;
        unset($queryString["page"]);
        $itensPorPagina = array_key_exists("itensPorPagina", $queryString) ? $queryString["itensPorPagina"] : 5;
        unset($queryString["itensPorPagina"]);

        return [$queryString, $ordenacao, $paginaAtual, $itensPorPagina];
    }

    public function buscarDadosOrdenacao(Request $request)
    {
        [,$ordenacao,,] = $this->buscarDadosRequest($request);

        return $ordenacao;
    }

    public function buscarDadosFiltro(Request $request)
    {
        [$filtros,,,] = $this->buscarDadosRequest($request);

        return $filtros;
    }

    public function buscarDadosPaginacao(Request $request)
    {
        [,,$paginaAtual, $itensPorPagina] = $this->buscarDadosRequest($request);

        return [$paginaAtual, $itensPorPagina];
    }
}
