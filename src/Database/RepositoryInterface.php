<?php

namespace Commty\Simple\Database;

/**
 * Interface RepositoryInterface
 * @package commty\Database
 */
interface RepositoryInterface
{
    /**
     * @return mixed
     */
    public function getEntityClass();

    /**
     * @param $criteria
     * @param array $params
     * @return mixed
     */
    public function findOne($criteria, $params = []);

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @return Entity|null
     */
    public function findOneBy(array $criteria);

    /**
     * Search All resources by criteria
     * @param array $searchCriteria
     * @return
     */
    public function findBy(array $searchCriteria = []);

    /**
     * Save a resource
     * @param EntityInterface $entity
     * @return
     */
    public function save(EntityInterface $entity);

    /**
     * Update a resource
     *
     * @param EntityInterface $entity
     */
    public function update(EntityInterface $entity);

    /**
     * Delete a resource
     *
     * @param EntityInterface $entity
     * @return mixed
     */
    public function delete(EntityInterface $entity);
}
