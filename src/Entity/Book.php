<?php

namespace App\Entity;

use App\Entity\Book\Score;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'books')]
    private Collection $categories;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Embedded(class: Score::class)]
    private ?Score $score = null;

    public function __construct()
    { 
        $this->score = Score::create();
        $this->categories = new ArrayCollection();
    }

    public static function create() : self {
        return new Book();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function update(string $title, ?string $image, ?string $description, ?Score $score, Category ...$categories) 
    {
        $this->title = $title;
        $this->image = $image;
        $this->description = $description;
        $this->score = $score;

        $this->updateCategories(...$categories);
    }

    function updateCategories(Category ...$categories) {
        $originalCategories = new ArrayCollection();
        foreach($this->categories as $category) {
            $originalCategories->add($categories);
        }

        //Remove Categories
        foreach ($originalCategories as $originalCategory) {
            if (!in_array($originalCategory, $categories)) {
                $this->removeCategory($originalCategory);
            }
        }

        //Add Categories
        foreach ($categories as $newCategory) {
            if (!$originalCategories->contains($newCategory)) {
                $this->addCategory($newCategory);
            }
        }
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getScore(): Score
    {
        return $this->score;
    }

    public function setScore(Score $score): self
    {
        $this->score = $score;
        return $this;
    }
}
