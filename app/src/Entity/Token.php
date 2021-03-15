<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class Token implements UserInterface
{
    private $token;

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getRoles() {
        return [];
    }

    public function getPassword()
    {
        return "";
    }

    public function getUsername()
    {
        return $this->token;
    }

    public function getSalt()
    {
        return "";
    }

    public function eraseCredentials()
    {
        
    }
}
