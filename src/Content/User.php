<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

class User
{
    protected $id;
    protected $name;
    protected $bio;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return User
     */
    public function setName($name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param mixed $bio
     * @return User
     */
    public function setBio($bio): User
    {
        $this->bio = $bio;
        return $this;
    }
}
