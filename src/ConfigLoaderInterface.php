<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Config;

interface ConfigLoaderInterface
{
    public function load(array $locations, array $sources): array;
}
