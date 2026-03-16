<?php
// No variables required; this is a standalone view.
?>

<?php require __DIR__ . '/../templates/header.php'; ?>

<h2>Scan Truck QR</h2>

<input type="text" id="qr" placeholder="Scan QR هنا">

<button onclick="checkTruck()">بحث</button>

<div id="result"></div>

<script>
function checkTruck() {
    let qr = document.getElementById('qr').value;

    fetch('check_truck.php?qr=' + encodeURIComponent(qr))
    .then(r => r.json())
    .then(data => {
        if (data.status === 'no_trip') {
            document.getElementById('result').innerHTML = `
            <h3>Truck: ${data.truck.plaque}</h3>

            Origin:
            <input id="origin">

            Cargo:
            <input id="cargo">

            <button onclick="startTrip(${data.truck.id})">
            START TRIP
            </button>
            `;
        }

        if (data.status === 'open_trip') {
            document.getElementById('result').innerHTML = `
            <h3>Cargo: ${data.trip.cargo}</h3>
            <h3>Origin: ${data.trip.origin}</h3>

            Destination:
            <input id="destination">

            <button onclick="endTrip(${data.trip.id})">
            END TRIP
            </button>
            `;
        }

        if (data.error) {
            document.getElementById('result').innerHTML = `<div style="color:red;">${data.error}</div>`;
        }
    });
}

function startTrip(truck_id) {
    fetch('api/start_trip.php', {
       method: 'POST',
       headers: { 'Content-Type': 'application/json' },
       body: JSON.stringify({
           truck_id: truck_id,
           origin: document.getElementById('origin').value,
           cargo: document.getElementById('cargo').value
       })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert('Trip Started');
            checkTruck();
        } else {
            alert(res.message || 'Error starting trip');
        }
    });
}

function endTrip(trip_id) {
    fetch('complete_trip.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: trip_id,
            destination: document.getElementById('destination').value
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert('Trip Ended');
            checkTruck();
        } else {
            alert(res.message || 'Error ending trip');
        }
    });
}
</script>

<?php require __DIR__ . '/../templates/footer.php'; ?>
