<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    private $sucesso;
    private $conteudoResposta;
    private $statusResposta;
    private $paginaAtual;
    private $itensPorPagina;

    public function __construct(
        bool $sucesso, 
        $conteudoResposta, 
        int $statusResposta = Response::HTTP_OK,
        int $paginaAtual = null,
        int $itensPorPagina = null
    ) {
        $this->sucesso = $sucesso;
        $this->conteudoResposta = $conteudoResposta;
        $this->paginaAtual = $paginaAtual;
        $this->statusResposta = $statusResposta;
        $this->itensPorPagina = $itensPorPagina;
    }

    public function getResponse()
    {
        $resposta = [
            "sucesso" => $this->sucesso,
            "conteudoResposta" => $this->conteudoResposta,
            "statusResposta" => $this->statusResposta
        ];

        if(!is_null($this->paginaAtual)) {
            $resposta["paginaAtual"] = $this->paginaAtual;
            $resposta["itensPorPagina"] = $this->itensPorPagina;
        }
            
        return new JsonResponse($resposta);
    }

    public static function fromError(\Throwable $erro)
    {
        return new self(false, ['mensagem' => $erro->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR, null);
    }
}