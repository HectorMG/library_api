<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Model\Comment\CommentRepositoryCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }



    public function save(Comment $comment): Comment
    {
        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();
        return $comment;   
    }

    public function findByCriteria(CommentRepositoryCriteria $criteria) : array {
        $queryBuilder = $this->createQueryBuilder('c')
                        ->orderBy('c.created_at', $criteria->orderBy);


        if ($criteria->book !== null) {
            $queryBuilder
                ->andWhere(':book = c.book')
                ->setParameter('book', $criteria->book);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    function delete(Comment $comment) {
        $this->getEntityManager()->remove($comment);
        $this->getEntityManager()->flush();
    }


//    /**
//     * @return Comment[] Returns an array of Comment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
