<?php

namespace App\Service;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class MicroPostService
{
    public function __construct(
        private MicroPostRepository $microPostRepository,
        private EntityManagerInterface $em,
        private FormFactoryInterface $formFactory,
    ) {}

    public function findAll(): array
    {
        return $this->microPostRepository->findAll();
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
}