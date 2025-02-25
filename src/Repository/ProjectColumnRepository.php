<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Comment;
use App\Entity\ProjectColumn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectColumn>
 */
class ProjectColumnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectColumn::class);
    }

    public function getCards($id) :array
    {
        $em = $this->getEntityManager();

        return $em->getRepository(Card::class)
            ->createQueryBuilder('c')
            ->where('c.project_column = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    public function getMessages($id) :array
    {
        $em = $this->getEntityManager();

        return $em->getRepository(Comment::class)
            ->createQueryBuilder('c')
            ->where('c.card = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function deleteCards($id) :void
    {
        $em = $this->getEntityManager();

        $em
            ->createQueryBuilder()
            ->delete(Card::class, 'c')
            ->where('c.project_column = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

//    /**
//     * @return ProjectColumn[] Returns an array of ProjectColumn objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProjectColumn
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
