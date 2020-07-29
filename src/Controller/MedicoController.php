<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Helper\ExtratorDadosRequest;
use App\Repository\MedicosRepository;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class MedicoController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager, 
        MedicoFactory $medicoFactory, 
        MedicosRepository $repositorio,
        ExtratorDadosRequest $dadosRequest,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    )
    {
       parent::__construct($entityManager, $medicoFactory, $repositorio, $dadosRequest, $cache, $logger);
    }

    /**
     * @Route("/especialidades/{especialidadeId}/medico", methods={"GET"})
     */
    public function buscarPorEspecialidade(int $especialidadeId)
    {
        $listaMedicos = $this->repositorio->findBy(["especialidade" => $especialidadeId]);

        return new JsonResponse($listaMedicos);
    }

    /**
     * @param Medico $entidade
     * @param Medico $novosDados
     */
    public function atualizarEntidadeExistente($entidade, $novosDados)
    {
        $entidade->setCrm($novosDados->getCrm());
        $entidade->setNome($novosDados->getNome());
        $entidade->setEspecialidade($novosDados->getEspecialidade());
    }

    public function cachePrefix()
    {
        return "Medico_";
    }
}
