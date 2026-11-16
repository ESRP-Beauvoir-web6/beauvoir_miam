<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/recipe')]
final class RecipeController extends AbstractController
{
    #[Route(name: 'app_recipe_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->remove('status');
        $form->handleRequest($request);
        $user = $this->getUser();
        $status = "En attente";

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setAuthor($user);
            $recipe->setStatus($status);
            $image = $form->get('image')->getData();

            if ($image) {
                $filename = $fileUploader->upload($image);
                $recipe->setImage($filename);
            }

            $entityManager->persist($recipe);
            $entityManager->flush();

            return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_show', methods: ['GET'])]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $entityManager, FileUploader $fileUploader, Filesystem $filesystem): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        if (!$this->IsGranted('ROLE_ADMIN')) {
            $form->remove('status');
        }
        $form->handleRequest($request);
        $oldFilename = $recipe->getImage();

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $filename = $fileUploader->upload($image);
                if ($oldFilename) {
                    $oldFilepath = $this->getParameter('images_directory').'/'.$oldFilename;
                    if ($filesystem->exists($oldFilepath)) {
                        $filesystem->remove($oldFilepath);
                    }
                }
                $recipe->setImage($filename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->getPayload()->getString('_token'))) {
            $oldFilename = $recipe->getImage();

            if ($oldFilename) {
                $oldFilepath = $this->getParameter('images_directory').'/'.$oldFilename;
                if ($filesystem->exists($oldFilepath)) {
                    $filesystem->remove($oldFilepath);
                }
            }
            $entityManager->remove($recipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
