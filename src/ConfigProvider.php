<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

final class ConfigProvider implements ConfigProviderInterface
{
    private const INTERPOLATION_PATTERN = '/(\$\{(.*?)\})/';

    private $config;

    private $params;

    public function __construct(ConfigProviderParamsInterface $params)
    {
        $this->params = $params;
        $this->config = [];
    }

    public function get(string $path, $default = null)
    {
        $configPath = ConfigPath::fromString($path);
        $scope = $configPath->getScope();
        if (!isset($this->config[$scope]) && $this->params->hasScope($scope)) {
            $this->config[$scope] = $this->loadScope($scope);
        }
        return $this->resolvePath($configPath) ?? $default;
    }

    public function has(string $path): bool
    {
        return $this->get($path) !== null;
    }

    private function loadScope(string $scope)
    {
        $this->config[$scope] = $this->params->getLoader($scope)->load(
            $this->params->getLocations($scope),
            $this->params->getSources($scope)
        );
        return $this->interpolateConfigValues($this->config[$scope]);
    }

    private function resolvePath(ConfigPathInterface $path)
    {
        $scope = $path->getScope();
        if (!isset($this->config[$scope])) {
            return null;
        }
        $value = &$this->config[$scope];
        $pathParts = $path->getParts();
        while (!empty($pathParts)) {
            $pathPart = array_shift($pathParts);
            if (!isset($value[$pathPart])) {
                return null;
            }
            if (!is_array($value)) {
                throw new \Exception("Trying to traverse non array-value with path: '".$path->getKey()."'");
            }
            $value = &$value[$pathPart];
        }
        return $value;
    }

    private function interpolateConfigValues(array $config): array
    {
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $config[$key] = $this->interpolateConfigValues($value);
            } elseif (is_string($value) && preg_match_all(self::INTERPOLATION_PATTERN, $value, $matches)) {
                $replacements = [];
                foreach ($matches[2] as $configKey) {
                    $replacements[] = $this->get($configKey);
                }
                $config[$key] = str_replace($matches[0], $replacements, $value);
            }
        }
        return $config;
    }
}
