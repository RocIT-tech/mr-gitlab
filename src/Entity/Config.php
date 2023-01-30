<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SensitiveParameter;

#[ORM\Entity()]
#[ORM\Table(name: 'config', schema: 'config', uniqueConstraints: [
    new ORM\UniqueConstraint(
        name: 'UNIQUE_CONFIG_PER_HOST_IN_ACCOUNT',
        fields: ['account', 'host'],
    )
])]
class Config
{
    #[ORM\Id]
    #[ORM\Column(name: 'config_id', length: 36)]
    public readonly string $id;

    #[ORM\Column(length: 255)]
    public string $host;

    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Column(length: 255)]
    public string $token;

    #[ORM\ManyToOne(inversedBy: 'configs')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'account_id')]
    private ?Account $account = null;

    /**
     * @var Collection<int, ConfigMetric>
     */
    #[ORM\OneToMany(mappedBy: 'config', targetEntity: ConfigMetric::class, orphanRemoval: true)]
    private Collection $configMetrics;

    public function __construct(
        string                       $id,
        string                       $host,
        string                       $name,
        #[SensitiveParameter] string $token,
    ) {
        $this->id    = $id;
        $this->host  = $host;
        $this->name  = $name;
        $this->token = $token;
        $this->configMetrics = new ArrayCollection();
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function attachAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Collection<int, ConfigMetric>
     */
    public function getConfigMetrics(): Collection
    {
        return $this->configMetrics;
    }

    public function addConfigMetric(ConfigMetric $configMetric): self
    {
        if (!$this->configMetrics->contains($configMetric)) {
            $this->configMetrics->add($configMetric);
            $configMetric->attachToConfig($this);
        }

        return $this;
    }
}
