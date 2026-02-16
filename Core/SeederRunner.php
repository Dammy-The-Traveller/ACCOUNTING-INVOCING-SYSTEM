<?php
namespace Core;

use PDO;

class SeederRunner
{
    private PDO $pdo;
    private string $dir;

    public function __construct(PDO $pdo, string $seedersDir)
    {
        $this->pdo = $pdo;
        $this->dir = rtrim($seedersDir, '/\\');
    }

    public function runAll(): array
    {
        $files = glob($this->dir . '/*.php') ?: [];
        sort($files);
        $results = [];

        foreach ($files as $file) {
            $seeder = require $file;
            if (!is_callable($seeder)) {
                $results[] = basename($file) . " ... SKIPPED (invalid seeder)";
                continue;
            }
            $seeder($this->pdo);
            $results[] = basename($file) . " ... OK";
        }
        return $results;
    }
}
