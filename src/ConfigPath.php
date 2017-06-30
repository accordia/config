<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

final class ConfigPath implements ConfigPathInterface
{
    private const PATH_SEP = ".";

    private $scope;

    private $parts;

    public static function fromString(string $path): ConfigPathInterface
    {
        $separatorPosition = strpos($path, self::PATH_SEP);
        if ($separatorPosition === 0) {
            throw new \Exception("Initializing malformed ConfigPath: Path may not start with separator.");
        }
        $pathParts = explode(self::PATH_SEP, $path);
        return new static(array_shift($pathParts), $pathParts);
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getParts(): array
    {
        return $this->parts;
    }

    public function hasParts(): bool
    {
        return !empty($this->parts);
    }

    public function getLength(): int
    {
        return count($this->parts);
    }

    public function __toString(): string
    {
        $pathParts = $this->parts;
        array_unshift($pathParts, $this->scope);
        return join(self::PATH_SEP, $pathParts);
    }

    private function __construct(string $scope, array $parts)
    {
        if (empty($scope)) {
            throw new \Exception("Trying to create ConfigPath from empty scope.");
        }
        $this->scope = $scope;
        $this->parts = $parts;
    }
}
