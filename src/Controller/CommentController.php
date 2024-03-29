<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Type\CommentFormType;
use App\Model\Comment\CommentRepositoryCriteria;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/book/{id}/comment', name: 'add_comment', methods: ['POST'])]
    function addComment(
        string $id,
        BookRepository $bookRepository,
        CommentRepository $commentRepository,
        Request $request
    ) {
        $book = $bookRepository->find((int)$id);

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBook($book);
            $comment->setUser($this->getUser());
            $commentRepository->save($comment);
        }

        $criteria = new CommentRepositoryCriteria('DESC', (int)$id);

        $comments = $commentRepository->findByCriteria($criteria);


        $subvista = $this->renderView('comment/index.html.twig', [
            'comments' => $comments
        ]);

        return new JsonResponse(['subvista' => $subvista]);
    }

    #[Route('/comment/{id}', name: 'delete_comment', methods: ['POST'])]
    public function delete(
        int $id,
        CommentRepository $commentRepository
    ) {
        $comment = $commentRepository->find($id);

        $candDelete = $this->canDeleteOrUpdate($comment->getUser()->getId());

        if (!$candDelete) {
            $this->addFlash('success', 'No tiene permisos para realizar esta operación');
        }else{
            $commentRepository->delete($comment);
            $this->addFlash('success', 'Se eliminó el comentario con éxito');
        }

        return $this->redirectToRoute('book_show', array('id' => $comment->getBook()->getId()));
    }

    #[Route('/comment_edit/{id}', name: 'edit_comment', methods: ['POST'])]
    public function update(
        int $id,
        CommentRepository $commentRepository,
        Request $request
    ) {
        $comment = $commentRepository->find($id);

        $candUpdate = $this->canDeleteOrUpdate($comment->getUser()->getId());

        if (!$candUpdate) {
            $this->addFlash('success', 'No tiene permisos para realizar esta operación');
        }else{
            $form = $this->createForm(CommentFormType::class, $comment);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $commentRepository->save($comment);
    
                $this->addFlash('success', 'Se actualizó el comentario con éxito');
            }
        }

        $criteria = new CommentRepositoryCriteria('DESC', $comment->getBook()->getId());
        $comments = $commentRepository->findByCriteria($criteria);

        $subvista = $this->renderView('comment/index.html.twig', [
            'comments' => $comments
        ]);

        return new JsonResponse(['subvista' => $subvista]);
    }

    private function canDeleteOrUpdate(string $user_id)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $user_id == $user->getId()) 
        {
            return true;
        }
        return false;
    }
}
