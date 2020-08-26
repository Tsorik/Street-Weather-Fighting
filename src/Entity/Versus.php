<?php

namespace App\Entity;

use App\Repository\VersusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=VersusRepository::class)
 * @UniqueEntity(
 *      fields= {"city1"},
 * )
 * @UniqueEntity(
 *      fields= {"city2"},
 * )
 */
class Versus
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city2;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="versus", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getcity1(): ?string
    {
        return $this->city1;
    }

    public function setcity1(string $city1): self
    {
        $this->city1 = $city1;

        return $this;
    }

    public function getcity2(): ?string
    {
        return $this->city2;
    }

    public function setcity2(string $city2): self
    {
        $this->city2 = $city2;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setVersus($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getVersus() === $this) {
                $comment->setVersus(null);
            }
        }

        return $this;
    }
}
