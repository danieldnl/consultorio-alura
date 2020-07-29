<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Helper\ExtratorDadosRequest;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EspecialidadeRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadeController extends BaseController
{
    private $repository;
    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeFactory $especialidadeFactory,
        EspecialidadeRepository $repository,
        ExtratorDadosRequest $dadosRequest,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        parent::__construct($entityManager, $especialidadeFactory, $repository, $dadosRequest, $cache, $logger);
        $this->repository = $repository;
    }

    /**
     * @param Especialidade $entidade
     * @param Especialidade $novosDados
     */
    protected function atualizarEntidadeExistente($entidade, $novosDados)
    {
        $entidade->setDescricao($novosDados->getDescricao());
    }

    public function cachePrefix()
    {
        return "Especialidade_";
    }

    /**
     * @Route("/especialidades_html")
     */
    public function especialidadesEmHtml()
    {
        $especialidades = $this->repository->findAll();
        return $this->render('especialidades.html.twig', ['especialidades' => $especialidades]);
    }
}
