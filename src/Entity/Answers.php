<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswersRepository")
 */
class Answers
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
    private $questionid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $answer;

    /**
     * @ORM\Column(type="integer")
     */
    private $correctanswer;

     /**
     * @ORM\Column(type="string", length=100)
     */
    private $creatorname;


    public function getId()
    {
        return $this->id;
    }

    public function getQuestionid(): ?int
    {
        return $this->questionid;
    }

    public function setQuestionid(int $questionid): self
    {
        $this->questionid = $questionid;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getCorrectanswer()
    {
        return $this->correctanswer;
    }

    public function setCorrectanswer($correctanswer)
    {
        $this->correctanswer = $correctanswer;

        return $this;
    }

    //CREATORNAME
    public function getCreatorname()
    {
        return $this->creatorname;
    }

    public function setCreatorname($creatorname)
    {
        $this->creatorname = $creatorname;
        return $this;
    }


    //ANSWERNUMBER
    /*
    public function getAnswernumber()
    {
        return $this->answernumber;
    }

    public function setAnswernumber($answernumber)
    {
        $this->answernumber = $answernumber;
        return $this;
    }
    */


    //one who answered
    /*public function getOne_who_answered_id()
    {
        return $this->one_who_answered_id;
    }

    public function setOne_who_answered_id($one_who_answered_id)
    {
        $this->one_who_answered_id = $one_who_answered_id;
        return $this;
    }
    */
}
