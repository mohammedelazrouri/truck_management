<?php
// No variables needed; this view uses client-side API calls (actions/api.php) to load data.
?>

<?php require __DIR__ . '/../../templates/header.php'; ?>

<div class="page-header">
    <h2>Gestion des Voyages</h2>
</div>

<div class="action-buttons">
    <a href="add_trip.php" class="btn btn-primary">➕ Créer un nouveau voyage</a>
    <a href="end_trip.php" class="btn btn-success">✅ End Trip (Scanner QR)</a>
    <a href="in_progress_trips.php" class="btn btn-info">📋 In Progress</a>
    <a href="add_driver.php" class="btn btn-warning">👤 Add Driver</a>
    <a href="manage_points_goods.php" class="btn btn-secondary">📍 Points</a>
</div>

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
        <button type="button" onclick="refreshDashboard()" class="btn btn-primary">🔎 Filtrer</button>
    </div>
</div>

<div class="table-wrapper">
    <table class="table">
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
                <th>Modified</th>
            </tr>
        </thead>
        <tbody id="real-time-dashboard-body"></tbody>
    </table>
</div>

<script>
// ====================== Format Date ======================
function formatDate(d){
    if(!d || d === "0000-00-00 00:00:00") return "N/A";
    return new Date(d).toLocaleString();
}

// ====================== تعديل BON ======================
function updateBon(id) {

    const bonPour = prompt("Bon Pour:");
    if (bonPour === null) return;

    const bonLivraison = prompt("Bon Livraison:");
    if (bonLivraison === null) return;

    fetch('actions/api.php?action=update_bon', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
            id: id,
            bon_pour: bonPour,
            bon_livraison: bonLivraison
        })
    })
    .then(res => res.json())
    .then(r => {
        if(r.success) refreshDashboard();
        else alert(r.message || "Erreur");
    });
}

// ====================== تحميل الرحلات ======================
function refreshDashboard(){

    const dateFrom = document.getElementById('filter-date-from').value;
    const dateTo   = document.getElementById('filter-date-to').value;
    const origin   = document.getElementById('filter-origin').value;
    const destination = document.getElementById('filter-destination').value;

    let url = `actions/api.php?action=get_trips`;

    if(dateFrom && dateTo){
        url += `&date_from=${dateFrom}&date_to=${dateTo}`;
    }
    if(origin) url += `&origin=${origin}`;
    if(destination) url += `&destination=${destination}`;

    fetch(url)
    .then(res => res.json())
    .then(data => {

        if(!data.success){
            alert(data.message || "Erreur serveur");
            return;
        }

        const trips = data.data || [];
        const tbody = document.getElementById("real-time-dashboard-body");
        tbody.innerHTML = '';

        if(trips.length === 0){
            tbody.innerHTML = `<tr><td colspan="10">Aucun voyage trouvé</td></tr>`;
            return;
        }

        trips.forEach(trip => {
            const tr = document.createElement('tr');
            if(trip.status) {
                tr.classList.add('table-row-' + trip.status.toLowerCase());
            }

            let statusClass = '';
            if (trip.status) {
                switch (trip.status.toLowerCase()) {
                    case 'completed':
                        statusClass = 'badge-success';
                        break;
                    case 'in_progress':
                        statusClass = 'badge-warning';
                        break;
                    case 'cancelled':
                        statusClass = 'badge-danger';
                        break;
                }
            }

            tr.innerHTML = `
                <td>${trip.id}</td>
                <td><span class="badge ${statusClass}">${trip.status || 'N/A'}</span></td>
                <td>${trip.truck_plaque || 'N/A'}</td>
                <td>${trip.driver_name || 'N/A'}</td>
                <td>${trip.origin_name || 'N/A'}</td>
                <td>${trip.destination_name || 'N/A'}</td>
                <td>${formatDate(trip.start_time)}</td>
                <td>${formatDate(trip.end_time)}</td>
                <td>
                    <a href="view_trip.php?id=${trip.id}">Détails</a><br>
                    <button class="btn btn-sm btn-outline" onclick="updateBon(${trip.id})">✏</button>
                </td>
                <td>${formatDate(trip.updated_at)}</td>
            `;

            tbody.appendChild(tr);
        });

    });
}

// ====================== تحميل النقاط ======================
function loadPoints(){
    fetch('actions/api.php?action=get_points')
    .then(res => res.json())
    .then(data => {
        if(!data.success) return;

        const origin = document.getElementById('filter-origin');
        const dest   = document.getElementById('filter-destination');

        origin.innerHTML = '<option value="">Toutes</option>';
        dest.innerHTML = '<option value="">Toutes</option>';

        data.data.forEach(p => {
            origin.add(new Option(p.nom, p.id));
            dest.add(new Option(p.nom, p.id));
        });
    });
}

document.addEventListener("DOMContentLoaded", function(){
    loadPoints();
    refreshDashboard();
});

// تحديث تلقائي كل 30 ثانية
setInterval(refreshDashboard, 30000);
</script>

<?php require __DIR__ . '/../../templates/footer.php'; ?>
