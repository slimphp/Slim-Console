<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Config;

use ArrayAccess;

use function array_key_exists;
use function array_merge;
use function ctype_lower;
use function is_null;
use function preg_replace;
use function strtoupper;

class Config implements ArrayAccess
{
    private const ENV_PREFIX = 'SLIM_CONSOLE_';

    /**
     * @var array<mixed>
     */
    private $default = [
        'bootstrapDir'  => DIRECTORY_SEPARATOR . 'app',
        'commandsDir'   => DIRECTORY_SEPARATOR . 'Application/Commands',
        'indexDir'  => DIRECTORY_SEPARATOR . 'public',
        'indexFile' => DIRECTORY_SEPARATOR . 'index.php',
        'sourceDir' => DIRECTORY_SEPARATOR . 'src',
    ];

    /**
     * @var array<mixed>
     */
    private $params = [];

    /**
     * Config constructor.
     * @param array<mixed> $params
     * @param string $configDir
     */
    public function __construct(array $params = [], string $configDir = '')
    {
        if ($configDir !== '') {
            $this->set('rootDir', $configDir);
        }
        $this->setAll($params);
    }

    /**
     * @return array<mixed>
     */
    public function all(): array
    {
        return $this->params;
    }

    /**
     * @param array<mixed> $params
     * @return void
     */
    public function setAll(array $params): void
    {
        $params = array_merge($this->default, $params);

        foreach ($params as $key => $value) {
            //Env variables take precedence
            $this->params[$key] = $this->getEnvironmentVariableValue((string) $key) ?: $this->get('rootDir') . $value;
        }
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $this->params[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->params[$key]);
    }

    /**
     * @param string $key
     */
    public function delete(string $key): void
    {
        unset($this->params[$key]);
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * @param mixed $key
     * @return mixed|null
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * @param mixed $key
     */
    public function offsetUnset($key): void
    {
        $this->delete($key);
    }

    /**
     * @param string $key
     * @return string|null
     */
    private function getEnvironmentVariableValue(string $key): ?string
    {
        //Return nothing for unknown keys
        if (array_key_exists($key, $this->default) === false) {
            return null;
        }

        if (ctype_lower($key) === true) {
            return strtoupper($key);
        }

        //Convert CamelCase to Snake_Case
        $key = preg_replace('/(.)(?=[A-Z])/u', '$1' . '_', $key);
        if (is_null($key)) {
            return null;
        }

        $key = self::ENV_PREFIX . strtoupper($key);
        $value = getenv($key);

        return $value ?: null;
    }
}
