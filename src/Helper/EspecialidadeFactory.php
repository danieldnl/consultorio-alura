<?php

namespace App\Helper;

use App\Entity\Especialidade;
use EntidadeFactoryException;

class EspecialidadeFactory implements EntidadeFactory
{
    public function criar(string $json)
    {
        $dados = json_decode($json);
        
        if(!property_exists($dados, 'descricao'))
            throw new EntidadeFactoryException("É obrigatório informar a especialidade");

        $especialidade = new Especialidade();
        $especialidade->setDescricao($dados->descricao);

        return $especialidade;
    }
}