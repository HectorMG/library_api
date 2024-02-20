<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    /**
     * @Route("/",name="book_list")
     */
    public function list(BookRepository $bookRepository)
    {
       $books = $bookRepository->findAll();

       $data = [];
       foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'image' => $book->getImage()
            ];
       }

       return $this->render('index.html.twig', [
    ]);
    }
    
    /**
     * @Route("/book",name="book_create")
     */
    function createBook(Request $request, EntityManagerInterface $em) : JsonResponse {
        $book = new Book();

        $title = $request->get('title');

        if (empty($title)) {
            $response = $this->json([
                'error'=>'El título no puede estar vacío',
                'data'=>null
            ]);
            return $response;
        }

        $book->setTitle($title);
        $em->persist($book);
        $em->flush();
        $response = $this->json([
            'data'=>[
                'id' => $book->getId(),
                'title' => $book->getTitle()
            ]
        ]);
        return $response;
    }
}