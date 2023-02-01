<?php

namespace App\Controller;

use App\Entity\Cinema;
use App\Form\CinemaType;
use App\Repository\CinemaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;




#[Route('/')]
class CinemaController extends AbstractController
{
    #[Route('/', name: 'app_cinema_index', methods: ['GET'])]
    public function index(CinemaRepository $cinemaRepository): Response
    {
        return $this->render('cinema/index.html.twig', [
            'cinemas' => $cinemaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cinema_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CinemaRepository $cinemaRepository): Response
    {
        $cinema = new Cinema();
        $form = $this->createForm(CinemaType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cinemaRepository->save($cinema, true);
            $this->addFlash('success', 'Le film a bien été ajouté');
            return $this->redirectToRoute('app_cinema_index', [], Response::HTTP_CREATED);
        }
        

        return $this->renderForm('cinema/new.html.twig', [
            'cinema' => $cinema,
            'form' => $form,
        ]);
    }

    #[Route('/getall', name: 'get_all_cinemas', methods: ['GET'])]
    public function getAll(CinemaRepository $cinemaRepository): JsonResponse
    {
        $cinema = $cinemaRepository->findAll();

        $cinema = array_map(function ($cinema) {
            return [
                'id' => $cinema->getId(),
                'nom' => $cinema->getNom(),
                'synopsis' => $cinema->getSynopsis(),
                'type' => $cinema->getType(),
                'date' => $cinema->getCreatedAt()
            ];
        }, $cinema);

        return new JsonResponse($cinema, Response::HTTP_OK);
    

        
    }

    #[Route('/{id}', name: 'app_cinema_show', methods: ['GET'])]
    public function show(Cinema $cinema): Response
    {
        return $this->render('cinema/show.html.twig', [
            'cinema' => $cinema,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cinema_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cinema $cinema, CinemaRepository $cinemaRepository): Response
    {
        $form = $this->createForm(CinemaType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cinemaRepository->save($cinema, true);

            return $this->redirectToRoute('app_cinema_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cinema/edit.html.twig', [
            'cinema' => $cinema,
            'form' => $form,
        ]);
    }

    

    #[Route('/{id}', name: 'app_cinema_delete', methods: ['POST'])]
    public function delete(Request $request, Cinema $cinema, CinemaRepository $cinemaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cinema->getId(), $request->request->get('_token'))) {
            $cinemaRepository->remove($cinema, true);
            
        }

        return $this->redirectToRoute('app_cinema_index', [], Response::HTTP_SEE_OTHER);
    }

   
   

    
    #[Route('/get/{id}', name: 'get_cinema', methods: ['GET'])]
    public function get(int $id, CinemaRepository $cinemaRepository): JsonResponse
    {
        $cinema = $cinemaRepository->find($id);
        $data = [
            'id' => $cinema->getId(),
            'nom' => $cinema->getNom(),
            'synopsis' => $cinema->getSynopsis(),
            'type' => $cinema->getType(),
            'createdAt' => $cinema->getCreatedAt(),
        ];

        //if id is null, throw an exception
        if (!$cinema) {
            throw $this->createNotFoundException(
                'Pas de cinéma trouvé'
            );
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
