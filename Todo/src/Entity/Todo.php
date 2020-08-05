<?php

namespace App\Entity;

use App\Repository\TodoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="`todo`")
 * @ORM\Entity(repositoryClass=TodoRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Todo implements \JsonSerializable
{
    //@ORM\GeneratedValue(strategy="AUTO")
    /**
     * @ORM\Id()
     * @ORM\Column(type="string" ,options={"unsigned": false})
     */
     private $id;

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @ORM\Column(type="text")
     */
    private $task;

    /**
     * @ORM\Column(type="boolean" )
     */
    private $performance;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="todo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTask(): ?string
    {
        return $this->task;
    }

    public function setTask(string $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getPerformance(): ?bool
    {
        return $this->performance;
    }

    public function setPerformance(bool $performance): self
    {
        $this->performance = $performance;

        return $this;
    }
    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            "task" => $this->getTask(),
            "performance" => $this->getPerformance(),
            "id"=>$this->getId()
        ];
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

