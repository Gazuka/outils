<?php

namespace Gazuka\Outils;

use Gazuka\Outils\OutilsEntity;
use Gazuka\Outils\OutilsAffichage;
use Gazuka\Outils\OutilsFormulaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Outils {
    
    protected $outilsAffichage;
    protected $outilsEntity;
    protected $outilsFormulaire;
    protected $outilsPage;
    protected $manager;
    protected $jobController = array();

    public function __construct(EntityManagerInterface $manager, RequestStack $requestStack, $classePage = null)
    {
        $this->outilsAffichage = new OutilsAffichage();
        $this->outilsEntity = new OutilsEntity($manager);
        $this->outilsFormulaire = new OutilsFormulaire($requestStack);
        $this->outilsPage = new OutilsPage($manager, $requestStack, $classePage);
        $this->manager = $manager;
    }

    /** Retourne un tableau que le controller analysera pour effectuer son affichage
     *
     * @return Array
     */
    public function recupJobController():array
    {
        //Vérifier si twig est vide et qu'il n'y a pas encore de redirect, alors c'est que nous devons attendre une réponse de formulaireService
        if($this->outilsAffichage->getTwig() == null && $this->outilsAffichage->getRedirection() == null)
        {
            //Si une redirection existe, elle est prioritaire
            if($this->outilsFormulaire->getRedirect() != null)
            {
                //Une redirection doit être effectuer
                if($this->outilsFormulaire->getRedirect() == 'redirect_true')
                {
                    //Si une page mere existe on y retourne
                    if($this->outilsPage->getPageMere() != null)
                    {
                        $this->defineRedirection($this->outilsPage->getPageMere()->getNomChemin());
                        foreach($this->outilsPage->getPageMere()->getParams() as $param => $valeur)
                        {
                            $this->addParamRedirect($param, $valeur);
                        }                        
                    }
                }
                else
                {
                    $this->defineRedirection($this->outilsFormulaire->getRedirect());
                    $this->addParamsRedirect($this->outilsFormulaire->getPageResultatConfig());
                }                
            }
            else
            {
                //Sinon un twig est défini pour le formulaire, on l'affiche
                if($this->outilsFormulaire->getTwigFormulaire() != null)
                {
                    $this->defineTwig($this->outilsFormulaire->getTwigFormulaire());
                    $this->addParamTwig('form', $this->outilsFormulaire->getForm());
                    $this->addParamTwig('element', $this->outilsFormulaire->getElement());
                }
                else
                {
                    //Sinon, si une page mere existe on y retourne
                    if($this->outilsPage->getPageMere() != null)
                    {
                        $this->defineRedirection($this->outilsPage->getPageMere()->getNomChemin());
                        foreach($this->outilsPage->getPageMere()->getParams() as $param => $valeur)
                        {
                            $this->addParamRedirect($param, $valeur);
                        }                        
                    }
                }        
            } 
        }
        
        //Récupération du jobController de OutilsAffichage
        $this->jobController['affichage'] = $this->outilsAffichage->jobController();
        
        //On enregistre le tout
        $this->outilsPage->enregistrer();
        $this->enregistrer();

        //Retourne le jobController au Controller qui affichera la page
        return $this->jobController;
    }

    ////////////////////////////////////////////////////////////////////////
    // GESTION DE L'AFFICHAGE
    ////////////////////////////////////////////////////////////////////////
    
    /** Permet de définir le fichier .twig qui servira pour l'affichage de la page
     *
     * @param string $twig //Chemin du fichier twig depuis le dossier template
     * @return void
     */
    public function defineTwig(string $twig):void
    {
        $this->outilsAffichage->setTwig($twig);
    }

    /** Permet de définit les paramètres utiles au Twig lors de l'affichage
     *
     * @param string $nom //Nom de la variable dans le twig
     * @param $valeur //Données qui seront utilisées dans le twig
     * @return void
     */
    public function addParamTwig(string $nom, $valeur):void
    {
        $this->outilsAffichage->addParametreTwig($nom, $valeur);
    }

    /** Permet de définir le nom de la page qui sera utilisé lors de la redirection
     *
     * @param string $redirectionName
     * @return void
     */
    public function defineRedirection(string $redirectionName):void
    {
        $this->outilsAffichage->setRedirection($redirectionName);
    }

    /** Permet de définit les paramètres utiles à la redirection lors de l'affichage
     *
     * @param string $nom
     * @param [type] $valeur
     * @return void
     */
    public function addParamRedirect(string $nom, $valeur)
    {
        $this->outilsAffichage->addParametreRedirection($nom, $valeur);
    }

    /** Permet de définit plusieurs paramètres utiles à la redirection lors de l'affichage
     *
     * @param array $valeurs
     * @return void
     */
    public function addParamsRedirect($valeurs)
    {
        foreach($valeurs as $nom => $valeur)
        {
            $this->outilsAffichage->addParametreRedirection($nom, $valeur);
        }
    }

    ////////////////////////////////////////////////////////////////////////
    // GESTION DES ENTITES
    ////////////////////////////////////////////////////////////////////////
    
    /** Enregistre les entites et la page */
    public function enregistrer()
    {
        //Enregistre les entités dans le manager
        $this->manager->flush();
    }

    public function persist($entity)
    {
        //Persist une entité dans le manager
        $this->manager->persist($entity);
    }

    /** Supprimer une entité à partir de son Id
     *
     * @param string $class
     * @param integer $id
     * @return void
     */
    public function deleteEntityById(string $class, int $id):void
    {
        $this->outilsEntity->deleteById($class, $id);
    }

    /** Récupérer toutes les entités
     *
     * @param string $class
     * @return Array
     */
    public function findAllEntity(string $class):Array
    {
        return $this->outilsEntity->findAll($class);
    }

    /** Récupérer une entité à partir de son id
     *
     * @param string $class
     * @param integer $id
     * @return Object
     */
    public function findEntityById(string $class, int $id):?Object
    {
        return $this->outilsEntity->findById($class, $id);
    }

    /** Récupérer une entité à partir de son slug
     *
     * @param string $class
     * @param string $slug
     * @return Object
     */
    public function findEntityBySlug(string $class, string $slug):?Object
    {
        return $this->outilsEntity->findBySlug($class, $slug);
    }

    /** Récupérer une entité par critères simples
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
        return $this->outilsEntity->findBy($class, $criteria, $orderBy, $limit, $offset);
    }

    /** Récupère le Repo d'une Entité
     * 
     * @param string $class
     */
    public function returnRepo(string $class)
    {
        return $this->outilsEntity->getRepo($class);
    }

    ////////////////////////////////////////////////////////////////////////
    // GESTION DES FORMULAIRES
    ////////////////////////////////////////////////////////////////////////
  
    public function setFormActions($actions)
    {
        $this->outilsFormulaire->setActions($actions);
    }

    public function setFormClassType($classType)
    {
        $this->outilsFormulaire->setClassType($classType);
    }
    public function getFormClassType()
    {
        return $this->outilsFormulaire->getClassType();
    }

    public function setFormElement($element)
    {
        $this->outilsFormulaire->setElement($element);
    }

    public function getFormElement()
    {
        return $this->outilsFormulaire->getElement();
    }
    
    public function setFormForm($form)
    {
        $this->outilsFormulaire->setForm($form);
    }
    
    public function setFormPageResultat($page)
    {
        $this->outilsFormulaire->setPageResultat($page);
    }
    
    public function setFormPageResultatConfig($config)
    {
        $this->outilsFormulaire->setPageResultatConfig($config);
    }
    
    public function setFormTexteConfirmation($texte)
    {
        $this->outilsFormulaire->setTexteConfirmation($texte);
    }
    
    public function setFormTexteConfirmationEval($eval)
    {
        $this->outilsFormulaire->setTexteConfirmationEval($eval);
    }
    
    public function setFormTwigFormulaire($twigFormulaire)
    {
        $this->outilsFormulaire->setTwigFormulaire($twigFormulaire);
    }
    
    public function creerFormulaire($controller):void
    {
        $this->outilsFormulaire->creer($controller, $this->manager);
    }

    ////////////////////////////////////////////////////////////////////////
    // GESTION DE LA PAGE
    ////////////////////////////////////////////////////////////////////////
    
    public function getPageIdActuel()
    {
        return $this->outilsPage->getPageId();
    }

    public function getPagePageMere()
    {
        return $this->outilsPage->getPageMere();
    }

    // public function setPageParams($params)
    // {
    //     $this->outilsPage->setParams($params);
    // }
    
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