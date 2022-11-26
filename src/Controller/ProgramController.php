<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render('Program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    #[Route('/{id<^[0-9]+$>}', name: 'by_id', methods: ['GET'])]
    public function show(int $id, Program $program, SeasonRepository $seasonRepository): Response
    {

        $seasons = $seasonRepository->findBy(['program' => $id]);

        return $this->render('Program/show.html.twig', [
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


        return $this->render('Program/season-show.html.twig', [
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
        return $this->render('Program/show_episode.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }
}