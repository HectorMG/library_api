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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BookFormProcessor
{
    private BookRepository $bookRepository;
    private FileUploader $fileUploader;
    private CategoryRepository $categoryRepository;
    private FormFactoryInterface $formFactory;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        BookRepository $bookRepository,
        FileUploader $fileUploader, 
        CategoryRepository $categoryRepository,
        FormFactoryInterface $formFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->bookRepository = $bookRepository;
        $this->fileUploader = $fileUploader;
        $this->categoryRepository = $categoryRepository;
        $this->formFactory = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
    }


    function __invoke(Book $book, Request $request): array {
        $bookDto = BookDto::createFromBook($book);

        foreach($book->getCategories() as $category) {
            $bookDto->categories[] = CategoryDto::createFromCategory($category);
        }

        $content = json_decode($request->getContent(), true);
        $form = $this->formFactory->create(BookFormType::class, $bookDto);
        $form->submit($content);
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
            foreach ($book->pullDomainEvents() as $event) {
                $this->eventDispatcher->dispatch($event);
            }
            return [$book, null];
        }
        return [null, $form];
    }
}