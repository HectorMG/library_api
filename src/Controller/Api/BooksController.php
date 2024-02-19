<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookFormProcessor;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BooksController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/books")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(BookRepository $bookRepository)
    {
        return $bookRepository->findAll();
    }

    /**
     * @Rest\Get("/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    function getSingleAction(
        int $id,
        BookRepository $bookRepository
    ) {
        $book = $bookRepository->find($id);

        if (!$book) {
            return View::create(['Book not found'], Response::HTTP_NOT_FOUND);
        }

        return $book;
    }

    /**
     * @Rest\Post("/book", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    function postAction(
        Request $request,
        BookFormProcessor $bookFormProcessor
    ) {
        $book = Book::create();
        [$book, $error] = $bookFormProcessor($book, $request);
        
        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $book ?? $error;
        $view = View::create($data, $statusCode);
        return $view;
    }


    /**
     * @Rest\Put("/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function editAction(
        int $id, 
        BookRepository $bookRepository,
        BookFormProcessor $bookFormProcessor,
        Request $request
    )
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            return View::create('Book not found', Response::HTTP_NOT_FOUND);
        }

        [$book, $error] = $bookFormProcessor($book, $request);
        
        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $book ?? $error;
        $view = View::create($data, $statusCode);
        return $view;
    }

     /**
     * @Rest\Patch("/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function patchAction(
        int $id, 
        BookRepository $bookRepository,
        BookFormProcessor $bookFormProcessor,
        Request $request
    )
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            return View::create('Book not found', Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $book->patch($data);
        return View::create($book, Response::HTTP_OK);
    }


    /**
     * @Rest\Delete("/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteAction(
        int $id, 
        BookRepository $bookRepository
    )
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            return View::create('Book not found', Response::HTTP_NOT_FOUND);
        }

        $bookRepository->delete($book);
        
        return View::create("Book deleted", Response::HTTP_NO_CONTENT);
    }
}
