<?php include __DIR__ . '/../partials/head.php'; ?>

<body data-open="click" data-menu="vertical-menu" data-col="2-columns"
      class="vertical-layout vertical-menu 2-columns  fixed-navbar"><span id="hdata"
                                                                          data-df="dd-mm-yyyy"
                                                                          data-curr="$"></span>
<div class="container mt-5">
    <h2>Database Setup</h2>
    <p>Please provide your database credentials.</p>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ Database connection is successful and all tables has been created with data for demo purpose, you can empty table later to insert your own data!</div>
        <a href="/install/admin" class="btn btn-primary">Next → Admin Setup</a>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">❌ Connection failed: <?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form method="POST" action="/AIS/install-save-database" class="mt-4">
        <div class="mb-3">
            <label>Host</label>
            <input type="text" name="db_host" class="form-control" value="<?= $existing['database']['host'] ?? 'localhost' ?>">
            
        </div>
        <div class="mb-3">
            <label>Database Name</label>
            <input type="text" name="db_name" class="form-control" value="<?= $existing['database']['dbname'] ?? '' ?>">
            <p>Please </p>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="db_user" class="form-control" value="<?= $existing['database']['username'] ?? '' ?>">
        </div>
         <div class="mb-3">
            <label>Password</label>
            <input type="password" name="db_pass" class="form-control" value="<?= $existing['database']['password'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label>Driver</label>
            <input type="text" name="db_driver" class="form-control" value="<?= $existing['database']['driver'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label>Port</label>
            <input type="text" name="db_port" class="form-control" value="<?= $existing['database']['port'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label>Charset</label>
            <input type="text" name="db_charset" class="form-control" value="<?= $existing['database']['charset'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label>collation</label>
            <input type="text" name="db_collation" class="form-control" value="<?= $existing['database']['collation'] ?? '' ?>">
        </div>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
</body>
</html>

