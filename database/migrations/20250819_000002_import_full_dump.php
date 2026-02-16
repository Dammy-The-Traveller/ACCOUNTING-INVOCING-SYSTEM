<?php
return [
    'up' => function (PDO $pdo) {
        $dump = __DIR__ . '/../dumps/schema.sql';
        if (!file_exists($dump)) {
            throw new \RuntimeException("Dump file not found: " . $dump);
        }
        $sql = file_get_contents($dump);
        // Warning: assumes the dump is compatible with your current DB + user
           $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
      $sql = file_get_contents($dump);

      $pdo->exec($sql);                  // imports schema + data
      $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    },
    'down' => function (PDO $pdo) {
        // (Optional) You could drop the tables imported by the dump here.
        // For safety we’ll leave this blank.
    }
];
