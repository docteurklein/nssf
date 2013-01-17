<?php

namespace App\Mongo\Repository;

use \MongoDB;
use \MongoCursor;
use \MongoId;
use \MongoDate;
use \DateTime;
use \ArrayIterator;

trait MongoRepository
{
    protected $mongo;

    public function __construct(MongoDB $mongo)
    {
        $this->mongo = $mongo->selectCollection($this->getCollectionName());
    }

    abstract public function newInstance();

    abstract public function getCollectionName();

    public function transform($object)
    {
        $destination = $this->newInstance();
        $this->copy((object) $object, $destination);

        return $this->doTransform($destination);
    }

    public function find($id)
    {
        $object = $this->mongo->findOne(['_id' => new \MongoId($id)]);
        $object = $this->transform($object);

        return $object;
    }

    public function findAll()
    {
        return $this->hydrateCursor($this->mongo->find());
    }

    public function save($object)
    {
        $object = $this->reverseTransform($object);

        return $this->mongo->save($object);
    }

    protected function hydrateCursor(MongoCursor $cursor)
    {
        $results = [];
        foreach ($cursor as $document) {
            $results[] = $this->transform($document);
        }

        return $this->toArrayIterator($results);
    }

    protected function copy($source, $destination)
    {
        $refl = new \ReflectionObject($source);

        foreach ($refl->getProperties() as $property) {
            $property->setAccessible(true);
            $property->setValue($destination, $property->getValue($source));
        }
    }

    protected function doTransform($object)
    {
        if ($object instanceof MongoId) {
            return (string) $object;
        }

        if ($object instanceof MongoDate) {
            return $this->toDateTime($object);
        }

        if (is_object($object)) {
            return $this->transformObject($object);
        }

        if (is_array($object)) {
            return $this->transformArray($object);
        }

        return $object;
    }

    protected function transformObject($object)
    {
        $refl = new \ReflectionObject($object);
        foreach ($refl->getProperties() as $property) {

            $property->setAccessible(true);
            $value = $property->getValue($object);
            $property->setValue($object, $this->doTransform($value));
        }

        return $object;
    }

    protected function transformArray(array $object)
    {
        foreach ($object as $key => $value) {
            $object[$key] = $this->doTransform($value);
        }

        if ($this->isHash($object)) {
            return $this->toArrayIterator($object);
        }

        return $object;
    }

    public function reverseTransform($object)
    {
        if (is_object($object)) {
            return $this->doReverseTranform(clone $object);
        }

        return $this->doReverseTranform($object);
    }

    protected function doReverseTranform($object)
    {
        if (isset($object->_id) && !$object->_id instanceof MongoId) {
            $object->_id = $this->toMongoId($object->_id);
        }

        if ($object instanceof DateTime) {
            return $this->toMongoDate($object);
        }

        if (is_object($object)) {
            return $this->reverseTransformObject($object);
        }

        if (is_array($object)) {
            return $this->reverseTransformArray($object);
        }

        return $object;
    }

    protected function reverseTransformObject($object)
    {
        $refl = new \ReflectionObject($object);
        foreach ($refl->getProperties() as $property) {

            $property->setAccessible(true);
            $value = $property->getValue($object);

            $property->setValue($object, $this->doReverseTranform($value));
        }

        return $object;
    }

    protected function reverseTransformArray(array $object)
    {
        foreach ($object as $key => $value) {
            $object[$key] = $this->doReverseTranform($value);
        }

        return $object;
    }

    protected function isHash(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    protected function toMongoId($id)
    {
        return new MongoId($id);
    }

    protected function toMongoDate(DateTime $date)
    {
        return new MongoDate($date->format('U.u'));
    }

    protected function toDateTime(MongoDate $date)
    {
        $dateTime = new DateTime;
        $dateTime->setTimestamp($date->sec);

        return $dateTime;
    }

    protected function toArrayIterator(array $object)
    {
        return new ArrayIterator($object);
    }
}

