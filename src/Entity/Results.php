<?php

/**
 * This file contains Results entity and its methods
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=App\Repository\ResultsRepository::class)
 */
class Results
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $fullName;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $raceTime;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     */
    private $distance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $placement;

    /**
     * @ORM\ManyToOne(targetEntity="Race", inversedBy="results")
     * @ORM\JoinColumn(nullable=false)
     */
    private $race;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getRaceTime(): ?string
    {
        return $this->raceTime;
    }

    public function setRaceTime(string $raceTime): self
    {
        $this->raceTime = $raceTime;

        return $this;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(string $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getPlacement(): ?int
    {
        return $this->placement;
    }

    public function setPlacement(int $placement): self
    {
        $this->placement = $placement;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }
}
