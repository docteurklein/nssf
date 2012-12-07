<?php

namespace App\Mongo\Repository;

use \MongoId;
use \MongoDate;
use \DateTime;

class MongoRepository
{
    public function transform($object)
    {
        if (is_object($object)) {
            $object = $this->transformObject($object);
        }

        if (is_array($object)) {
            $object = $this->transformArray($object);
        }

        if ($object instanceof MongoId) {
            $object = (string) $object;
        }

        if ($object instanceof MongoDate) {
            $object = $this->toDateTime($object);
        }

        var_dump('before return', $object);
        return $object;
    }

    public function reverseTransform($object)
    {
        if (isset($object->_id) && !$object->_id instanceof MongoId) {
            $object->_id = new MongoId($object->_id);
        }

        if (is_object($object)) {
            $object = $this->reverseTransformObject($object);
        }

        if (is_array($object)) {
            $object = $this->reverseTransformArray($object);
        }

        if ($object instanceof DateTime) {
            $object = $this->toMongoDate($object);
        }

        return $object;
    }

    private function transformObject($object)
    {
        $refl = new \ReflectionObject($object);
        foreach ($refl->getProperties() as $property) {

            $property->setAccessible(true);
            $value = $property->getValue($object);

            $property->setValue($object, $this->transform($value));
        }

        return $object;
    }

    private function transformArray(array $object)
    {
        if ($this->isHash($object)) {
            return (object) $object;
        }

        foreach ($object as $key => $value) {
            $object[$key] = $this->transform($value);
        }

        return $object;
    }

    private function reverseTransformObject($object)
    {
        $refl = new \ReflectionObject($object);
        foreach ($refl->getProperties() as $property) {

            $property->setAccessible(true);
            $value = $property->getValue($object);

            $property->setValue($object, $this->reverseTransform($value));
        }

        return $object;
    }

    private function reverseTransformArray(array $object)
    {
        if (!$this->isHash($object)) {
            return (array) $object;
        }

        foreach ($object as $key => $value) {
            $object[$key] = $this->reverseTransform($value);
        }

        return $object;
    }

    private function isHash(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    private function toMongoDate(DateTime $date)
    {
        return new MongoDate($date->format('U.u'));
    }

    private function toDateTime(MongoDate $date)
    {
        $dateTime = new DateTime;
        $dateTime->setTimestamp($date->sec);

        return $dateTime;
    }
}

