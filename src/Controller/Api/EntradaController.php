<?php

namespace App\Controller\Api;

use App\Repository\EntradaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntradaController extends AbstractController
{
    /**
     * @Route("/api/entrada", methods={"GET"})
     */
    public function index(Request $request, EntradaRepository $entradaRepository): Response
    {
        $entradas = $entradaRepository->findAll();
        $resultado = [];
        foreach ($entradas as $entrada) {
            $resultado[] = [
                'id' => $entrada->getId(),
                'fecha' => $entrada->getFecha()->format('Y-m-d H:i:s'),
                'slug' => $entrada->getSlug(),
                'titulo' => $entrada->getTitulo()
            ];
        }
        return $this->json($resultado);
    }
}
