<?php

namespace App\Controller;

use App\Helper\EntidadeFactory;
use App\Helper\ResponseFactory;
use App\Helper\ExtratorDadosRequest;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    protected $entidadeFactory;
    protected $repositorio;
    protected $entityManager;
    protected $dadosRequest;
    protected $cache;
    protected $logger;

    public function __construct(
        EntityManagerInterface $entityManager, 
        EntidadeFactory $entidadeFactory, 
        ObjectRepository $repositorio,
        ExtratorDadosRequest $dadosRequest,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    )
    {
        $this->entityManager = $entityManager;
        $this->entidadeFactory = $entidadeFactory;
        $this->repositorio = $repositorio;
        $this->dadosRequest = $dadosRequest;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function novo(Request $request)
    {
        $entidade = $this->entidadeFactory->criar($request->getContent());
        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        $cacheItem = $this->cache->getItem($this->cachePrefix() . $entidade->getId());
        $cacheItem->set($entidade);
        $this->cache->save($cacheItem);

        $this->logger->notice(
            "Novo registro de {entidade} adicionado com o id {id}", [
            "entidade" => $entidade,
            "id" => $entidade->getId()
        ]);

        return new JsonResponse($entidade);
    }

    public function buscarTodos(Request $request)
    {
        $ordenacao = $this->dadosRequest->buscarDadosOrdenacao($request);
        $filtros = $this->dadosRequest->buscarDadosFiltro($request);
        [$paginaAtual, $itensPorPagina] = $this->dadosRequest->buscarDadosPaginacao($request);
        $offset = ($paginaAtual - 1) * $itensPorPagina;
        $lista = $this->repositorio->findBy($filtros, $ordenacao, $itensPorPagina, $offset);
        $resposta = new ResponseFactory(true, $lista, Response::HTTP_OK, $paginaAtual, $itensPorPagina);
        return $resposta->getResponse();
    }

    public function buscarPorId($id)
    {
        $entidade = $this->cache->hasItem($this->cachePrefix() . $id) ?
            $this->cache->getItem($this->cachePrefix() . $id)->get() :
            $this->repositorio->find($id);

        $codigoRetorno = is_null($entidade) ? Response::HTTP_NO_CONTENT : 200;
        $resposta = new ResponseFactory(true, $entidade, $codigoRetorno);
        return $resposta->getResponse();
    }

    public function atualizar(int $id, Request $request)
    {
        $novosDados = $this->entidadeFactory->criar($request->getContent());
        $entidade = $this->repositorio->find($id);

        if (is_null($entidade)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->atualizarEntidadeExistente($entidade, $novosDados);
        $this->entityManager->flush();

        $cacheItem = $this->cache->getItem($this->cachePrefix() . $id);
        $cacheItem->set($entidade);
        $this->cache->save($cacheItem);

        return new JsonResponse($entidade);
    }

    public function remover(int $id)
    {
        $entidade = $this->repositorio->find($id);
        $this->entityManager->remove($entidade);
        $this->entityManager->flush();
        $this->cache->deleteItem($this->cachePrefix() . $id);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    abstract protected function atualizarEntidadeExistente($entidade, $novosDados);

    abstract public function cachePrefix();
}
