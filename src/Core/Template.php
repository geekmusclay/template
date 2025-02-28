<?php

declare(strict_types=1);

namespace Geekmusclay\Template\Core;

/**
 * Template rendering class.
 * @deprecated
 */
class Template
{
    /** @var array $patterns The templating patterns */
    private array $patterns;

    /** @var string $root The root folder path */
    private string $root;

    /** @var string|null $cache The cache folder path */
    private ?string $cache;

    /**
     * Constructor.
     * 
     * @param array $patterns The templating patterns
     */
	public function __construct(
        string  $root,
        array   $patterns = [],
        ?string $cache    = null
    ) {
        $this->root     = $root;
		$this->patterns = $patterns;
        $this->cache    = $cache;
	}

    public function processOrGetFromCache(string $name): string
    {
        $key = base64_encode($name);
        $path = $this->cache . '/' . $key;

        if (true === file_exists($path)) {
            return file_get_contents($path);
        }

        $content = $this->processFile($name);
        $this->cacheResult($content, $name);

        return $content;
    }

    public function getFromCache(string $name): string
    {
        if (null === $this->cache) {
            throw new \Exception('No cache folder defined.');
        }

        $key = base64_encode($name);
        $path = $this->cache . '/' . $key;

        if (false === file_exists($path)) {
            throw new \Exception('Template file not found.');
        }

        return file_get_contents($path);
    }

    public function processFile(string $name): string
    {
        $path = $this->root . '/' . $name;
        if (false === file_exists($path)) {
            throw new \Exception('Template file not found.');
        }

        $content = file_get_contents($path);
        if (false === $content) {
            throw new \Exception('Template file could not be read.');
        }

        return $this->process($content);
    }

	public function process(string $input): string
	{
        if (0 === count($this->patterns)) {
            throw new \Exception('No patterns defined.');
        }

		return preg_replace(array_keys($this->patterns), array_values($this->patterns), $input);
	}

    public function cacheResult(string $result, string $name): int|false
    {
        if (null === $this->cache) {
            throw new \Exception('No cache folder defined.');
        }

        $key = base64_encode($name);
        if (false === file_exists($this->cache)) {
            mkdir($this->cache, 0777, true);
        }

        $path = $this->cache . '/' . $key;

        return file_put_contents($path, $result);
    }
}
