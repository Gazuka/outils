<?php

namespace Gazuka\Outils;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class OutilsPage {
    
    private $manager;
    private $request;
    private $repoPage;
    //private $idPageActuelle;
    private $classePage;
    //Page actuelle :
    private $route;
    private $params = array();
    private $page;
    //Page mère :
    private $pageMere;

    public function __construct(EntityManagerInterface $manager, RequestStack $requestStack, $classePage)
    {
        $this->classePage = $classePage;
        if($this->classePage != null)
        {
            $this->manager = $manager;
            $this->request = $requestStack->getCurrentRequest();
            $this->route = $this->request->get('_route');
            $this->recupRepoPage();
            $this->params = $this->request->attributes->get('_route_params');
        }
        
    }

    private function recupRepoPage()
    {
        if($this->classePage != null)
        {
            $this->repoPage = $this->manager->getRepository($this->classePage);
        }        
    }

    //===================================================================================//
    //** GETs et SETs ********************************************************************/

    public function getRoute()
    {
        return $this->route;
    }
    public function setRoute($route)
    {
        $this->route = $route;
    }
    public function getParams()
    {
        return $this->params;
    }
    // public function addParam($cle, $valeur) //A supprimer par la suite ?
    // {
    //     $this->params[$cle] = $valeur;
    // }
    // public function setParams($params)
    // {
    //     $this->params = $params;
    // }

    /** Récupère la page mère à partir de son Id
     *
     * @param [type] $id
     * @return void
     */
    private function recupPageMere($id)
    {
        $this->pageMere = $this->repoPage->findOneById($id);
        return $this->pageMere;
    }

    public function getPageMere()
    {
        if($this->pageMere == null)
        {
            if(!empty($this->params['idpagemere']))
            {
                $this->recupPageMere($this->params['idpagemere']);
            }
        }
        return $this->pageMere;
    }

    /** Retourne la page actuelle
     *
     * @return void
     */
    public function getPage()
    {
        //Si l'objet page n'est pas encore créé
        if($this->page == null)
        {
            //On vérifi si il existe dans la BDD et on le récupère
            $this->page = $this->repoPage->findOneByCheminParam($this->route, serialize($this->params));
            //Sinon, on le crée
            if($this->page == null)
            {
                $this->page = new $this->classePage();
                $this->page->setNomChemin($this->route);
                $this->page->setParams($this->params);
                $this->manager->persist($this->page);
                $this->manager->flush();
            }
        }
        return $this->page;
    }

    /** Retourne l'Id de la page actuelle
     *
     * @return void
     */
    public function getPageId()
    {
        return $this->getPage()->getId();
    }

    /** Permet l'enregistrement de la page en BDD
     *
     * @return void
     */
    public function Enregistrer()
    {
        if($this->page != null)
        {
            $this->manager->persist($this->page);
            //$this->manager->flush();
        }
    }
}