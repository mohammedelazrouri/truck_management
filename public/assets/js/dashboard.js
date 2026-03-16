// moved from old inline script
let statusChart = null;
let driverChart = null;

function loadFilters() {
    fetch('/actions/api.php?action=get_points')
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;
            const origin = document.getElementById("filter-origin");
            const destination = document.getElementById("filter-destination");
            origin.innerHTML = '<option value="">Toutes</option>';
            destination.innerHTML = '<option value="">Toutes</option>';
            data.data.forEach(point => {
                origin.innerHTML += `<option value="${point.id}">${point.nom}</option>`;
                destination.innerHTML += `<option value="${point.id}">${point.nom}</option>`;
            });
        })
        .catch(err => console.error("Load points error:", err));
}

function formatDate(dateString) {
    if (!dateString || dateString === "0000-00-00 00:00:00") return "N/A";
    const d = new Date(dateString);
    return isNaN(d) ? "N/A" : d.toLocaleString();
}

function refreshDashboard() {
    const dateFrom = document.getElementById('filter-date-from')?.value;
    const dateTo = document.getElementById('filter-date-to')?.value;
    const origin = document.getElementById('filter-origin')?.value;
    const destination = document.getElementById('filter-destination')?.value;

    let url = '/actions/api.php?action=get_trips';
    if (dateFrom) url += `&date_from=${encodeURIComponent(dateFrom)}`;
    if (dateTo) url += `&date_to=${encodeURIComponent(dateTo)}`;
    if (origin) url += `&origin=${origin}`;
    if (destination) url += `&destination=${destination}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (!data.success) return console.error("API error");
            const trips = data.data || [];
            updateKPIs(trips);
            updateTripTable(trips);
        })
        .catch(err => console.error("Fetch error:", err));
}

function updateTripTable(trips) {
    const tbody = document.querySelector("#trips-table tbody");
    tbody.innerHTML = '';
    trips.forEach(trip => {
        const tr = document.createElement('tr');
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
                <a href="/view_trip?id=${trip.id}" class="btn btn-sm btn-outline">Détails</a>
                <button class="btn btn-sm btn-secondary" onclick="deleteTrip(${trip.id})">✏️</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function updateKPIs(trips) {
    let totalDuration = 0;
    let completedTrips = 0;
    const statusCount = {completed:0,in_progress:0,cancelled:0};
    const drivers = {};
    trips.forEach(t => {
        if (t.status) {
            const s = t.status.toLowerCase();
            if (statusCount[s] !== undefined) statusCount[s]++;
        }
        if (t.start_time && t.end_time) {
            const diff = (new Date(t.end_time) - new Date(t.start_time)) / 60000;
            if (diff>0) { totalDuration+=diff; completedTrips++; }
        }
        if (t.status === 'COMPLETED') {
            drivers[t.driver_name] = (drivers[t.driver_name]||0)+1;
        }
    });
    document.getElementById('stat-total').innerText = trips.length;
    updateCharts(statusCount, drivers);
}

function updateCharts(statusCount, drivers) {
    if (statusChart) statusChart.destroy();
    if (driverChart) driverChart.destroy();
    const statusCanvas = document.getElementById('statusChart');
    const driverCanvas = document.getElementById('driverChart');
    if (statusCanvas) {
        statusChart = new Chart(statusCanvas, {
            type:'pie',
            data:{ labels:['Completed','In Progress','Cancelled'], datasets:[{ data:[statusCount.completed,statusCount.in_progress,statusCount.cancelled], backgroundColor:['#2563eb','#d97706','#dc2626'] }] },
            options:{responsive:true,maintainAspectRatio:false}
        });
    }
    if (driverCanvas) {
        const sorted = Object.entries(drivers).sort((a,b)=>b[1]-a[1]).slice(0,5);
        driverChart = new Chart(driverCanvas, {
            type:'bar',
            data:{ labels: sorted.map(d=>d[0]), datasets:[{ data: sorted.map(d=>d[1]), backgroundColor:'#2563eb' }] },
            options:{indexAxis:'y',responsive:true,maintainAspectRatio:false}
        });
    }
}

function deleteTrip(id) {
    if (!confirm('Supprimer ce voyage ?')) return;
    fetch('/actions/api.php?action=delete_trip',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({id})
    })
    .then(r=>r.json())
    .then(res=>{ if(res.success) refreshDashboard(); else alert('Erreur'); })
    .catch(err=>console.error('Delete error',err));
}

document.addEventListener('DOMContentLoaded', function(){
    loadFilters();
    refreshDashboard();
});
setInterval(refreshDashboard,30000);
