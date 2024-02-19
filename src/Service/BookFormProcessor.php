<?php


namespace App\Service;

use App\Entity\Book;
use App\Entity\Book\Score;
use App\Entity\Category;
use App\Form\Model\BookDto;
use App\Form\Model\CategoryDto;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

class BookFormProcessor
{
    private $bookRepository;
    private $fileUploader;
    private $categoryRepository;
    private $formFactory;

    public function __construct(
        BookRepository $bookRepository,
        FileUploader $fileUploader, 
        CategoryRepository $categoryRepository,
        FormFactoryInterface $formFactory
    ) {
        $this->bookRepository = $bookRepository;
        $this->fileUploader = $fileUploader;
        $this->categoryRepository = $categoryRepository;
        $this->formFactory = $formFactory;
    }


    function __invoke(Book $book, Request $request): array {
        $bookDto = BookDto::createFromBook($book);

        foreach($book->getCategories() as $category) {
            $bookDto->categories[] = CategoryDto::createFromCategory($category);
        }

        $form = $this->formFactory->create(BookFormType::class, $bookDto);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return [null, "form not submitted"];
        }

        $categories = [];
        foreach ($bookDto->categories as $newCategoryDto) {
            $category = $this->categoryRepository->find($newCategoryDto->id ?? 0);

            if (!$category) {
                $category = Category::create();
                $category->setName($newCategoryDto->name);
                $this->categoryRepository->save($category);
            }

            $categories[] = $category;
        }

        if ($form->isValid()) {
            $fileName = null;
            if ($bookDto->base64Image) {
                $fileName = $this->fileUploader->uploadBase64File($bookDto->base64Image);
            }
            $book->update($bookDto->title, $fileName, $bookDto->description, Score::create($bookDto->score), ...$categories);
            $this->bookRepository->save($book);
            return [$book, null];
        }
        return [null, $form];
    }
}