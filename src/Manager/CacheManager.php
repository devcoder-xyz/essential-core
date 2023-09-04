<?php

namespace Essential\Core\Manager;

final class CacheManager
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function get(string $key)
    {
        $cacheFile = $this->getCacheFilePath($key);

        if (file_exists($cacheFile)) {
            $cachedData = unserialize(file_get_contents($cacheFile));
            if ($cachedData !== false) {
                if ($cachedData['ttl'] === 0 || $cachedData['ttl'] > time()) {
                    return $cachedData['data'];
                }
                $this->delete($key);
            }
        }

        return null;
    }

    public function set(string $key, $data, int $ttl = 0): void
    {
        $cacheFile = $this->getCacheFilePath($key);

        file_put_contents($cacheFile, serialize([
            'data' => $data,
            'ttl' => $ttl > 0 ? time() + $ttl : 0,
        ]));
    }

    public function delete(string $key): void
    {
        $cacheFile = $this->getCacheFilePath($key);

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    private function getCacheFilePath(string $key): string
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . md5($key) . '.cache';
    }
}
