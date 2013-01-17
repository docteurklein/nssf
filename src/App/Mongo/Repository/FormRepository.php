<?php

namespace App\Mongo\Repository;

use App\Mongo\Form;

class FormRepository
{
    use MongoRepository;

    public function newInstance()
    {
        return new Form;
    }

    public function getCollectionName()
    {
        return 'form';
    }
}

