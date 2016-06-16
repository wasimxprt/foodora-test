<?php


namespace app\Service;

use app\Traits\Utility;

/**
 * Class Service is the parent class for services
 * @package app\Service
 */
class Service {

    protected $factory;
    protected $repository;

    use Utility;

    public function __construct(\app\Factory\Kernel $factory) {
        $this->factory    = $factory;
    }

    /**
     * Gets all the records for a specific table
     * @return mixed
     */
    public function getAll() {
        return $this->repository->findAll();
    }

    /**
     * Gets a specific record by id
     * @param $object
     * @param $id
     * @return bool|object
     */
    public function getById($object, $id) {
        $objectResult = $this->repository->findById($id);
        return $objectResult ? $this->createObject($object, $objectResult) : false;
    }
} 