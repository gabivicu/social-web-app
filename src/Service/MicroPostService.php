<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class MicroPostService
{
    public function __construct(
        private MicroPostRepository $microPostRepository,
        private EntityManagerInterface $em,
        private FormFactoryInterface $formFactory,
        private Security $security,
    ) {}

    public function findAll(): array
    {
        return $this->microPostRepository->findAllWithComments();
    }

    public function find(int $id): ?MicroPost
    {
        return $this->microPostRepository->find($id);
    }

    public function createForm(Request $request): FormInterface
    {
        $microPost = new MicroPost();
        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $microPost->setCreated(new \DateTime());
            $this->em->persist($microPost);
            $this->em->flush();
        }

        return $form;
    }

    public function editForm(int $id, Request $request): FormInterface
    {
        $microPost = $this->microPostRepository->find($id);
        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
        }

        return $form;
    }

    public function like(int $id): void
    {
        $microPost = $this->microPostRepository->find($id);
        $microPost->setLikes($microPost->getLikes() + 1);
        $this->em->flush();
    }

    public function createCommentForm(int $postId, Request $request): FormInterface
    {
        $comment = new Comment();
        $form = $this->formFactory->create(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $this->microPostRepository->find($postId);
            $comment->setPost($post);
            $comment->setAuthor($this->security->getUser());
            $comment->setAuthorName($this->security->getUser()->getUserIdentifier());
            $comment->setCreatedAt(new \DateTime());
            $this->em->persist($comment);
            $this->em->flush();
        }

        return $form;
    }
}