<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AloMundoController extends AbstractController
{
    /**
     * @Route("/alo", name="alo_mundo")
     */
    public function index(Request $request)
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/AloMundoController.php',
        // ]);

        $pathInfo = $request->getPathInfo();
        $param = $request->get('param');

        return new JsonResponse(['mensagem' => 'olá mundo!', 'pathInfo' => $pathInfo, 'param' => $param]);
    }
}
