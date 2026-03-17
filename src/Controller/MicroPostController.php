<?php

namespace App\Controller;

use App\Service\MicroPostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class MicroPostController extends AbstractController
{
    public function __construct(
        private MicroPostService $microPostService,
    ) {}

    #[Route('/micro/post', name: 'app_micro_post')]
    public function index(): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $this->microPostService->findAll(),
        ]);
    }

    #[Route('/micro/post/create', name: 'app_micro_post_create')]
    public function create(Request $request): Response
    {
        $form = $this->microPostService->createForm($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Micro post created!');

            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/micro/post/{id}/edit', name: 'app_micro_post_edit')]
    public function edit(int $id, Request $request): Response
    {
        $form = $this->microPostService->editForm($id, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Micro post updated!');

            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/micro/post/{id}/like', name: 'app_micro_post_like', methods: ['POST'])]
    public function like(int $id, Request $request): Response
    {
        $this->microPostService->like($id);

        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?: $this->generateUrl('app_micro_post'));
    }

    #[Route('/micro/post/{id}', name: 'app_micro_post_show')]
    public function show(int $id, Request $request): Response
    {
        $post = $this->microPostService->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post not found.');
        }

        $commentForm = $this->microPostService->createCommentForm($id, $request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->addFlash('success', 'Comment added!');

            return $this->redirectToRoute('app_micro_post_show', ['id' => $id]);
        }

        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
            'comment_form' => $commentForm,
        ]);
    }
}