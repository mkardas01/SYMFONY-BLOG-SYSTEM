<?php

namespace App\Twig\Components;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('LikeComponent')]
final class LikeComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public Post $post;
    public $isLiked;
    public $isDisliked;

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[LiveAction]
    public function like(){
        $this->post->addUserThatLiked($this->security->getUser());
        $this->isLiked = true;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function undoLike(){
        $this->post->removeUserThatLiked($this->security->getUser());
        $this->isLiked = false;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function dislike(){
        $this->post->addUsersThatDontLike($this->security->getUser());
        $this->isDisliked = true;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function undoDislike(){
        $this->post->removeUsersThatDontLike($this->security->getUser());
        $this->isDisliked = false;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

}
