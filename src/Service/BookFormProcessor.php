<?php


namespace App\Service;

use App\Entity\Book;
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

        $originalCategories = new ArrayCollection();
        foreach($book->getCategories() as $category) {
            $categoryDto = CategoryDto::createFromCategory($category);
            $bookDto->categories[] = $categoryDto;
            $originalCategories->add($categoryDto);
        }

        $form = $this->formFactory->create(BookFormType::class, $bookDto);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return [null, "form not submitted"];
        }

        if ($form->isValid()) {
            foreach ($originalCategories as $originalCategoryDto) {
                if (!in_array($originalCategoryDto, $bookDto->categories)) {
                    $category = $this->categoryRepository->find($originalCategoryDto->id);
                    $book->removeCategory($category);
                }
            }

            foreach ($bookDto->categories as $newCategory) {
                if (!$originalCategories->contains($newCategory)) {
                    $category = $this->categoryRepository->find($newCategory->id ?? 0);

                    if (!$category) {
                        $category = Category::create();
                        $category->setName($newCategory->name);
                        $this->categoryRepository->save($category);
                    }

                    $book->addCategory($category);
                }
            }

            $book->setTitle($bookDto->title);
            if ($bookDto->base64Image) {
                $fileName = $this->fileUploader->uploadBase64File($bookDto->base64Image);
                $book->setImage($fileName);
            }
            $this->bookRepository->save($book);
            return [$book, null];
        }
        return [null, $form];
    }
}