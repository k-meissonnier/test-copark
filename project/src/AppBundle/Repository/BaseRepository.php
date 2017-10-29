<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class BaseRepository extends EntityRepository
{
    /**
     * MUST BE OVERRIDED IN CHILD CLASSES.
     */
    const ALIAS = '';

    /**
     * Generic retrieve query builder.
     */
    public function retrieve(int $value)
    {
        return $this->createQueryBuilder(static::ALIAS)
            ->where(static::ALIAS.'.id = :value')
            ->setParameter('value', $value)
            ->getQuery()->getOneOrNullResult()
            ;
    }

    /**
     * Generic retrieve all query builder.
     */
    public function retrieveAll(): array
    {
        return $this->createQueryBuilder(static::ALIAS)
            ->getQuery()->getResult();
    }

    /**
     * Generic save.
     */
    public function save($object, $flush = true): void
    {
        $this->_em->persist($object);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Generic merge
     *
     * @param $object
     */
    public function merge($object)
    {
        $this->_em->merge($object);
        $this->_em->flush();
    }

    /**
     * Generic delete.
     */
    public function delete($object, $flush = true): void
    {
        $this->_em->remove($object);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Generic refresh.
     *
     * @param $object
     */
    public function refresh($object)
    {
        $this->_em->refresh($object);
    }

    /**
     * Generic detach.
     *
     * @param $object
     */
    public function detach($object)
    {
        $this->_em->detach($object);
    }
}
