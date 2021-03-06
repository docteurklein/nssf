<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="App\Document\Repository\FormRepository")
 */
class Form
{
    /**
     * @MongoDB\Id
     */
    public $id;

    /**
     * @MongoDB\String
     */
    public $name;
}
