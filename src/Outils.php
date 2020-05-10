<?php

namespace Gazuka\Outils;

use Gazuka\Outils\OutilsTwig;

class Outils {
    
    private $outilsTwig;

    public function __construct()
    {
    }

    //Gestion du Twig - Public
    public function defineTwig(string $twig):void
    {
        // $outilsTwig = $this->recupOutilsTwig();
        // $this->outilsTwig->setTwig('coucou');
        // $outilsTwig->setTwig('test');
    }

    //Gestion du Twig - Prive

    /**
     * Permet de récupérer le OutilsTwig (il le crée s'il n'existe pas encore)
     *
     * @return OutilsTwig
     */
    private function recupOutilsTwig():OutilsTwig
    {
        if($this->outilsTwig == null)
        {
            $this->outilsTwig = new OutilsTwig();
        }
        return $this->outilsTwig;
    }

    //Gestion des formulaires
}