<?php

namespace App\Controller;

use App\Entity\Link;
use App\Entity\LinkGroup;
use App\Form\LinkGroupType;
use App\Form\LinkType;
use App\Repository\LinkRepository;
use App\Repository\LinkGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LinkGroupController extends AbstractController
{
    public function __construct(
        private LinkGroupRepository $linkGroupRepository,
        private LinkRepository $linkRepository,
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger
    )
    {
    }

    #[Route('/link/group', name: 'app_link_group_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $linkGroups = $this->linkGroupRepository->findAll();

        return $this->render('link_group/index.html.twig', [
            'linkGroups' => $linkGroups
        ]);
    }

    #[Route('/link/group/create', name: 'app_link_group_create')]
    #[Route('/link/group/{slug}/edit', name: 'app_link_group_edit')]
    public function create(?string $slug, Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $linkGroup = new LinkGroup();

        if ($slug) {
            $linkGroupDb = $this->linkGroupRepository->findOneBy(['slug' => $slug]);

            if ($linkGroupDb !== null) {
                $linkGroup = $linkGroupDb;
            }
        }

        $form = $this->createForm(LinkGroupType::class, $linkGroup);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $linkGroup->setSlug($this->slugger->slug($linkGroup->getName())->lower());

            $this->entityManager->persist($linkGroup);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_link_group_show', ['slug' => $linkGroup->getSlug()]);
        }



        return $this->render('link_group/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/link/group/{slug}/add-link', name: 'app_link_group_add_link')]
    public function addLinkToGroup(string $slug, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $linkGroup = $this->linkGroupRepository->findOneBy(['slug' => $slug]);

        if (!$linkGroup) {
            $this->redirectToRoute('app_link_group_index');
        }

        $link = new Link();

        $form = $this->createForm(LinkType::class, $link);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $link->setLinkGroup($linkGroup);

            $this->entityManager->persist($link);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_link_group_show', ['slug' => $linkGroup->getSlug()]);
        }

        return $this->render('link_group/add_link.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/link/group/{slug}', name: 'app_link_group_show')]
    public function show(string $slug): Response
    {
        $linkGroup = $this->linkGroupRepository->findOneBy(['slug' => $slug]);

        if (!$linkGroup) {
            throw $this->createNotFoundException();
        }

        return $this->render('link_group/show.html.twig', [
            'linkGroup' => $linkGroup,
            'links' => $this->linkRepository->findBy(['linkGroup' => $linkGroup])
        ]);
    }
}
