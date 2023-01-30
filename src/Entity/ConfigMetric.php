<?php

namespace App\Entity;

use App\Metrics\Metric;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'config_metric', schema: 'config')]
class ConfigMetric
{
    #[ORM\Id]
    #[ORM\Column(name: 'config_metric_id', length: 36)]
    public readonly string $id;

    #[ORM\Column(length: 40)]
    public Metric $key;

    #[ORM\Column]
    public bool $enabled;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $assert = null;

    #[ORM\ManyToOne(inversedBy: 'configMetrics')]
    #[ORM\JoinColumn(referencedColumnName: 'config_id', nullable: false)]
    private ?Config $config = null;

    public function __construct(string $id, Metric $key, bool $enabled, ?string $assert)
    {
        $this->id      = $id;
        $this->key     = $key;
        $this->enabled = $enabled;
        $this->assert  = $assert;
    }

    public function attachToConfig(?Config $config): self
    {
        $this->config = $config;

        return $this;
    }
}
