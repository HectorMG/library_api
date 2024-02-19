<?php

namespace App\Repository;

use App\Entity\Book;
use App\Model\Book\BookRepositoryCriteria;
use App\Service\FileDeleter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Book>

 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private FileDeleter $fileDeleter)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $book): Book
    {
        $this->getEntityManager()->persist($book);
        $this->getEntityManager()->flush();
        return $book;
    }

    public function reload(Book $book): Book
    {
        $this->getEntityManager()->refresh($book);
        return $book;
    }

    function delete(Book $book) {

        $image = $book->getImage();

        if (null !== $image) {
            ($this->fileDeleter)($image);
        }

        $this->getEntityManager()->remove($book);
        $this->getEntityManager()->flush();
    }

    public function findByCriteria(BookRepositoryCriteria $criteria) : array {
        $queryBuilder = $this->createQueryBuilder('b')
                        ->orderBy('b.id', 'DESC');

        if ($criteria->categoryId !== null) {
            $queryBuilder
                ->andWhere(':categoryId MEMBER OF b.categories')
                ->setParameter('categoryId', $criteria->categoryId);
        }

        if ($criteria->searchText !== null) {
            $queryBuilder
                ->andWhere('b.title LIKE :searchText or b.description LIKE :searchText')
                ->setParameter('searchText', "%{$criteria->searchText}%");
        }

        $queryBuilder->setMaxResults($criteria->itemsPerPage);
        $queryBuilder->setFirstResult(($criteria->page - 1) * $criteria->itemsPerPage);

        $paginator = new Paginator($queryBuilder->getQuery());
        return [
            'total' => \count($paginator),
            'itemsPerPage' => $criteria->itemsPerPage,
            'page' => $criteria->page,
            'data' => iterator_to_array($paginator->getIterator())
        ];
    }


//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
