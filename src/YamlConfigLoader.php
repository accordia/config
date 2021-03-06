<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Config;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

final class YamlConfigLoader implements ConfigLoaderInterface
{
    private Yaml $yamlParser;

    private Finder $finder;

    public function __construct(Yaml $yamlParser = null, Finder $finder = null)
    {
        $this->yamlParser = $yamlParser ?? new Yaml;
        $this->finder = $finder ?? new Finder;
    }

    public function load(array $locations, array $sources): array
    {
        return array_reduce(
            $locations,
            /** @param string|string[] $location */
            function (array $config, $location) use ($sources): array {
                return array_replace_recursive($config, $this->loadSources($location, $sources));
            },
            []
        );
    }

    /** @param string|string[] $location */
    private function loadSources($location, array $sources): array
    {
        $location = array_filter((array)$location, 'is_dir');

        if (empty($location) || empty($sources)) {
            return [];
        }

        return array_reduce($sources, function (array $config, string $source) use ($location): array {
            foreach ($this->finder
                ->create()
                ->files()
                ->ignoreUnreadableDirs()
                ->in($location)
                ->name($source)
                ->sortByName() as $file) {
                if ($file->isReadable()) {
                    $config = array_replace_recursive($config, $this->yamlParser->parse($file->getContents()) ?? []);
                };
            }
            return $config;
        }, []);
    }
}
