<?php

declare(strict_types=1);

namespace App\Tests\Domain\Tenant;

use App\Domain\Metrics\Metric;
use App\Domain\Tenant\ConfigMetrics;
use Generator;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(ConfigMetrics::class)]
//#[CoversFunction('__construct()')]
final class ConfigMetricsTest extends TestCase
{
    private const DEFAULT = [
        'Number of Threads'            => [
            'enabled'    => true,
            'constraint' => 'value < 30',
        ],
        'Thread / Files Ratio'         => [
            'enabled'    => true,
            'constraint' => 'value < 1',
        ],
        'Lines / Files Ratio'          => [
            'enabled'    => true,
            'constraint' => 'value < 40',
        ],
        'Files Changed'                => [
            'enabled'    => true,
            'constraint' => 'value < 30',
        ],
        'Lines Added'                  => [
            'enabled'    => true,
            'constraint' => 'value < 500',
        ],
        'Lines Removed'                => [
            'enabled'    => true,
            'constraint' => 'value < 500',
        ],
        'Replies per Thread Ratio'     => [
            'enabled'    => true,
            'constraint' => 'value < 2.5',
        ],
        'Alert Ratio'                  => [
            'enabled'    => true,
            'constraint' => 'value == 0',
        ],
        'Warning Ratio'                => [
            'enabled'    => true,
            'constraint' => 'value < 0.5',
        ],
        'Readability Ratio'            => [
            'enabled'    => true,
            'constraint' => 'value < 1',
        ],
        'Security Ratio'               => [
            'enabled'    => true,
            'constraint' => 'value == 0',
        ],
        'Number of unresolved threads' => [
            'enabled'    => true,
            'constraint' => 'value == 0',
        ],
    ];

    public static function generateDisabledConfigs(): Generator
    {
        yield 'empty' => [
            'config'          => [],
            'metricsDisabled' => 0,
        ];

        yield 'alert & warning ratio explicitly enabled' => [
            'config'          => [
                'Alert Ratio'   => [
                    'enabled' => true,
                ],
                'Warning Ratio' => [
                    'enabled' => true,
                ],
            ],
            'metricsDisabled' => 0,
        ];

        yield 'alert & warning ratio disabled' => [
            'config'          => [
                'Alert Ratio'   => [
                    'enabled' => false,
                ],
                'Warning Ratio' => [
                    'enabled' => false,
                ],
            ],
            'metricsDisabled' => 2,
        ];

        yield 'altered constraint for lines addition & removal' => [
            'config'          => [
                'Lines Added'   => [
                    'constraint' => 'value < 1000',
                ],
                'Lines Removed' => [
                    'constraint' => 'value < 1000',
                ],
            ],
            'metricsDisabled' => 0,
        ];
    }

    /**
     * @param array<string, array{enabled?: bool, constraint?: string}> $config
     */
    #[DataProvider('generateDisabledConfigs')]
    //#[CoversFunction('isMetricDisabled()')]
    public function testMetricIsDisabled(array $config, int $metricsDisabled): void
    {
        $configItemMetrics = new ConfigMetrics($config);
        $countDisabled     = 0;

        foreach (self::DEFAULT as $metricName => $metricConfig) {
            $countDisabled += $configItemMetrics->isMetricDisabled($metricName) === true ? 1 : false;
        }

        $this->assertSame($metricsDisabled, $countDisabled);
    }

    public static function generateAlteredConfigs(): Generator
    {
        $defaultHasConstraint = [
            Metric::NumberOfThreads->value           => false,
            Metric::ThreadsFilesRatio->value         => false,
            Metric::LinesFilesRatio->value           => false,
            Metric::FilesChanged->value              => false,
            Metric::LinesAdded->value                => false,
            Metric::LinesRemoved->value              => false,
            Metric::RepliesPerThreadRatio->value     => false,
            Metric::AlertRatio->value                => false,
            Metric::WarningRatio->value              => false,
            Metric::ReadabilityRatio->value          => false,
            Metric::SecurityRatio->value             => false,
            Metric::NumberOfUnresolvedThreads->value => false,
        ];

        yield 'empty' => [
            'config'         => [],
            'hasConstraints' => $defaultHasConstraint,
        ];

        yield 'alert & warning ratio disabled' => [
            'config'         => [
                Metric::AlertRatio->value   => [
                    'enabled' => false,
                ],
                Metric::WarningRatio->value => [
                    'enabled' => false,
                ],
            ],
            'hasConstraints' => $defaultHasConstraint,
        ];

        $constraint = 'value < 1000';
        yield 'altered constraint for lines addition & removal' => [
            'config'         => [
                Metric::LinesAdded->value   => [
                    'constraint' => $constraint,
                ],
                Metric::LinesRemoved->value => [
                    'constraint' => $constraint,
                ],
            ],
            'hasConstraints' => [
                                    Metric::LinesAdded->value   => true,
                                    Metric::LinesRemoved->value => true,
                                ] + $defaultHasConstraint,
        ];
    }

    /**
     * @param array<string, array{enabled?: bool, constraint?: string}> $config
     * @param array<string, bool>                                       $hasConstraints
     */
    #[DataProvider('generateAlteredConfigs')]
    //#[CoversFunction('isMetricDisabled()')]
    //#[CoversFunction('hasConstraint()')]
    //#[CoversFunction('getConstraint()')]
    public function testConstraintIsSet(array $config, array $hasConstraints): void
    {
        $configItemMetrics = new ConfigMetrics($config);

        foreach ($hasConstraints as $hasConstraintName => $hasConstraint) {
            $this->assertSame($hasConstraint, $configItemMetrics->hasConstraint($hasConstraintName));
            if (true === $hasConstraint) {
                $this->assertSame('value < 1000', $configItemMetrics->getConstraint($hasConstraintName));
            } else {
                $this->expectException(LogicException::class);
                $configItemMetrics->getConstraint($hasConstraintName);
            }
        }
    }
}
