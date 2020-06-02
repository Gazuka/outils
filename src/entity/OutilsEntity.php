<?php

namespace Gazuka\Outils\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class OutilsEntity {
    
    private $entityClass;
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setEntityClass(string $entityClass)
    {
        $this->entityClass = $entityClass;

        return $this->entityClass;
    }

    /**
     * Permet de supprimer un objet de la base avec son ID
     *
     * @param string $class
     * @param integer $id
     * @return void
     */
    public function deleteEntityById(string $class, int $id):void
    {
        //On récupére l'objet
        $objet = $this->findEntityById($class, $id);
        //On supprime l'objet
        $this->manager->remove($objet);
        //$manager->flush();
    }

    /**
     * Récupérer une entité à partir de son id
     *
     * @param string $class
     * @param integer $id
     * @return Object
     */
    public function findEntityById(string $class, int $id):?Object
    {
        $repo = $this->returnRepo($class);
        return $repo->findOneById($id);
    }

    /**
     * Permet de récupérer le Repo d'une Entité
     * 
     * @param string $class
     */
    public function returnRepo(string $class)
    {
        $repo = $this->manager->getRepository($class);
        return $repo;
    }

    /**
     * Récupérer toutes les entités d'une classe
     *
     * @param string $class
     * @return Array
     */
    public function findAllEntity(string $class):Array
    {
        $repo = $this->returnRepo($class);
        return $repo->findAll();
    }

    /**
     * Récupérer une entité à partir de son slug
     *
     * @param string $class
     * @param string $slug
     * @return Object
     */
    public function findEntityBySlug(string $class, string $slug):?Object
    {
        $repo = $this->returnRepo($class);
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
    public function findEntityBy(string $class, array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
    {
        $repo = $this->returnRepo($class);
        return $repo->findBy($criteria, $orderBy, $limit, $offset);
    }


    
}