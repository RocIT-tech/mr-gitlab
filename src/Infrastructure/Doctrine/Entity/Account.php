<?php

namespace App\Infrastructure\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'account', schema: 'config')]
class Account
{
    #[ORM\Id]
    #[ORM\Column(name: 'account_id', length: 36)]
    public readonly string $id;

    #[ORM\Column(length: 255)]
    public string $name;

    /**
     * @var Collection<int, Config>
     */
    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Config::class, orphanRemoval: true)]
    private Collection $configs;

    public function __construct(string $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
        $this->configs = new ArrayCollection();
    }

    /**
     * @return Collection<int, Config>
     */
    public function getConfigs(): Collection
    {
        return $this->configs;
    }

    public function addConfig(Config $config): self
    {
        if (!$this->configs->contains($config)) {
            $this->configs->add($config);
            $config->attachAccount($this);
        }

        return $this;
    }

    public function removeConfig(Config $config): self
    {
        if ($this->configs->removeElement($config)) {
            if ($config->getAccount() === $this) {
                $config->attachAccount(null);
            }
        }

        return $this;
    }
}
