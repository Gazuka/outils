<?php

namespace Gazuka\Outils\Entity;

use Symfony\Component\HttpFoundation\Response;

class OutilsEntity {
    
    private $entityClass;

    public function __construct()
    {
    }

    public function getEntityClass()
    {
        return $this->twig;
    }

    public function setEntityClass(string $entityClass)
    {
        $this->entityClass = $entityClass;

        return $this->entityClass;
    }
}