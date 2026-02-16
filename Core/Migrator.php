<?php
namespace Core;

use PDO;
use Exception;

class Migrator
{
    private PDO $pdo;
    private string $dir;

    public function __construct(PDO $pdo, string $migrationsDir)
    {
        $this->pdo = $pdo;
        $this->dir = rtrim($migrationsDir, '/\\');
    }

    public function ensureMigrationsTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                ran_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_migration (migration)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function getRan(): array
    {
        $ran = [];
        $stmt = $this->pdo->query("SELECT migration FROM migrations");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ran[$row['migration']] = true;
        }
        return $ran;
    }

    private function nextBatch(): int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) AS b FROM migrations");
        $max = (int)($stmt->fetchColumn() ?: 0);
        return $max + 1;
    }

    public function runPending(): array
    {
        $this->ensureMigrationsTable();

        $files = glob($this->dir . '/*.php') ?: [];
        sort($files);

        $ran = $this->getRan();
        $batch = $this->nextBatch();
        $results = [];

        foreach ($files as $file) {
            $name = basename($file);
            if (isset($ran[$name])) {
                $results[] = "$name ... SKIPPED (already ran)";
                continue;
            }

            $migration = require $file;

            // migration format must be ['up' => callable(PDO), 'down' => callable(PDO)]
            if (!is_array($migration) || !isset($migration['up']) || !is_callable($migration['up'])) {
                throw new Exception("Invalid migration format in $name");
            }

            $this->pdo->beginTransaction();
            try {
                $migration['up']($this->pdo);
                $stmt = $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$name, $batch]);
                $this->pdo->commit();
                $results[] = "$name ... OK";
            } catch (Exception $e) {
                $this->pdo->rollBack();
                throw new Exception("Migration $name failed: " . $e->getMessage());
            }
        }
        return $results;
    }

    public function rollbackLastBatch(): array
    {
        $this->ensureMigrationsTable();

        // find last batch
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM migrations");
        $lastBatch = (int)$stmt->fetchColumn();
        if ($lastBatch === 0) return ["Nothing to rollback"];

        // get migrations in reverse order
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC");
        $stmt->execute([$lastBatch]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rows as $row) {
            $name = $row['migration'];
            $file = $this->dir . DIRECTORY_SEPARATOR . $name;
            if (!file_exists($file)) {
                $results[] = "$name ... MISSING FILE (cannot rollback)";
                continue;
            }
            $migration = require $file;
            if (!isset($migration['down']) || !is_callable($migration['down'])) {
                $results[] = "$name ... NO DOWN() (cannot rollback)";
                continue;
            }

            $this->pdo->beginTransaction();
            try {
                $migration['down']($this->pdo);
                $del = $this->pdo->prepare("DELETE FROM migrations WHERE migration = ?");
                $del->execute([$name]);
                $this->pdo->commit();
                $results[] = "$name ... ROLLED BACK";
            } catch (Exception $e) {
                $this->pdo->rollBack();
                $results[] = "$name ... ROLLBACK FAILED: " . $e->getMessage();
            }
        }
        return $results;
    }
}
