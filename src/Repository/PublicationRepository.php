<?php

namespace App\Repository;

use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publication>
 *
 * @method Publication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publication[]    findAll()
 * @method Publication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    public function save(Publication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Publication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Publication[] Returns an array of Publication objects
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

//    public function findOneBySomeField($value): ?Publication
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByType($profession, $type)
    {
        if($profession === null){
            $queryBuilder = $this->createQueryBuilder('p')
                ->where('p.type =:type')
                ->setParameter('type', $type)
                ->orderBy('p.createdAt', 'DESC');
        } elseif ($type === null){
            $queryBuilder = $this->createQueryBuilder('p')
                ->where('p.title =:profession')
                ->setParameter('profession', $profession)
                ->orderBy('p.createdAt', 'DESC');
                return $queryBuilder->getQuery()->getResult();
        } else {
            $queryBuilder = $this->createQueryBuilder('p')
                ->where('p.title =:profession')
                ->setParameter('profession', $profession)
                ->andWhere('p.type =:type')
                ->setParameter('type', $type)
                ->orderBy('p.createdAt', 'DESC');
        }
        return $queryBuilder->getQuery()->getResult();
    }

    public function findBySearch($searchName)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->join('p.publicationUser' , 'u')
            ->where('u.name LIKE :search')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('search', '%'.$searchName.'%')
            ->getQuery();
        return $queryBuilder->getResult();
    }
}
