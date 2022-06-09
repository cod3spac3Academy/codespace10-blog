<?php

namespace App\Controller\Api;

use App\Repository\EntradaRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntradaController extends AbstractController
{
    /**
     * @Route("/api/entrada", methods={"GET"})
     */
    public function index(Request $request, EntradaRepository $entradaRepository, PaginatorInterface $paginator): Response
    {
        $currentPage = $request->query->get('page', 1);
        $filter = $request->query->all();
        $query = $entradaRepository->getQueryByFilter($filter);
        $entradas = $paginator->paginate($query, $currentPage, 10);
        $resultado = [];
        foreach ($entradas as $entrada) {
            $resultado[] = [
                'id' => $entrada->getId(),
                'fecha' => $entrada->getFecha()->format('Y-m-d H:i:s'),
                'slug' => $entrada->getSlug(),
                'titulo' => $entrada->getTitulo(),
                'usuario' => $entrada->getUsuario()->getEmail(),
                'categoria' => $entrada->getCategoria()->getNombre()
            ];
        }
        return $this->json($resultado);
    }

    /**
     * @Route("/api/entrada/{slug}", methods={"GET"})
     */
    public function detail($slug, EntradaRepository $entradaRepository)
    {
        $entrada = $entradaRepository->findOneBy(['slug' => $slug]);
        if ($entrada == null) {
            throw $this->createNotFoundException();
        }

        // Comentarios
        $comentarios = $entrada->getComentarios();
        $resultadoComentarios = [];
        foreach ($comentarios as $comentario) {
            $resultadoComentarios[] = [
                'fecha' => $comentario->getFecha()->format('Y-m-d H:i:s'),
                'usuario' => $comentario->getUsuario()->getEmail(),
                'texto' => $comentario->getTexto()
            ];
        }

        // Etiquetas
        $etiquetas = $entrada->getEtiquetas();
        $resultadoEtiquetas = [];
        foreach ($etiquetas as $etiqueta) {
            $resultadoEtiquetas[] = $etiqueta->getNombre();
        }

        $resultado = [
            'titulo' => $entrada->getTitulo(),
            'fecha' => $entrada->getFecha()->format('Y-m-d H:i:s'),
            'usuario' => $entrada->getUsuario()->getEmail(),
            'categoria' => $entrada->getCategoria()->getNombre(),
            'espacio' => $entrada->getCategoria()->getEspacio()->getNombre(),
            'resumen' => $entrada->getResumen(),
            'texto' => $entrada->getTexto(),
            'comentarios' => $resultadoComentarios,
            'etiquetas' => $resultadoEtiquetas
        ];

        return $this->json($resultado);
    }
}
