<?php

namespace Commty\Simple\Database;

use Commty\Simple\Exception\ConfigureApplicationException;

/**
 * Class Repository
 * @package commty\Database
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @var PersistManager
     */
    private $database;

    /**
     * @var Entity
     */
    private $entityClass;

    /**
     * @return mixed
     */
    abstract public function getEntityClass();

    /**
     * Repository constructor.
     * @param PersistManager $database
     * @throws ConfigureApplicationException
     */
    public function __construct(PersistManager $database)
    {
        if (empty($this->getEntityClass())) {
            throw new ConfigureApplicationException('EntityClass is not defined.');
        }

        $this->database = $database;
        $this->entityClass = $this->getEntityClass();
    }

    /**
     * @return PersistManager
     */
    public function getDb()
    {
        return $this->database;
    }

    /**
     * @param $criteria
     * @param array $params
     * @return mixed
     */
    abstract public function findOne($criteria, $params = []);

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @return Entity|null
     */
    abstract public function findOneBy(array $criteria);

    /**
     * Search All resources by criteria
     *
     * @param array $searchCriteria
     * @return Collection
     */
    abstract public function findBy(array $searchCriteria = []);

    /**
     * Save a resource
     *
     * @return Entity
     */
    abstract public function save(EntityInterface $entity);

    /**
     * Update a resource
     *
     * @param EntityInterface $entity
     * @return Entity
     */
    abstract public function update(EntityInterface $entity);

    /**
     * Delete a resource
     *
     * @param EntityInterface $entity
     * @return mixed
     */
    abstract public function delete(EntityInterface $entity);
}
