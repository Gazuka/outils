<?php

namespace Gazuka\Outils;

use Symfony\Component\HttpFoundation\Response;

class OutilsEntity {
    
    private $manager;

    public function __construct($manager)
    {
        //Constructeur à garder même vide (afin de ne pas utiliser celui de outils en boucle...)
        $this->manager = $manager;
    }

    /**
     * Permet de récupérer le Repo d'une Entité
     * 
     * @param string $class
     */
    public function getRepo(string $class)
    {
        return $this->manager->getRepository($class);
    }

    /**
     * Permet de supprimer un objet de la base avec son ID
     *
     * @param string $class
     * @param integer $id
     * @return void
     */
    public function deleteById(string $class, int $id):void
    {
        $objet = $this->findById($class, $id);
        $this->manager->remove($objet);        
    }

    /**
     * Récupérer une entité à partir de son id
     *
     * @param string $class
     * @param integer $id
     * @return Object
     */
    public function findById(string $class, int $id):?Object
    {
        $repo = $this->getRepo($class);
        return $repo->findOneById($id);
    }

    /**
     * Récupérer toutes les entités d'une classe
     *
     * @param string $class
     * @return Array
     */
    public function findAll(string $class):Array
    {
        $repo = $this->getRepo($class);
        return $repo->findAll();
    }

    /**
     * Récupérer une entité à partir de son slug
     *
     * @param string $class
     * @param string $slug
     * @return Object
     */
    public function findBySlug(string $class, string $slug):?Object
    {
        $repo = $this->getRepo($class);
        return $repo->findOneBySlug($slug);
    }

    /**
     * Récupérer une entité
     *
     * @param string $class
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return Object
     */
    public function findBy(string $class, array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
    {
        $repo = $this->getRepo($class);
        return $repo->findBy($criteria, $orderBy, $limit, $offset);
    }

}