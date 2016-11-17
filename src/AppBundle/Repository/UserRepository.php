<?php

namespace AppBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function saverObject($object)
    {
        $em = $this->getEntityManager();
        $em->persist($object);
        $em->flush();
    }

    public function removeObject($object)
    {
        $em = $this->getEntityManager();
        $em->remove($object);
        $em->flush();
    }

    public function getLimitOffsetUser($limit, $offset)
    {

        $query = $this->createQueryBuilder('user_repository')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->getResult();
    }

}
