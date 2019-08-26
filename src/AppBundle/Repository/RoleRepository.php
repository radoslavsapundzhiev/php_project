<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use AppBundle\Entity\Role;
/**
 * RoleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RoleRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct(EntityManagerInterface $em,
                                Mapping\ClassMetadata $metaData = null)
    {
        parent::__construct($em,
            $metaData == null ?
                new Mapping\ClassMetadata(Role::class) :
                $metaData);
    }
}
