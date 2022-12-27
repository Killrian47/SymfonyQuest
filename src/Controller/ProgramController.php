<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\User;
use App\Form\CommentsType;
use App\Form\ProgramType;
use App\Form\SearchProgramType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Repository\UserRepository;
use App\Service\ProgramDuration;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository, RequestStack $requestStack, Request $request): Response
    {
        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findByProgramName($search);
        } else {
            $programs = $programRepository->findAll();
        }
        $session = $requestStack->getSession();
        if (!$session->has('total')) {
            $session->set('total', 0); // if total doesn’t exist in session, it is initialized.
        }

        return $this->renderForm('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form,

        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_ADMIN')]
    public function newProgram(Request $request, ProgramRepository $programRepository, MailerInterface $mailer): Response
    {
        $program = new Program();

        // Create the form, linked with $category
        $form = $this->createForm(ProgramType::class, $program);

        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->slugger->slug($program->getTitle());
            $program->setSlug($slug);

            $program->setOwner($this->getUser());

            $programRepository->save($program, true);

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('Email/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);

            $this->addFlash('success', 'The new comment has been created');

            return $this->redirectToRoute('program_index');
            // Deal with the submitted data
            // For example : persiste & flush the entity
            // And redirect to a route that display the result
        }

        // Render the form (best practice)
        return $this->renderForm('Program/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'by_id', methods: ['GET'])]
    public function show(Program $program, SeasonRepository $seasonRepository, ProgramDuration $programDuration): Response
    {
        $seasons = $seasonRepository->findBy(['program' => $program]);


        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
            'programDuration' => $programDuration->calculate($program)
        ]);
    }

    #[Route('/{id}/watchlist', name: 'watchlist')]
    public function addToWatchList(Program $program, UserRepository $userRepository)
    {
        $user = $this->getUser();
        if (!$user->isInWatchList($program  )) {
            $user->addToWatchList($program);
        } else {
            $user->removeFromWatchList($program);
        }

        $userRepository->save($user, true);

        return $this->json([
            'isInWatchList' => $user->isInWatchList($program)
        ]);
    }

    #[Route('/{slug}/seasons/{season_id<\d+>}', name: 'season_show')]
    #[Entity('program', options: ['id' => 'program_id'])]
    #[Entity('season', options: ['id' => 'season_id'])]
    public function showSeason(string $slug, int $season_id, ProgramRepository $programRepository, SeasonRepository $seasonRepository, EpisodeRepository $episodeRepository): Response
    {
        $program = $programRepository->findBy(['slug' => $slug]);
        $season = $seasonRepository->findBy(['id' => $season_id]);
        $episodes = $episodeRepository->findBy(['season' => $season_id]);

        return $this->render('program/season-show.html.twig', [
            'season' => $season,
            'episodes' => $episodes,
            'program' => $program
        ]);
    }

    #[Route('/{slug}/seasons/{season_id<\d+>}/episode/{episode_id<\d+>}', name: 'episode_show')]
    #[Entity('season', options: ['id' => 'season_id'])]
    #[Entity('episode', options: ['id' => 'episode_id'])]
    public function showEpisode(Program $program, Season $season, Episode $episode, MailerInterface $mailer, Request $request, CommentRepository $commentRepository, ?UserInterface $user): Response
    {
        $comment = new Comment();

        $comments = $episode->getComments();

        // Create the form, linked with $category
        $form = $this->createForm(CommentsType::class, $comment);

        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEpisode($episode);
            $comment->setUser($user);

            $commentRepository->save($comment, true);

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Un nouveau commentaire vient d\'être publié !')
                ->html($this->renderView('Email/newCommentEmail.html.twig', ['comment' => $comment]));

            $mailer->send($email);

            $this->addFlash('success', 'A new comment has been created');

            // Deal with the submitted data
            // For example : persiste & flush the entity
            // And redirect to a route that display the result
        }

        return $this->renderForm('program/show_episode.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form,
            'comments' => $comments,
            'user' => $user
        ]);
    }
}