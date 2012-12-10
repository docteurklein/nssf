<?php

namespace App\Mongo\Repository;

use App\Mongo\Form;
use \MongoDB;

class FormRepository extends MongoRepository
{
    private $mongo;

    public function __construct(MongoDB $mongo)
    {
        $this->mongo = $mongo->selectCollection('form');
    }

    public function newInstance()
    {
        return new Form;
    }

    public function find($id)
    {
        $object = $this->mongo->findOne(['_id' => new \MongoId($id)]);
        $object = $this->transform($object);
        (var_dump('final', $object));
        (var_dump('finalreverse ', $this->reverseTransform($object)));

        var_dump($object);

        return $object;
    }

    public function findAll()
    {
        return $this->mongo->find();
    }

    public function save($object)
    {
        $this->reverseTransform($object);

        return $this->mongo->save($object);
    }
}

