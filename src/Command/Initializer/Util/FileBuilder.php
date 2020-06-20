<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Util;

use function file_get_contents;
use function file_put_contents;
use function is_file;
use function str_replace;

class FileBuilder
{
    /**
     * @var string|false
     */
    private $template;

    /**
     * @var array<string|null>
     */
    private $replaceTokens = [];

    /**
     * FileBuilder constructor.
     *
     * @param string $templatePath Template file path.
     */
    public function __construct(string $templatePath)
    {
        if (!is_file($templatePath)) {
            throw new \InvalidArgumentException("File `{$templatePath}` not found!");
        }

        $this->template = file_get_contents($templatePath);
    }

    /**
     * Set value of replace token.
     *
     * @param string      $token Replace token (e.g "{replaceMe}").
     * @param string|null $value Value to replace.
     *
     * @return FileBuilder
     */
    public function setReplaceToken(string $token, ?string $value): self
    {
        $this->replaceTokens[$token] = $value;

        return $this;
    }

    /**
     * Replace tokens in template and write it to destination file.
     *
     * @param string $destinationFile Destination file to write to.
     *
     * @return int The Exit Code.
     */
    public function buildFile(string $destinationFile): int
    {
        // Replace tokens.
        foreach ($this->replaceTokens as $token => $replace) {
            $this->template = str_replace($token, $replace ?? '', (string)$this->template);
        }

        if (file_put_contents($destinationFile, $this->template)) {
            return 0;
        }

        return -1;
    }
}
