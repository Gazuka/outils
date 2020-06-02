<?php

namespace Gazuka\Outils;

use Gazuka\Outils\Entity\OutilsEntity;
use Doctrine\ORM\EntityManagerInterface;
use Gazuka\Outils\Affichage\OutilsAffichage;
use Gazuka\Outils\Formulaire\OutilsFormulaire;

class Outils {
    
    private $outilsAffichage;
    private $outilsEntity;
    private $outilsFormulaire;
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    //Gestion de l'affichage - Public ===============================================================================

    /** Permet de définir le fichier .twig qui servira pour l'affichage de la page
     *
     * @param string $twig //Chemin du fichier twig depuis le dossier template
     * @return void
     */
    public function defineTwig(string $twig):void
    {
        $outilsAffichage = $this->recupOutilsAffichage();
        $outilsAffichage->setTwig($twig);
    }

    /**
     * Permet de définit les paramètres utiles au Twig lors de l'affichage
     *
     * @param string $nom //Nom de la variable dans le twig
     * @param $valeur //Données qui seront utilisées dans le twig
     * @return void
     */
    public function defineParamTwig(string $nom, $valeur):void
    {
        $outilsAffichage = $this->recupOutilsAffichage();
        $outilsAffichage->addParametreTwig($nom, $valeur);
    }

    /**
     * Permet de définir le nom de la page qui sera utilisé lors de la redirection
     *
     * @param string $redirectionName
     * @return void
     */
    public function defineRedirection(string $redirectionName):void
    {
        $outilsAffichage = $this->recupOutilsAffichage();
        $outilsAffichage->setRedirection($redirectionName);
    }

    /**
     * Permet de définit les paramètres utiles à la redirection lors de l'affichage
     *
     * @param string $nom
     * @param [type] $valeur
     * @return void
     */
    public function defineParamRedirect(string $nom, $valeur)
    {
        $outilsAffichage = $this->recupOutilsAffichage();
        $outilsAffichage->addParametreRedirection($nom, $valeur);
    }

    /**
     * Retourne un tableau que le controller analysera pour effectuer son affichage
     *
     * @return Array
     */
    public function afficher():array
    {
        //Enregistre tous les managers!!!
        $this->manager->flush();
        //Affiche la page
        $outilsAffichage = $this->recupOutilsAffichage();
        return $outilsAffichage->afficher();
    }

    //Gestion de l'affichage - Prive ================================================================================

    /**
     * Permet de récupérer le OutilsAffichage (il le crée s'il n'existe pas encore)
     *
     * @return OutilsAffichage
     */
    private function recupOutilsAffichage():OutilsAffichage
    {
        if($this->outilsAffichage == null)
        {
            $this->outilsAffichage = new OutilsAffichage();
        }
        return $this->outilsAffichage;
    }

    //Gestion des entités - Public ==================================================================================

    /**
     * Permet de supprimer un objet de la base avec son ID
     *
     * @param string $class
     * @param integer $id
     * @return void
     */
    public function deleteEntityById(string $class, int $id):void
    {
        $outilsEntity = $this->recupOutilsEntity();
        $outilsEntity->deleteEntityById($class, $id);
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
        $outilsEntity = $this->recupOutilsEntity();
        return $outilsEntity->findEntityById($class, $id);
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
        $outilsEntity = $this->recupOutilsEntity();
        return $outilsEntity->findBySlug($class, $slug);
    }
    
    /**
     * Récupérer toutes les entités d'une classe
     *
     * @param string $class
     * @return Array
     */
    public function findAllEntity(string $class):Array
    {
        $outilsEntity = $this->recupOutilsEntity();
        return $outilsEntity->findAllEntity($class);
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
        $outilsEntity = $this->recupOutilsEntity();
        return $outilsEntity->findEntityBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Permet de récupérer le Repo d'une Entité
     * 
     * @param string $class
     */
    public function returnRepo(string $class)
    {
        $outilsEntity = $this->recupOutilsEntity();
        return $outilsEntity->returnRepo($class);
    }


    //Gestion des entités - Prive ===================================================================================

    /**
     * Permet de récupérer le OutilsEntity (il le crée s'il n'existe pas encore)
     *
     * @return OutilsEntity
     */
    private function recupOutilsEntity():OutilsEntity
    {
        if($this->outilsEntity == null)
        {
            $this->outilsEntity = new OutilsEntity($this->manager);
        }
        return $this->outilsEntity;
    }

    //Gestion des formulaires - Public ==============================================================================

    // public function setActions($controller, $actions) //????????????????????????????????????????????????????????
    // {
    //     $outilsFormulaire = $this->recupOutilsFormulaire();
    //     $outilsFormulaire->setActions()
    // }
    public function setFormClassType($classType)
    {
        $outilsFormulaire = $this->recupOutilsFormulaire();
        $outilsFormulaire->setClassType($classType);
    }

    public function setFormElement($element)
    {
        $outilsFormulaire = $this->recupOutilsFormulaire();
        $outilsFormulaire->setElement($element);
    }
    
    public function setFormForm($form)
    {
        $outilsFormulaire = $this->recupOutilsFormulaire();
        $outilsFormulaire->setForm($form);
    }
    
    public function setFormPageResultat($page)
    {
        $outilsFormulaire = $this->recupOutilsFormulaire();
        $outilsFormulaire->setPageResultat($page);
    }
    
    public function setFormPageResultatConfig($config)
    {
        $outilsFormulaire = $this->recupOutilsFormulaire();
        $outilsFormulaire->setPageResultatConfig($config);
    }
    
    public function setFormTexteConfirmation($texte)
    {
        $outilsFormulaire = $this->recupOutilsFormulaire();
        $outilsFormulaire->setTexteConfirmation($texte);
    }
    
    public function setFormTexteConfirmationEval($eval)
    {
        $outilsFormulaire = $this->recupOutilsFormulaire();
        $outilsFormulaire->setTexteConfirmationEval($eval);
    }
    
    public function setFormTwigFormulaire($twigFormulaire)
    {
        $outilsFormulaire = $this->recupOutilsFormulaire();
        $outilsFormulaire->setTwigFormulaire($twigFormulaire);
    }
    
    public function creerFormulaire():void
    {
        $outilsFormulaire = $this->recupOutilsFormulaire();
        $outilsFormulaire->creerFormulaire();
    }

    //Gestion des formulaires - Privé ===============================================================================

    /**
     * Permet de récupérer le OutilsFormulaire (il le crée s'il n'existe pas encore)
     *
     * @return OutilsFormulaire
     */
    private function recupOutilsFormulaire():OutilsFormulaire
    {
        if($this->outilsFormulaire == null)
        {
            $this->outilsFormulaire = new OutilsFormulaire();
        }
        return $this->outilsFormulaire;
    }
}





// protected function Afficher():Response
// {
    
//     //Vérifier si twig est vide et qu'il n'y a pas encore de redirect, alors c'est que nous devons attendre une réponse de formulaireService
//     if($this->twig == null && $this->redirect == null)
//     {
//         //Vérifier si le formulaireService souhaite effectuer une redirection ou appeler un twig
//         if($this->formulaireService->getRedirect() != null)
//         {
//             $this->defineRedirect($this->formulaireService->getRedirect());
//             $this->defineParamRedirect($this->formulaireService->getPageResultatConfig());
//         }
//         else
//         {
//             $this->defineTwig($this->formulaireService->getTwigFormulaire());
//             $this->defineParamTwig('form', $this->formulaireService->getForm());
//             $this->defineParamTwig('element', $this->formulaireService->getElement());
//         }   

//         //Vérifier si le formulaireService souhaite faire passer des messages flush
//         foreach($this->formulaireService->getMessagesFlash() as $message)
//         {
//             $this->addFlash($message[0], $message[1]);
//         } 
//     }
    
//     //Donner le gestionService à Twig
//     $this->defineParamTwig('gestionService', $this->gestionService);

//     //Enregistrer la page
//     if($this->pageService != null)
//     {
//         $this->pageService->Enregistrer();
//     }

//     //Vérifier si redirection ou affichage
//     if($this->redirect != null)
//     {
        
//     }
//     else
//     {
        
//     }
// }