<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionsRepository")
 */
class Questions
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
     * @ORM\Column(type="string", length=100)
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $authorname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="integer")
     */
    private $questionnumber;

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

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }


    //AUTHORNAME
    public function getAuthorname()
    {
        return $this->authorname;
    }

    public function setAuthorname($authorname)
    {
        $this->authorname = $authorname;

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

    //questionnumber
    public function getQuestionnumber()
    {
        return $this->questionnumber;
    }

    public function setQuestionnumber($questionnumber)
    {
        $this->questionnumber = $questionnumber;
        return $this;
    }
}
