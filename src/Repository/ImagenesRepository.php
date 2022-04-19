<?php

namespace App\Repository;

use App\Entity\Imagenes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Imagenes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Imagenes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Imagenes[]    findAll()
 * @method Imagenes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagenesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Imagenes::class);
    }

    public function mostrarTodasImagenes() {
        $consulta = $this->getEntityManager()->createQuery(
            'SELECT imagen.id, imagen.nombre, imagen.descripcion, imagen.numVisualizaciones, imagen.numLikes, imagen.numDownloads, categorias.nombre AS categoria
             FROM App\Entity\Imagenes AS imagen JOIN imagen.categoria AS categorias')->getResult();
        return $consulta;
    }

    // /**
    //  * @return Imagenes[] Returns an array of Imagenes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Imagenes
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
