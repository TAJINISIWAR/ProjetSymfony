<?php
#php pour indiquer que le code est de langage php
#le namespace est pour indiquer que le package de cette classe est App\Controller
namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User;

use App\Entity\Cours;
use App\Entity\Classe;
use App\Form\ClasseType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
#Import de package pour l'utilisation des annotations pour les routes
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
#Import de package pour la classe AbstractController
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Searchbyname;
use App\Form\SearchbynameType;
#le nom de la classe est IndexController et elle hérite de la classe AbstractController
class IndexController extends AbstractController
{
 

  /**
 *@Route("/",name="Cours_list")
 */
 public function home(Request $request)
 {
 $Searchbyname = new Searchbyname();
 $form = $this->createForm(SearchbynameType::class,$Searchbyname);
 $form->handleRequest($request);
 //initialement le tableau des cours est vide,
 //c.a.d on affiche les cours que lorsque l'utilisateur
 //clique sur le bouton rechercher
 $Cours= [];
 
 if($form->isSubmitted() && $form->isValid()) {
 //on récupère le nom du cours tapé dans le formulaire
 $nom = $Searchbyname->getNom(); 
 if ($nom!="")
 //si on a fourni un nom du cours on affiche tous les courss ayant ce nom
 $Cours= $this->getDoctrine()->getRepository(Cours::class)->findBy(['nom' => $nom] );
 else 
 //si aucun nom n'est fourni on affiche tous les cours
 $Cours= $this->getDoctrine()->getRepository(Cours::class)->findAll();
 }
 return $this->render('cours/index.html.twig',[ 'form' =>$form->createView(), 'Cours' => $Cours]); 
 }
/**
     * @Route("/Cours/new", name="new_Cours")
     * Method({"GET", "POST"})
     */
    public function new(Request $request) {
    #Creation d'un objet Cours
        $Cours = new Cours();
    #Creation d'un formulaire  avec la méthode createFormBuilder on va remplir le formulaire par les objets Cours
        $form = $this->createFormBuilder($Cours)
    #Ces sont les valeurs qui seront en saisie dans le formulaire
          ->add('nom', TextType::class)
          ->add('Type', TextType::class)
          ->add('Coach', TextType::class)
          ->add('Salle', TextType::class)
              #un botton Save
          ->add('save', SubmitType::class, array(
            'label' => 'Créer')
    #une fois c'est terminé ,je fait un appel au méthode getForm() pour la mise en forme
          )->getForm();
          
  
        $form->handleRequest($request);
  #Si le formulaire est valid ,on prend les valeurs saisies 
        if($form->isSubmitted() && $form->isValid()) {
          $Cours = $form->getData();
          $entityManager = $this->getDoctrine()->getManager();
   #pour enregistrer les données 
          $entityManager->persist($Cours);
          $entityManager->flush();
  #pour appeler la rout Cours_list et afficher les produits 
          return $this->redirectToRoute('Cours_list');
        }
  #Si ce n'est pas valide ,on restra sur la meme page du formulaire
        return $this->render('Cours/new.html.twig',['form' => $form->createView()]);
    }
  
  #on a un parametre id qu'on utilsera dans la route 
    /**
     * @Route("/Cours/{id}", name="Cours_show")
     */
    public function show($id) {
  #à l'aide de la méthode getDoctrine ,on va récuperer l'objet Cours ,qui a l'id passé comme parametre ,de la base de donnée
        $Cours = $this->getDoctrine()->getRepository(Cours::class)->find($id);
  #on va mettre l'objet récuperé dans un table
  #on a fait un appel au show.html.twig
        return $this->render('Cours/show.html.twig', array('Cours' => $Cours));
      }
/**
     * @Route("/Cours/edit/{id}", name="edit_Cours")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $Cours = new Cours();
        $Cours = $this->getDoctrine()->getRepository(Cours::class)->find($id);
  
        $form = $this->createFormBuilder($Cours)
          ->add('nom', TextType::class)
          ->add('type', TextType::class)
          ->add('coach', TextType::class)
          ->add('salle', TextType::class)
          ->add('save', SubmitType::class, array(
            'label' => 'Modifier'         
          ))->getForm();
  
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
  
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->flush();
  
          return $this->redirectToRoute('Cours_list');
        }
  
        return $this->render('Cours/edit.html.twig', ['form' => $form->createView()]);
      }
/**
     * @Route("/Cours/delete/{id}",name="delete_Cours")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id) {
        #On récupere l'objet de la base de donnée qui a la valeur de id 
        $Cours = $this->getDoctrine()->getRepository(Cours::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        #On utilise la méthode remove pour supprimer l'objet
        $entityManager->remove($Cours);
        #On l'utilise pour enregistrer la suppression 
        $entityManager->flush();
        $response = new Response();
        $response->send();
        #une fois c'est terminé ,on retourne à la liste des Cours
        return $this->redirectToRoute('Cours_list');
      }
      /**
     * @Route("/Classe/new", name="new_Classe")
     * Method({"GET", "POST"})
     */
    public function newClasse(Request $request) {
        $Classe = new Classe();
      
        $form = $this->createForm(ClasseType::class,$Classe);
  
        $form->handleRequest($request);
  
        if($form->isSubmitted() && $form->isValid()) {
          $Cours = $form->getData();
  
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($Classe);
          $entityManager->flush();
        }
        return $this->render('Cours/newClasse.html.twig',['form' => $form->createView()]);

    }
 


}