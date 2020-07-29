<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    private $repositorio;
    private $encoder;

    public function __construct(UserRepository $repositorio, UserPasswordEncoderInterface $encoder) {
        $this->repositorio = $repositorio;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="login")
     */
    public function index(Request $request)
    {
        $dados = json_decode($request->getContent());
        if(is_null($dados->usuario) || is_null($dados->senha))
            return new JsonResponse(["erro" => "Digite um usuário e uma senha válida"], 400);

        $user = $this->repositorio->findOneBy(["username" => $dados->usuario]);

        if(!$this->encoder->isPasswordValid($user, $dados->senha)){
            return new JsonResponse(["erro" => "Usuário ou senha inválidos"], Response::HTTP_UNAUTHORIZED);
        }

        $token = JWT::encode(["username" => $user->getUsername()], "chave");

        return new JsonResponse([
            "access_token" => $token
        ]);
        
    }
}
