<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name:'posts')]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Type('string')]
    #[Assert\Length(
        max:100,
        maxMessage: 'Title too long',
    )]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Assert\Type('string')]
    #[Assert\Length(
        max:3000,
        maxMessage: 'Content too long',
    )]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'likedPosts')]
    #[ORM\JoinTable('likes')]
    private Collection $userThatLiked;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'dislikedPosts')]
    #[ORM\JoinTable('dislikes')]
    private Collection $usersThatDontLike;

    public function __construct()
    {
        $this->userThatLiked = new ArrayCollection();
        $this->usersThatDontLike = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserThatLiked(): Collection
    {
        return $this->userThatLiked;
    }

    public function addUserThatLiked(User $userThatLiked): static
    {
        if (!$this->userThatLiked->contains($userThatLiked)) {
            $this->userThatLiked->add($userThatLiked);
            $userThatLiked->addLikedPost($this);
        }

        return $this;
    }

    public function removeUserThatLiked(User $userThatLiked): static
    {
        if ($this->userThatLiked->removeElement($userThatLiked)) {
            $userThatLiked->removeLikedPost($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersThatDontLike(): Collection
    {
        return $this->usersThatDontLike;
    }

    public function addUsersThatDontLike(User $usersThatDontLike): static
    {
        if (!$this->usersThatDontLike->contains($usersThatDontLike)) {
            $this->usersThatDontLike->add($usersThatDontLike);
            $usersThatDontLike->addDislikedPost($this);
        }

        return $this;
    }

    public function removeUsersThatDontLike(User $usersThatDontLike): static
    {
        if ($this->usersThatDontLike->removeElement($usersThatDontLike)) {
            $usersThatDontLike->removeDislikedPost($this);
        }

        return $this;
    }
}
