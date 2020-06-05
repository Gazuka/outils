<?php

namespace Gazuka\Outils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;

class OutilsFormulaire {
    
    //Reçus dans le constructeur
    private $request;               //Objet Request
    
    //Setters obligatoires
    private $element;               //Objet que l'on souhaite obtenir avec ce formulaire
    private $classType;             //Classe du formulaire (ObjetType::class)
    private $twigformulaire;        //Chemin du template du formulaire
    private $pageResultat;          //Nom de la page de redirection après validation du formulaire
    private $texteConfirmation;     //Texte affiché lors de la validation du formulaire
     
    //Setters facultatifs
    private $pageResultatConfig = array();
    private $dependances = null;           //Le tableau de ses dépendances sous la forme ['Dependances' => 'Element'] (les noms de dépendances prennent un "s", l'élément reste au singulier !)
    private $texteConfirmationEval = null; //Permet de dynamiser le 'texteConfirmation' en remplaçant des sections du code (ex : $variables['texteConfirmationEval']["###"] = '$element->getNom();';)                                
    private $deletes = null;               //Le tableau des objets susceptible de devenir orphelin sous la forme ['findBy' => 'element', 'classEnfant' => 'sousElement', 'repo' => $repoSousElement] (element : nom de l'élément actif dans la BDD, sousElement : nom de la sous classe au pluriel, repoSousElement : repository de la sous classe)
    private $actions = null;               //Le tableau
    
    //Controller permet d'utiliser les actions
    private $controller;
    private $manager;

    //A créer obligatoirement dans le controller et envoyer via le setter
    private $form;                  //Formulaire créé dans le controller

    //Information qui seront utile à notre CONTROLLER //??? pas possible ici ????????????????????????????????????????????????????????
    private $messagesFlash = array(); 
    private $redirect = false;

    /*========================================================================================*/
    /*========================================================================================*/
    /*========================================================================================*/
    /** FONCTIONS MAGIQUES ********************************************************************/

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }
    /*========================================================================================*/
    /*========================================================================================*/
    /*========================================================================================*/
    /** FONCTIONS GET ET SET ******************************************************************/

    public function setActions($actions) 
    {
        $this->actions = $actions;
    }
    public function getClassType()
    {
        return $this->classType;
    }
    public function setClassType($classType)
    {
        $this->classType = $classType;
    }
    public function getElement()
    {
        return $this->element;
    }
    public function setElement($element)
    {
        $this->element = $element;
    }
    public function getForm()
    {
        return $this->form->createView();
    }
    public function setForm($form)
    {
        $this->form = $form;
    }
    public function getMessagesFlash()
    {
        return $this->messagesFlash;
    }
    public function setPageResultat($page)
    {
        $this->pageResultat = $page;
    }
    public function getPageResultatConfig()
    {
        return $this->pageResultatConfig;
    }
    public function setPageResultatConfig($config)
    {
        $this->pageResultatConfig = $config;
    }
    public function getRedirect()
    {
        if($this->redirect == true)
        {
            return $this->pageResultat;
        }
        else
        {
            return null;
        }
    }
    public function setTexteConfirmation($texte)
    {
        $this->texteConfirmation = $texte;
    }
    public function setTexteConfirmationEval($eval)
    {
        $this->texteConfirmationEval = $eval;
    }
    public function getTwigFormulaire()
    {
        return $this->TwigFormulaire;
    }
    public function setTwigFormulaire($twigFormulaire)
    {
        $this->TwigFormulaire = $twigFormulaire;
    }
    
    /*========================================================================================*/
    /*========================================================================================*/
    /*========================================================================================*/
    /** FONCTIONS PUBLIQUES *******************************************************************/
    
    public function creer($controller, $manager):void
    {
        $this->controller = $controller;
        $this->manager = $manager;
        
        $this->form->handleRequest($this->request);

        //On vérifie que le formulaire soit soumis et valide
        if($this->form->isSubmitted() && $this->form->isValid()) 
        {
            //On effectue les actions si besoins pour modifier l'element
            if($this->actions != null)
            {
                $this->methode_Actions();
            }
            
            //On persist l'élément
            $this->manager->persist($this->element);

            //On persist ses dependances
            if($this->dependances != null)
            {
                $this->methode_Dependances();
            }

            //On delete ses dependances orphelines...
            if($this->deletes != null)
            {
                $this->methode_Deletes();
            }

            // //On enregistre le tout
            // $this->manager->flush();

            //On dynamise le texte de confirmation du formulaire
            if($this->texteConfirmationEval != null)
            {
                $this->methode_DynamiseTexte();
            }
            
            //On prépare un message de validation
            array_push($this->messagesFlash, ['success', $this->texteConfirmation]);

            //On prépare la redirection
            $this->redirect = true;

            $this->manager->flush(); //On enregistre maintenant afin de donner un id à l'élément !
            $this->pageResultatConfig[strtolower('id'.substr(strrchr(get_class($this->element), "\\"), 1))] = $this->element->getId();
        }
    }

    /*========================================================================================*/
    /*========================================================================================*/
    /*========================================================================================*/
    /** FONCTIONS PRIVES **********************************************************************/

    /** Permet d'effectuer des actions lors de la validation du formulaire afin de modifier l'element */
    private function methode_Actions()
    {
        foreach($this->actions as $action)
        {
            //On récupére ici les variables : $name, $params
            extract($action);
            //On lance la fonction qui retourne l'élément modifié
            $this->element = $this->controller->$name($this->element, $params);
        }
    }

    /** Permet de modifier les dépendances d'un élement d'une classe quelconque */
    private function methode_Dependances()
    {
        foreach($this->dependances as $dependance => $elem)
        {
            //On récupére les objets du type dépendance qui se raccroche à notre element
            eval('$objets = $this->element->get'.$dependance.'();');
            //Pour chacun des objets dépendant, on ajoute notre élement
            foreach($objets as $objet)
            {
                //Il faut utiliser addElement pour les relation ManyToMany et SetElement pour le reste, si la fonction addElement existe on l'utilise...
                if(method_exists($objet, 'add'.$elem))
                {
                    eval('$objet->add'.$elem.'($this->element);'); 
                }
                else
                {
                    eval('$objet->set'.$elem.'($this->element);'); 
                }
                $this->manager->persist($objet);
            }
        }
    }

    /** Permet de supprimer les objets orphelins ayant un lien avec notre élément */
    private function methode_Deletes() // ??? revoir l'histoire des repo si possibilité d'automatiser la chose !!!!!!!!!!!!!
    {
        foreach($this->deletes as $delete)
        {
            //On récupére ici les variables : $findBy, $classEnfant, $repo
            extract($delete); 
            //Récupére tous les sousElement de notre Element
            $recup = $repo->findBy([$findBy => $this->element]);
            //Pour chaque sousElement, on vérifie si il doit être supprimer ou pas
            foreach($recup as $elem)
            {
                eval('$elems = $this->element->get'.$classEnfant.'();');
                if(!$elems->contains($elem))
                {
                    $this->manager->remove($elem);
                }
            }
        }
    }
    
    /** Permet de dynamiser des emplacements spécifiques d'un message */
    private function methode_DynamiseTexte()
    {
        foreach($this->texteConfirmationEval as $key => $valeur)
        {
            eval('$valeur = '.$valeur);
            $this->texteConfirmation = str_replace($key, $valeur, $this->texteConfirmation);
        }
    }

}