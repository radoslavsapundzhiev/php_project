<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;
use AppBundle\Entity\User;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * UserRepository constructor.
     * @param EntityManagerInterface $em
     * @param Mapping\ClassMetadata|null $metaData
     */
    public function __construct(EntityManagerInterface $em,
                                Mapping\ClassMetadata $metaData = null)
    {
        parent::__construct($em,
            $metaData == null ?
                new Mapping\ClassMetadata(User::class):
                $metaData
        );
    }

    public function insert(User $user){
        try {
            $this->_em->persist($user);
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    public function update(User $user){
        try {
            $this->_em->merge($user);
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
}
