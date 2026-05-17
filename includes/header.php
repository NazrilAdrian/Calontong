<?php
if (!isset($page_title)) {
    $page_title = "Warung Kelontong";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/index.php">Warung Kelontong</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/pages/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 col-lg-2 bg-light border-end min-vh-100 p-3">
                <h6 class="text-uppercase text-muted">Menu</h6>
                <ul class="nav nav-pills flex-column gap-1">
                    <li class="nav-item"><a class="nav-link" href="/pages/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/pages/users/index.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="/pages/categories/index.php">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="/pages/products/index.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="/pages/customers/index.php">Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="/pages/transactions/index.php">Transactions</a></li>
                    <li class="nav-item"><a class="nav-link" href="/pages/reports/harian.php">Reports</a></li>
                </ul>
            </aside>

            <main class="col-md-9 col-lg-10 p-4">
