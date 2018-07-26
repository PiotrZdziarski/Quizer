<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EndingquotesRepository")
 */
class Endingquotes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quizid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $quote;

    /**
     * @ORM\Column(type="integer")
     */
    private $minanswers;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxanswers;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $authorname;

    public function getId()
    {
        return $this->id;
    }

    public function getQuizid(): ?int
    {
        return $this->quizid;
    }

    public function setQuizid(int $quizid): self
    {
        $this->quizid = $quizid;

        return $this;
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function setQuote(string $quote): self
    {
        $this->quote = $quote;

        return $this;
    }

    public function getMinanswers(): ?int
    {
        return $this->minanswers;
    }

    public function setMinanswers(int $minanswers): self
    {
        $this->minanswers = $minanswers;

        return $this;
    }

    public function getMaxanswers(): ?int
    {
        return $this->maxanswers;
    }

    public function setMaxanswers(int $maxanswers): self
    {
        $this->maxanswers = $maxanswers;

        return $this;
    }

    //title
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    //image
    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }


    //authorname
    public function getAuthorname()
    {
        return $this->authorname;
    }

    public function setAuthorname($authorname)
    {
        $this->authorname = $authorname;
        return $this;
    }

}
