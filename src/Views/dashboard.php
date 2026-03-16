<?php
// assumes $trips, $statusCount, $drivers variables may be set by controller
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/admin/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="/assets/js/dashboard.js" defer></script>
</head>
<body>
<nav class="admin-nav">
    <div class="nav-container">
        <a href="/dashboard" class="nav-logo">🚚 TMS</a>
        <ul class="nav-menu">
            <li><a href="/dashboard" class="active">📊 Dashboard</a></li>
            <li><a href="/trips">📍 Trajets</a></li>
            <li><a href="/trucks">🚚 Camions</a></li>
            <li><a href="/logout">🚪 Déconnexion</a></li>
        </ul>
    </div>
</nav>
<div class="container">
    <header class="page-header">
        <div class="header-content">
            <h1>Gestion des Voyages</h1>
            <p class="header-subtitle">Suivi des trajets et des conducteurs en temps réel</p>
        </div>
        <div class="page-header-actions">
            <a href="/add_trip" class="btn btn-primary">➕ Créer un voyage</a>
            <a href="/end_trip" class="btn btn-info">✅ Fin QR</a>
            <a href="/in_progress_trips" class="btn btn-warning">📋 En cours</a>
            <a href="/add_driver" class="btn btn-success">👤 Ajouter</a>
        </div>
    </header>

    <section class="filters-section">
        <div class="filters-grid">
            <div class="form-group">
                <label>Date (De)</label>
                <input type="date" id="filter-date-from" class="form-control">
            </div>
            <div class="form-group">
                <label>Date (À)</label>
                <input type="date" id="filter-date-to" class="form-control">
            </div>
            <div class="form-group">
                <label>Origine</label>
                <select id="filter-origin" class="form-control"><option value="">Toutes</option></select>
            </div>
            <div class="form-group">
                <label>Destination</label>
                <select id="filter-destination" class="form-control"><option value="">Toutes</option></select>
            </div>
            <div class="filters-actions">
                <button class="btn btn-primary" onclick="refreshDashboard()">🔎 Filtrer</button>
            </div>
        </div>
    </section>

    <section class="kpi-container">
        <div class="kpi-card">
            <div class="kpi-icon">📈</div>
            <div class="kpi-content">
                <span class="kpi-label">Total des trajets</span>
                <span id="stat-total" class="kpi-value">0</span>
            </div>
        </div>
    </section>

    <section class="charts-section">
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>États des trajets</h3>
                </div>
                <div class="chart-wrapper"><canvas id="statusChart"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Top 5 Conducteurs</h3>
                </div>
                <div class="chart-wrapper"><canvas id="driverChart"></canvas></div>
            </div>
        </div>
    </section>

    <section class="table-section">
        <div class="table-wrapper">
            <table class="data-table" id="trips-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Camion</th>
                        <th>Conducteur</th>
                        <th>Origine</th>
                        <th>Destination</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>