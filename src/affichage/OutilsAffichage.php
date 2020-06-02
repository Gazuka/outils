<?php

namespace Gazuka\Outils\Affichage;

use Symfony\Component\HttpFoundation\Response;

class OutilsAffichage {
    
    private $twig;
    private $parametresTwig = array();
    private $redirection;
    private $parametresRedirection = array();

    public function __construct()
    {
    }

    public function getTwig()
    {
        return $this->twig;
    }

    public function setTwig(string $twig)
    {
        $this->twig = $twig;

        return $this->twig;
    }

    public function getParametresTwig()
    {
        return $this->parametresTwig;
    }

    public function addParametreTwig(string $nom, $valeur)
    {
        $this->parametresTwig[$nom] = $valeur;
    }

    public function getRedirection()
    {
        return $this->redirection;
    }

    public function setRedirection(string $redirectionName)
    {
        $this->redirection = $redirectionName;

        return $this->redirection;
    }

    public function getParametresRedirection()
    {
        return $this->parametresRedirection;
    }

    public function addParametreRedirection(string $nom, object $valeur)
    {
        $this->parametresRedirection[$nom] = $valeur;
    }

    /**
     * Génère un jobController qui sera envoyé au controller
     */
    public function afficher()
    {
        //La redirection est prioritaire
        if($this->redirection != null)
        {
            //Afficher la redirection
            $jobController['fonction'] = 'redirectToRoute';
            $jobController['arguments'][] = $this->redirection;
            $jobController['arguments'][] = $this->parametresRedirection;
            return $jobController;
        }
        else
        {
            if($this->twig != null)
            {
                //Affiche la page
                $jobController['fonction'] = 'render';
                $jobController['arguments'][] = $this->twig;
                $jobController['arguments'][] = $this->parametresTwig;
                return $jobController;
            }
        }
    }
}