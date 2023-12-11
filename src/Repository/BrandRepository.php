<?php

namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Brand>
 *
 * @method Brand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Brand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Brand[]    findAll()
 * @method Brand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brand::class);
    }



    /**
    * @return array Returns an array of Brand objects
     */

    public function findBrandsWithProductCount(): array
    {
        // Obtient le gestionnaire d'entités pour la sous-requête
        $entityManager = $this->getEntityManager();

        // Crée une sous-requête pour compter les produits
        $subQuery = $entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from('App\Entity\Product', 'p')
            ->where('p.brand = b.id') // Assurez-vous que 'brand' est le bon nom de la propriété dans l'entité Product
            ->getDQL();

        // Crée la requête principale
        return $this->createQueryBuilder('b')
            ->addSelect('b, (' . $subQuery . ') as productCount')
            ->getQuery()
            ->getResult();
    }


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

//    public function findOneBySomeField($value): ?Brand
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
