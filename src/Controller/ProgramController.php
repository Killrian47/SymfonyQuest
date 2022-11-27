<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Repository\CategoryRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function newProgram(Request $request, ProgramRepository $programRepository): Response
    {
        $program = new Program();

        // Create the form, linked with $category
        $form = $this->createForm(ProgramType::class, $program);

        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
            $programRepository->save($program, true);
            return $this->redirectToRoute('program_index');
            // Deal with the submitted data
            // For example : persiste & flush the entity
            // And redirect to a route that display the result
        }

        // Render the form (best practice)
        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id<^[0-9]+$>}', name: 'by_id', methods: ['GET'])]
    public function show(int $id, Program $program, SeasonRepository $seasonRepository): Response
    {

        $seasons = $seasonRepository->findBy(['program' => $id]);

        return $this->render('program/show.html.twig', [
            'id' => $id,
            'program' => $program,
            'seasons' => $seasons
        ]);
    }

    #[Route('/{program_id}/seasons/{season_id<\d+>}', name: 'season_show')]
    #[Entity('program', options: ['id' => 'program_id'])]
    #[Entity('season', options: ['id' => 'season_id'])]
    public function showSeason(int $program_id, int $season_id, Program $program, Season $season, EpisodeRepository $episodeRepository): Response
    {
//        $program = $programRepository->findOneBy(['id' => $program_id]);
//        $season = $seasonRepository->findBy(['id' => $season_id]);
        $episodes = $episodeRepository->findBy(['season' => $season_id]);


        return $this->render('program/season-show.html.twig', [
            'season' => $season,
            'episodes' => $episodes,
            'program' => $program
        ]);
    }

    #[Route('/{program_id<\d+>}/seasons/{season_id<\d+>}/episode/{episode_id<\d+>}', name: 'episode_show')]
    #[Entity('program', options: ['id' => 'program_id'])]
    #[Entity('season', options: ['id' => 'season_id'])]
    #[Entity('episode', options: ['id' => 'episode_id'])]
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {
        return $this->render('program/show_episode.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }
}