<?php

namespace App\Document\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Document\Form;

class FormRepository extends DocumentRepository
{
    public function newInstance()
    {
        return new Form;
    }
}

