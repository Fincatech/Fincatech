<?php

namespace App\Repository;

use App\Controller\EntityHelperController;
use App\Entity\Comunidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeInterface;

/**
 * @method Comunidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comunidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comunidad[]    findAll()
 * @method Comunidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComunidadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $manager)
    {
        parent::__construct($registry, Comunidad::class);
        $this->manager = $manager;
    }

    /** Inserta una comunidad en base de datos */
    public function save($data)
    {
        $comunidad = new Comunidad();

        EntityHelperController::mapearEntidad($data, 'Comunidad', $comunidad);

        //$dateImmutable = \DateTimeInterface::::createFromFormat('Y-m-d H:i:s', strtotime('now'));
        //echo $dateImmutable;
        $comunidad->setCreated( new \DateTime() )
                  ->setUsercreate(1)
                  ->setEstado('P');

        $this->manager->persist($comunidad);
        $this->manager->flush();

    }

    /** Actualiza los datos de una comunidad */
    public function update(Comunidad $comunidad): Comunidad
    {
        $this->manager->persist($comunidad);
        $this->manager->flush();

        return $comunidad;
    }

    /** Elimina una comunidad y toda la información relacionada */
    public function remove(Comunidad $comunidad)
    {
        $this->manager->remove($comunidad);
        $this->manager->flush();
    }

    // /**
    //  * @return Usuarios[] Returns an array of Usuarios objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Usuarios
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
