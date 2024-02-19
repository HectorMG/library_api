<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Model\Book\BookRepositoryCriteria;
use App\Repository\BookRepository;
use App\Service\BookFormProcessor;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\View as ViewAtribute;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BooksController extends AbstractFOSRestController
{
    
    #[Get(path:"/books")]
    #[ViewAtribute(serializerGroups: ["book"], serializerEnableMaxDepthChecks: true)]
    public function getAction(BookRepository $bookRepository, Request $request)
    {
        $categoryId = $request->query->get('categoryId');
        $searchText = $request->query->get('searchText');
        $page = $request->query->get('page');
        $itemsPerPage = $request->query->get('itemsPerPage');
        $criteria = new BookRepositoryCriteria(
            $categoryId,
            $searchText, 
            $itemsPerPage!== null ? \intval($itemsPerPage) : 10, 
            $page !== null ? \intval($page) : 1
        );
        return $bookRepository->findByCriteria($criteria);
    }


    #[Get(path:"/book/{id}", requirements: ["id" => "\d+"])]
    #[ViewAtribute(serializerGroups: ["book"], serializerEnableMaxDepthChecks: true)]
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

    #[Post(path:"/book",requirements:["id" => "\d+"]) ]
    #[ViewAtribute(serializerGroups: ["book"], serializerEnableMaxDepthChecks: true)]
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


    #[Put(path:"/book/{id}", requirements: ["id" => "\d+"]) ]
    #[ViewAtribute(serializerGroups: ["book"], serializerEnableMaxDepthChecks: true)]
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

     
    #[Patch(path:"/book/{id}", requirements: ["id" => "\d+"]) ]
    #[ViewAtribute(serializerGroups: ["book"], serializerEnableMaxDepthChecks: true)]
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


    #[Delete(path:"/book/{id}", requirements: ["id" => "\d+"]) ]
    #[ViewAtribute(serializerGroups: ["book"], serializerEnableMaxDepthChecks: true)]
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
