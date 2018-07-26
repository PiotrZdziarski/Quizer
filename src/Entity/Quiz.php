<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 */
class Quiz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length = 100)
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $solutions;

    /**
     * @ORM\Column(type="integer")
     */
    private $comments;

    /**
     * @ORM\Column(type="integer")
     */
    private $typeid;

    /**
     * @ORM\Column(type="integer")
     */
    private $categoryid;

    /**
     * @ORM\Column(type="integer")
     */
    private $public;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length = 100)
     */
    private $authorname;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function getId(){return $this->id;}
    public function getTitle(){return $this->title;}
    public function getSolutions(){return $this->solutions;}
    public function getComments(){return $this->comments;}
    public function getTypeid(){return $this->typeid;}
    public function getCategoryid(){return $this->categoryid;}
    public function getPublic(){return $this->public;}
    public function getDescription(){return $this->description;}
    public function getImage(){return $this->image;}
    public function getAuthorname(){return $this->authorname;}
    public function getDate(){return $this->date;}

    public function setId($id){$this->id = $id; return $this;}
    public function setTitle($title){$this->title = $title;return $this;}
    public function setSolutions($solutions){$this->solutions = $solutions;return $this;}
    public function setComments($comments){$this->comments = $comments;return $this;}
    public function setTypeid($typeid){$this->typeid = $typeid;return $this;}
    public function setCategoryid($categoryid){$this->categoryid = $categoryid;return $this;}
    public function setPublic($public){$this->public = $public;return $this;}
    public function setDescription($description){$this->description = $description;return $this;}
    public function setImage($image){$this->image = $image;return $this;}
    public function setAuthorname($authorname){$this->authorname = $authorname;return $this;}
    public function setDate($date){$this->date = $date;return $this;}


}
