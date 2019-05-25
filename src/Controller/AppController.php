<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Form\GamesType;
use App\Form\ProfilType;
use App\Form\SearchType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppController extends AbstractController
{
    /**
     * @Route("/app", name="home")
     */
    public function index()
    {
        $user = $this->getUser()->getJeux();
        if ($user->isEmpty()){
            return $this->redirectToRoute("form_jeu");
        } else {
            return $this->redirectToRoute("view_games");
        }
    }

    /**
     * @Route("/app/ajouter_jeu", name="form_jeu")
     */
    public function addGame(Request $request, ObjectManager $manager){
        $game = new Game();

        $form = $this->createForm(GamesType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $user = $this->getUser();

            $data = $form->getData();
            foreach ($data->getGames() as $game){
                $user->addJeux($game);
                $manager->persist($user);
                $manager->flush();
            }
            $this->addFlash(
                'success',
                "Votre profil à bien été mis à jour !"
            );
            return $this->redirectToRoute('home');
        }

        return $this->render('app/form_jeu.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/app/profil", name="form_profil")
     */
    public function profil(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){

        $user = new User();

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isRequired()){
            $data = $form->getData();
            $hash = $encoder->encodePassword($user, $data->getPassword());
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->find($this->getUser());
            $user->setPassword($hash);
            $user->setEmail($data->getEmail());
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Votre mot de passe et votre email ont bien été mis à jour !"
            );
        }

        return $this->render('app/profil.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("app/profil/mes-jeux", name="view_games")
     */
    public function viewGame(){
        return $this->render('app/jeu.html.twig');
    }

    /**
     * @Route("app/recherche", name="form_recherche")
     */
    public function search(Request $request, ObjectManager $manager){

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isRequired()){
            $data = $form->getData();
            $manager = $this->getDoctrine()->getManager();
            $repository = $manager->getRepository(User::class);

            $query = $repository->createQueryBuilder('u')
                ->join('u.jeux', 'c')
                ->where('u.nom = :nom')
                ->orWhere('u.prenom = :prenom')
                ->orWhere('c.nom = :id')
                ->setParameter('nom', $data['nom'])
                ->setParameter('prenom', $data['prenom'])
                ->setParameter(':id', $data['games']->getNom())
                ->getQuery()

                ->getResult();

            return $this->render('app/resultat.html.twig', [
                'resultat' => $query
            ]);

        }

        return $this->render('app/rechercher.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
