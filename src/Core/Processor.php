<?php

declare(strict_types=1);

namespace Geekmusclay\Template\Core;

use RuntimeException;

class Processor
{
    private array $patterns;
    private string $root;
    private ?string $cache;

    public function __construct(string $root, array $patterns = [], ?string $cache = null)
    {
        $this->root = rtrim($root, '/');
        $this->patterns = $patterns;
        $this->cache = $cache ? rtrim($cache, '/') : null;
    }

    public function render(string $name): string
    {
        if ($this->cache && $this->isCached($name)) {
            return $this->getFromCache($name);
        }

        $content = $this->processFile($name);

        if ($this->cache) {
            $this->cacheResult($name, $content);
        }

        return $content;
    }

    private function isCached(string $name): bool
    {
        if (!$this->cache) {
            return false;
        }

        $cachePath = $this->getCachePath($name);
        return file_exists($cachePath);
    }

    private function getFromCache(string $name): string
    {
        $cachePath = $this->getCachePath($name);

        if (!file_exists($cachePath)) {
            throw new RuntimeException("Template cache file not found: {$cachePath}");
        }

        $content = file_get_contents($cachePath);
        if (false === $content) {
            throw new RuntimeException("Failed to read template cache file: {$cachePath}");
        }

        return $content;
    }

    private function processFile(string $name): string
    {
        $filePath = $this->root . '/' . $name;

        if (!file_exists($filePath)) {
            throw new RuntimeException("Template file not found: {$filePath}");
        }

        $content = file_get_contents($filePath);

        if (false === $content) {
            throw new RuntimeException("Failed to read template file: {$filePath}");
        }

        return $this->applyPatterns($content);
    }

    private function applyPatterns(string $content): string
    {
        if (empty($this->patterns)) {
            return $content;
        }

        return preg_replace(array_keys($this->patterns), array_values($this->patterns), $content);
    }

    private function cacheResult(string $name, string $content): void
    {
        if (!$this->cache) {
            return;
        }

        $cachePath = $this->getCachePath($name);
        $cacheDir = dirname($cachePath);

        if (!is_dir($cacheDir) && !mkdir($cacheDir, 0777, true)) {
            throw new RuntimeException("Failed to create cache directory: {$cacheDir}");
        }

        if (false === file_put_contents($cachePath, $content)) {
            throw new RuntimeException("Failed to write to cache file: {$cachePath}");
        }
    }

    private function getCachePath(string $name): string
    {
        return $this->cache . '/' . base64_encode($name);
    }
}