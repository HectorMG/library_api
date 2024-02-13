<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Form\Model\BookDto;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

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
     * @Rest\Post("/book")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    function PostAction(Request $request, EntityManagerInterface $em, FileUploader $fileUploader) {
        $bookDto = new BookDto();
        $form = $this->createForm(BookFormType::class, $bookDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($bookDto->base64Image) {
                $fileName = $fileUploader->uploadBase64File($bookDto->base64Image);
            }

            $book = new Book();
            $book->setTitle($bookDto->title);
            $book->setImage($fileName);
            $em->persist($book);
            $em->flush();
    
            return $book;
        }

        return $form;
    }
}
