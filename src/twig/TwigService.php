<?php

namespace Gazuka\Outils;

class OutilsService {
    
    private $twig;

    public function __construct()
    {
    }

    public function getTwig()
    {
        return $this->twig;
    }

    public function setTwig($twig)
    {
        $this->twig = $twig;

        return $this->twig;
    }
}