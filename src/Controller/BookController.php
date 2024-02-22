<?php

namespace App\Controller;


use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/', name: 'app_book', methods: ['GET'])]
    public function index(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/book/{id}', name: 'book_show', methods: ['GET'])]
    function show(
        int $id,
        BookRepository $bookRepository
    ) {
        $book = $bookRepository->find($id);
        if (!$book) {
            return $this->json('Book not found', Response::HTTP_NOT_FOUND);
        }
        
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
