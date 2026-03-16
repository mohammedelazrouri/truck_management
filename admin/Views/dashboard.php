<?php
// Dashboard view (loaded by Admin\Controllers\DashboardController)
?>

<?php include __DIR__ . '/../../templates/header.php'; ?>

<style>
    .status-badge{display:inline-block;padding:4px 8px;border-radius:6px;font-weight:600}
    .status-in_progress{background:#f59e0b;color:#000}
    .status-completed{background:#3b82f6;color:#fff}
    .status-cancelled{background:#ef4444;color:#fff}
</style>

<h2>Gestion des Voyages</h2>

<!-- Action Buttons -->
<div class="action-buttons">
    <a href="add_trip.php" class="btn-add">➕ Créer un nouveau voyage</a>
    <a href="end_trip.php" class="btn-end">✅ End Trip (Scanner QR)</a>
    <a href="in_progress_trips.php" class="btn-progress">📋 In Progress</a>
    <a href="add_driver.php" class="btn-driver">👤 Add Driver</a>
    <a href="manage_points_goods.php" class="btn-points">📍 Points</a>
    <a href="register_admin.php" class="btn-admin">👨‍💼 Register Admin</a>
    <a href="cv_pdf.php" class="btn-cv">📝 Télécharger CV PDF</a>
</div>

<!-- Filters -->
<div class="trip-filters">
    <div class="filter-group">
        <label for="filter-date-from">Date (De)</label>
        <input type="date" id="filter-date-from">
    </div>

    <div class="filter-group">
        <label for="filter-date-to">Date (À)</label>
        <input type="date" id="filter-date-to">
    </div>

    <div class="filter-group">
        <label for="filter-origin">Origine</label>
        <select id="filter-origin">
            <option value="">Toutes</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="filter-destination">Destination</label>
        <select id="filter-destination">
            <option value="">Toutes</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="filter-status">Status</label>
        <select id="filter-status">
            <option value="">Tous</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <div class="filter-group btn-group">
        <button type="button" onclick="refreshDashboard()">🔎 Filtrer</button>
        <button type="button" onclick="showTodayTrips()">📅 Aujourd'hui</button>
        <button type="button" onclick="resetFilters()">♻️ Réinitialiser</button>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-container">
    <div class="kpi-card">
        <h3>Total des trajets</h3>
        <p id="stat-total">0</p>
    </div>
</div>

<!-- Charts -->
<div class="charts-row">
    <div class="chart-box">
        <h3>📊 États des trajets</h3>
        <canvas id="statusChart"></canvas>
    </div>

    <div class="chart-box">
        <h3>🏆 Top 5 Conducteurs</h3>
        <canvas id="driverChart"></canvas>
    </div>
</div>

<!-- Real-Time Dashboard Table -->
<div id="real-time-dashboard">
    <table>
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
                <th>Supprimer</th>
                <th>Actions</th>
                <th>Modified</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let statusChart = null;
let driverChart = null;

/* ================= LOAD POINTS ================= */
function loadFilters() {
    fetch('actions/api.php?action=get_points')
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            const origin = document.getElementById("filter-origin");
            const destination = document.getElementById("filter-destination");

            if (!origin || !destination) return;

            origin.innerHTML = '<option value="">Toutes</option>';
            destination.innerHTML = '<option value="">Toutes</option>';

            data.data.forEach(point => {
                origin.innerHTML += `<option value="${point.id}">${point.nom}</option>`;
                destination.innerHTML += `<option value="${point.id}">${point.nom}</option>`;
            });
        })
        .catch(err => console.error("Load points error:", err));
}

/* ================= FORMAT DATE ================= */
function formatDate(dateString) {
    if (!dateString || dateString === "0000-00-00 00:00:00") return "N/A";
    const d = new Date(dateString);
    return isNaN(d) ? "N/A" : d.toLocaleString();
}

/* ================= REFRESH ================= */
function refreshDashboard() {

    const dateFrom = document.getElementById('filter-date-from')?.value;
    const dateTo = document.getElementById('filter-date-to')?.value;
    const origin = document.getElementById('filter-origin')?.value;
    const destination = document.getElementById('filter-destination')?.value;
    const status   = document.getElementById('filter-status')?.value;

    let url = 'actions/api.php?action=get_trips';

    if (dateFrom) url += `&date_from=${encodeURIComponent(dateFrom)}`;
    if (dateTo) url += `&date_to=${encodeURIComponent(dateTo)}`;
    if (origin) url += `&origin=${origin}`;
    if (destination) url += `&destination=${destination}`;
    if (status) url += `&status=${status}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {

            if (!data.success) {
                console.error("API error");
                return;
            }

            const trips = data.data || [];

            updateKPIs(trips);
            updateTripTable(trips);
        })
        .catch(err => console.error("Fetch error:", err));
}

/* ================= SHOW TODAY ================ */
function showTodayTrips() {
    const today = new Date().toISOString().split('T')[0];
    const fromElem = document.getElementById('filter-date-from');
    const toElem   = document.getElementById('filter-date-to');
    if (fromElem) fromElem.value = today;
    if (toElem) toElem.value = today;
    // keep status in place
    refreshDashboard();
}

function resetFilters() {
    const elems = ['filter-date-from','filter-date-to','filter-origin','filter-destination','filter-status'];
    elems.forEach(id=>{
        const el = document.getElementById(id);
        if (!el) return;
        if (el.tagName.toLowerCase() === 'select') el.selectedIndex = 0;
        else if (el.type === 'date') el.value = '';
    });
    refreshDashboard();
}

/* ================= UPDATE TABLE ================= */
function updateTripTable(trips) {

    const tbody = document.querySelector("#real-time-dashboard tbody");
    if (!tbody) return;

    tbody.innerHTML = '';

    trips.forEach(trip => {

        const tr = document.createElement("tr");

        const statusText = trip.status || 'N/A';
        const statusClass = statusText.toLowerCase().replace(/\s+/g, '_');
        tr.innerHTML = `
            <td>${trip.id}</td>
            <td><span class="status-badge status-${statusClass}">${statusText}</span></td>
            <td>${trip.truck_plaque || 'N/A'}</td>
            <td>${trip.driver_name || 'N/A'}</td>
            <td>${trip.origin_name || 'N/A'}</td>
            <td>${trip.destination_name || 'N/A'}</td>
            <td>${formatDate(trip.start_time)}</td>
            <td>${formatDate(trip.end_time)}</td>
            <td>
                <button onclick="deleteTrip(${trip.id})">❌</button>
            </td>
            <td>
                <a href="view_trip.php?id=${trip.id}">Détails</a><br>
                <button onclick="updateBon(${trip.id})">✏ Modifier</button>
            </td>
            <td>${formatDate(trip.updated_at)}</td>
        `;

        tbody.appendChild(tr);
    });
}

// initial load
window.addEventListener('DOMContentLoaded', () => {
    loadFilters();
    refreshDashboard();
});
</script>
