<?php
class Cache {
    private $cacheDir;
    private $defaultExpiry = 3600; // 1 hour default cache expiry

    public function __construct($cacheDir = null) {
        $this->cacheDir = $cacheDir ?: __DIR__ . '/../../cache';
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function set($key, $data, $expiry = null) {
        $expiry = $expiry ?: $this->defaultExpiry;
        $cacheFile = $this->getCacheFilePath($key);
        
        $cacheData = [
            'data' => $data,
            'expiry' => time() + $expiry
        ];

        return file_put_contents($cacheFile, serialize($cacheData)) !== false;
    }

    public function get($key) {
        $cacheFile = $this->getCacheFilePath($key);

        if (!file_exists($cacheFile)) {
            return null;
        }

        $cacheData = unserialize(file_get_contents($cacheFile));

        if ($cacheData['expiry'] < time()) {
            unlink($cacheFile);
            return null;
        }

        return $cacheData['data'];
    }

    public function delete($key) {
        $cacheFile = $this->getCacheFilePath($key);
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }
        return true;
    }

    public function clear() {
        $files = glob($this->cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }

    private function getCacheFilePath($key) {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }
}
