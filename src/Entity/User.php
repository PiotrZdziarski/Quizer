<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique = true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100, unique = true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $online;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOnline()
    {
        return $this->online;
    }

    public function setOnline($online)
    {
        $this->online = $online;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    public  function getRoles()
    {
        return [
            'ROLE_USER'
        ];
    }

    public function getSalt(){}
    public function eraseCredentials(){}
    public function serialize() {
        $this->online = 1;
        return serialize([
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->online
        ]);
    }
    public function  unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->online
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

}
