<?php

namespace App\Mongo\Repository;

use App\Mongo\Form;
use \MongoDB;

class FormRepository
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
        $object = (object) $this->mongo->findOne(['id' => $id]);
        $object->_id = (string) $object->_id;

        return $object;
    }

    public function findAll()
    {
        return $this->mongo->find();
    }

    public function save(\stdClass $form)
    {
        $form->_id = new \MongoId($form->_id);

        return $this->mongo->save($form);
    }
}

