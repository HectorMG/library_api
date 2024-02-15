<?php

namespace App\Controller\Api;

use App\Repository\BookRepository;
use App\Service\BookFormProcessor;
use App\Service\BookManager;
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
    public function getAction(BookManager $bookManager)
    {
        return $bookManager->getRepository()->findAll();
    }

    /**
     * @Rest\Get("/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    function getSingleAction(
        int $id,
        BookManager $bookManager
    ) {
        $book = $bookManager->find($id);

        if (!$book) {
            return View::create(['Book not found'], Response::HTTP_NOT_FOUND);
        }

        return $book;
    }

    function postAction(
        Request $request,
        BookFormProcessor $bookFormProcessor,
        BookManager $bookManager
    ) {
        $book = $bookManager->create();
        [$book, $error] = $bookFormProcessor($book, $request);
        
        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $book ?? $error;
        $view = View::create($data, $statusCode);
        return $view;
    }


    /**
     * @Rest\Post("/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function editAction(
        int $id, 
        BookManager $bookManager,
        BookFormProcessor $bookFormProcessor,
        Request $request
    )
    {
        $book = $bookManager->find($id);
        if (!$book) {
            return View::create('Book not found', Response::HTTP_NOT_FOUND);
        }

        [$book, $error] = $bookFormProcessor($book, $request);
        
        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $book ?? $error;
        $view = View::create($data, $statusCode);
        return $$view;
    }


    /**
     * @Rest\Delete("/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteAction(
        int $id, 
        BookManager $bookManager
    )
    {
        $book = $bookManager->find($id);
        if (!$book) {
            return View::create('Book not found', Response::HTTP_NOT_FOUND);
        }

        $bookManager->delete($book);
        
        return View::create("Book deleted", Response::HTTP_NO_CONTENT);
    }
}
