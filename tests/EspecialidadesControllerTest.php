<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EspecialidadesControllerTest extends WebTestCase
{
    public function testRequisicaoFalhaSemAutenticacao()
    {
        $client = static::createClient();
        $client->request("GET", "/especialidades");
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testEspecialidadesSaoListadas()
    {
        $client = static::createClient();
        $token = $this->login($client);
        $client->request("GET", "/especialidades", [], [], [
            "HTTP_AUTHORIZATION" => "Bearer $token"
        ]);

        $resposta = json_decode($client->getResponse()->getContent());
        $this->assertTrue($resposta->sucesso);
    }

    public function testInsereEspecialidade()
    {
        $client = static::createClient();
        $token = $this->login($client);
        $content = json_encode(["descricao" => "teste"]);
        $client->request("POST", "/especialidades", [], [], [
            "HTTP_AUTHORIZATION" => "Bearer $token",
            'CONTENT_TYPE' => 'application/json'
        ], $content);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testHtmlEspecialidades()
    {
        $client = self::createClient();
        $client->request('GET', '/especialidades_html');

        $this->assertSelectorTextContains('h1', 'Especialidades');
        $this->assertSelectorExists('.especialidade');
    }

    private function login(KernelBrowser $client): string
    {
        $content = json_encode([
            "usuario" => "daniel",
            "senha" => "123"
        ]);

        $client->request("POST", "/login", [], [], ["CONTENT_TYPE" => "application/json"], $content);
        $token = json_decode($client->getResponse()->getContent())->access_token;

        return $token;
    }
}
