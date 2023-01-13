<?php

declare(strict_types=1);

namespace App\Tests\Gitlab\Config;

use App\Gitlab\Config\ConfigItemMetrics;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * @coversDefaultClass \App\Gitlab\Config\ConfigItemMetrics
 * @covers ::__construct()
 */
final class ConfigItemMetricsTest extends TestCase
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

    public function generateDisabledConfigs(): Generator
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
     * @dataProvider generateDisabledConfigs
     *
     * @covers ::isMetricDisabled()
     *
     * @param array<string, array{enabled?: bool, constraint?: string}> $config
     */
    public function testMetricIsDisabled(array $config, int $metricsDisabled): void
    {
        $configItemMetrics = new ConfigItemMetrics($config);
        $countDisabled     = 0;

        foreach (self::DEFAULT as $metricName => $metricConfig) {
            $countDisabled += $configItemMetrics->isMetricDisabled($metricName) === true ? 1 : false;
        }

        $this->assertSame($metricsDisabled, $countDisabled);
    }

    public function generateAlteredConfigs(): Generator
    {
        yield 'empty' => [
            'config'      => [],
            'hasConstraints' => [
                'Number of Threads'            => false,
                'Thread / Files Ratio'         => false,
                'Lines / Files Ratio'          => false,
                'Files Changed'                => false,
                'Lines Added'                  => false,
                'Lines Removed'                => false,
                'Replies per Thread Ratio'     => false,
                'Alert Ratio'                  => false,
                'Warning Ratio'                => false,
                'Readability Ratio'            => false,
                'Security Ratio'               => false,
                'Number of unresolved threads' => false,
            ],
            'getConstraints' => [
                'Number of Threads'            => null,
                'Thread / Files Ratio'         => null,
                'Lines / Files Ratio'          => null,
                'Files Changed'                => null,
                'Lines Added'                  => null,
                'Lines Removed'                => null,
                'Replies per Thread Ratio'     => null,
                'Alert Ratio'                  => null,
                'Warning Ratio'                => null,
                'Readability Ratio'            => null,
                'Security Ratio'               => null,
                'Number of unresolved threads' => null,
            ],
        ];

        yield 'alert & warning ratio disabled' => [
            'config'      => [
                'Alert Ratio'   => [
                    'enabled' => false,
                ],
                'Warning Ratio' => [
                    'enabled' => false,
                ],
            ],
            'hasConstraints' => [
                'Number of Threads'            => false,
                'Thread / Files Ratio'         => false,
                'Lines / Files Ratio'          => false,
                'Files Changed'                => false,
                'Lines Added'                  => false,
                'Lines Removed'                => false,
                'Replies per Thread Ratio'     => false,
                'Alert Ratio'                  => false,
                'Warning Ratio'                => false,
                'Readability Ratio'            => false,
                'Security Ratio'               => false,
                'Number of unresolved threads' => false,
            ],
            'getConstraints' => [
                'Number of Threads'            => null,
                'Thread / Files Ratio'         => null,
                'Lines / Files Ratio'          => null,
                'Files Changed'                => null,
                'Lines Added'                  => null,
                'Lines Removed'                => null,
                'Replies per Thread Ratio'     => null,
                'Alert Ratio'                  => null,
                'Warning Ratio'                => null,
                'Readability Ratio'            => null,
                'Security Ratio'               => null,
                'Number of unresolved threads' => null,
            ],
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
            'hasConstraints' => [
                'Number of Threads'            => false,
                'Thread / Files Ratio'         => false,
                'Lines / Files Ratio'          => false,
                'Files Changed'                => false,
                'Lines Added'                  => true,
                'Lines Removed'                => true,
                'Replies per Thread Ratio'     => false,
                'Alert Ratio'                  => false,
                'Warning Ratio'                => false,
                'Readability Ratio'            => false,
                'Security Ratio'               => false,
                'Number of unresolved threads' => false,
            ],
            'getConstraints' => [
                'Number of Threads'            => null,
                'Thread / Files Ratio'         => null,
                'Lines / Files Ratio'          => null,
                'Files Changed'                => null,
                'Lines Added'                  => 'value < 1000',
                'Lines Removed'                => 'value < 1000',
                'Replies per Thread Ratio'     => null,
                'Alert Ratio'                  => null,
                'Warning Ratio'                => null,
                'Readability Ratio'            => null,
                'Security Ratio'               => null,
                'Number of unresolved threads' => null,
            ],
        ];
    }

    /**
     * @dataProvider generateAlteredConfigs
     *
     * @covers ::isMetricDisabled()
     * @covers ::hasConstraint()
     * @covers ::getConstraint()
     *
     * @param array<string, array{enabled?: bool, constraint?: string}> $config
     * @param array<string, bool> $hasConstraints
     * @param array<string, string|null> $getConstraints
     */
    public function testConstraintIsSet(array $config, array $hasConstraints, array $getConstraints): void
    {
        $configItemMetrics = new ConfigItemMetrics($config);

        foreach ($hasConstraints as $hasConstraintName => $hasConstraint){
            $this->assertSame($hasConstraint, $configItemMetrics->hasConstraint($hasConstraintName));
        }

        foreach ($getConstraints as $getConstraintName => $getConstraint){
            $this->assertSame($getConstraint, $configItemMetrics->getConstraint($getConstraintName));
        }
    }
}
