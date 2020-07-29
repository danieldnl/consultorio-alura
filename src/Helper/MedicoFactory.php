<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;
use EntidadeFactoryException;

class MedicoFactory implements EntidadeFactory
{
    private $repositorio;

    public function __construct(EspecialidadeRepository $repositorio) {
        $this->repositorio = $repositorio;
    }

    public function criar(string $json): Medico
    {
        $dados = json_decode($json);

        $this->checarPropriedades($dados);

        $especialidadeId = $dados->especialidadeId;
        $especialidade = $this->repositorio->find($especialidadeId); 
        $medico = new Medico();
        $medico
            ->setCrm($dados->crm)
            ->setNome($dados->nome)
            ->setEspecialidade($especialidade);

        return $medico;
    }

    private function checarPropriedades(object $json)
    {
        if(!property_exists($json, 'nome'))
            throw new EntidadeFactoryException("Obrigatório informar o nome do médico");

        if(!property_exists($json, 'crm'))
            throw new EntidadeFactoryException("Obrigatório informar o crm do médico");

        if(!property_exists($json, 'especialidadeId'))
            throw new EntidadeFactoryException("Obrigatório informar a especialidade do médico");
    }
}