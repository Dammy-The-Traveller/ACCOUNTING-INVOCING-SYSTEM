<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>System Requirements</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 2rem auto; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        .ok { color: green; }
        .fail { color: red; }
        a.button {
            display: inline-block;
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        a.button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Requirements Check</h1>
    <table>
        <tr><th>Check</th><th>Status</th></tr>
        <tr>
            <td>PHP ≥ 8.0</td>
            <td class="<?= $checks['php_version']['status'] ? 'ok' : 'fail' ?>">
                <?= htmlspecialchars($checks['php_version']['current']) ?>
            </td>
        </tr>
        <?php foreach ($checks['extensions'] as $ext => $ok): ?>
        <tr>
            <td>Extension: <?= htmlspecialchars($ext) ?></td>
            <td class="<?= $ok ? 'ok' : 'fail' ?>">
                <?= $ok ? 'Loaded' : 'Missing' ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php foreach ($checks['permissions'] as $path => $ok): ?>
        <tr>
            <td>Folder writable: <?= htmlspecialchars($path) ?></td>
            <td class="<?= $ok ? 'ok' : 'fail' ?>">
                <?= $ok ? 'Yes' : 'No' ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td>Migrations Directory</td>
            <td class="<?= $checks['migrations']['status'] ? 'ok' : 'fail' ?>">
                <?= htmlspecialchars($checks['migrations']['message']) ?>
            </td>
        </tr>
        <tr>
            <td>MySQL Server</td>
            <td class="<?= $checks['mysql_server']['status'] ? 'ok' : 'fail' ?>">
                <?= htmlspecialchars($checks['mysql_server']['message']) ?>
            </td>
        </tr>
    </table>
    <br><br>
    <?php 
    $allGood = $checks['php_version']['status'] 
        && $checks['extensions']['pdo'] 
        && $checks['extensions']['pdo_mysql'] 
        && $checks['extensions']['mbstring'] 
        && $checks['extensions']['json'] 
        && $checks['permissions']['storage']
        && $checks['permissions']['storage/logs']
        && $checks['permissions']['database']
        && $checks['permissions']['.env (create/write)']
        && $checks['mysql_server']['status'];
    ?>
    <?php if ($allGood): ?>
        <div class="next">
            <a href="/AIS/install-db" class="button">Next → Database Setup</a>
        </div>
    <?php else: ?>
        <p class="fail">Please fix the issues above before continuing.</p>
    <?php endif; ?>
</body>
</html>