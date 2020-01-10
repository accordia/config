<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Test\Config;

use Assert\AssertionFailedException;
use Daikon\Config\ConfigPath;
use PHPUnit\Framework\TestCase;

final class ConfigPathTest extends TestCase
{
    public function testGetScope()
    {
        $configPath = ConfigPath::fromString('settings.core.app_version');
        $this->assertEquals('settings', $configPath->getScope());
        $configPath = ConfigPath::fromString('settings');
        $this->assertEquals('settings', $configPath->getScope());
    }

    public function testGetParts()
    {
        $configPath = ConfigPath::fromString('settings.core.app_version');
        $this->assertEquals([ 'core', 'app_version' ], $configPath->getParts());
        $configPath = ConfigPath::fromString('settings');
        $this->assertEquals([], $configPath->getParts());
    }

    public function testGetLength()
    {
        $configPath = ConfigPath::fromString('settings.core.app_version');
        $this->assertEquals(2, $configPath->getLength());
    }

    public function testHasParts()
    {
        $configPath = ConfigPath::fromString('settings.core.app_version');
        $this->assertTrue($configPath->hasParts());
        $configPath = ConfigPath::fromString('settings');
        $this->assertFalse($configPath->hasParts());
    }

    public function testToString()
    {
        $configPath = ConfigPath::fromString('settings.core.app_version');
        $this->assertEquals('settings.core.app_version', (string)$configPath);
    }

    public function testWithEmptyPath()
    {
        $this->expectException(AssertionFailedException::class);
        ConfigPath::fromString('');
    } // @codeCoverageIgnore

    public function testInvalidPathWithLeadingSeparator()
    {
        $this->expectException(AssertionFailedException::class);
        ConfigPath::fromString('.settings.core.app_version');
    } // @codeCoverageIgnore
}
