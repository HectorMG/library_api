<?php


namespace App\Service;

use App\Entity\Book;
use App\Form\Model\BookDto;
use App\Form\Model\CategoryDto;
use App\Form\Type\BookFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

class BookFormProcessor
{
    private $bookManager;
    private $fileUploader;
    private $categoryManager;
    private $formFactory;

    public function __construct(
        BookManager $bookManager,
        FileUploader $fileUploader, 
        CategoryManager $categoryManager,
        FormFactoryInterface $formFactory
    ) {
        $this->bookManager = $bookManager;
        $this->fileUploader = $fileUploader;
        $this->categoryManager = $categoryManager;
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
                    $category = $this->categoryManager->find($originalCategoryDto->id);
                    $book->removeCategory($category);
                }
            }

            foreach ($bookDto->categories as $newCategory) {
                if (!$originalCategories->contains($newCategory)) {
                    $category = $this->categoryManager->find($newCategory->id ?? 0);

                    if (!$category) {
                        $category = $this->categoryManager->create();
                        $category->setName($newCategory->name);
                        $this->categoryManager->save($category);
                    }

                    $book->addCategory($category);
                }
            }

            $book->setTitle($bookDto->title);
            if ($bookDto->base64Image) {
                $fileName = $this->fileUploader->uploadBase64File($bookDto->base64Image);
                $book->setImage($fileName);
            }
            $this->bookManager->save($book);
            return [$book, null];
        }
        return [null, $form];
    }
}