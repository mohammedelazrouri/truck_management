<?php
// Variables provided by controller:
// - $destinations
?>

<?php include __DIR__ . '/../../templates/header.php'; ?>

<div class="container">
    <h2>إنهاء الرحلة - Destination</h2>

    <!-- QR Scanner -->
    <div id="qr-reader" style="width:100%;max-width:450px;height:300px;border:1px solid #ccc;margin-bottom:10px"></div>
    <div id="qr-msg" style="margin:10px 0;color:green"></div>

    <!-- Trip info -->
    <div id="trip-result"></div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let destinations = <?= json_encode($destinations, JSON_UNESCAPED_UNICODE) ?>;

function log(msg){
    document.getElementById('qr-msg').innerHTML = msg;
}

// ===== QR Scanner =====
function initScanner(){
    const scanner = new Html5QrcodeScanner("qr-reader",{ fps:10, qrbox:250 });

    scanner.render(
        (decodedText)=>{
            try{
                const data = JSON.parse(decodedText);

                if(!data.plaque){
                    alert("QR غير صالح");
                    return;
                }

                log("تم قراءة الشاحنة: " + data.plaque);
                fetchTrip(data.plaque);
                scanner.clear();

            }catch(e){
                alert("QR غير صالح!");
            }
        },
        (error)=>{}
    );
}

// ===== Fetch Trip =====
function fetchTrip(plaque){
    log("جاري البحث عن الرحلة...");

    fetch("get_trip_by_truck.php?plaque=" + encodeURIComponent(plaque))
    .then(r => r.json())
    .then(res => {
        if(!res.success){
            alert(res.message);
            return;
        }
        displayTrip(res.trip);
    })
    .catch(() => alert("خطأ أثناء جلب البيانات"));
}

// ===== Display Trip =====
function displayTrip(trip){

    let html = `
    <div style="border:1px solid #ccc;padding:10px;margin-top:10px">
        <h3>معلومات الرحلة</h3>
        <b>الشاحنة:</b> ${trip.plaque}<br>
        <b>السائق:</b> ${trip.driver ?? 'غير محدد'}<br>
        <b>البداية:</b> ${trip.start_time}<br>

        <hr>

        <h4>اختر الوجهة</h4>
        <select id="destination-select">
            <option value="">-- اختر الوجهة --</option>
    `;

    destinations.forEach(d=>{
        html += `<option value="${d.id}">${d.nom}</option>`;
    });

    html += `</select><hr><h4>البضائع</h4>`;

    if(trip.goods && trip.goods.length){
        trip.goods.forEach(g=>{
            html += `- ${g.nom} | الكمية: ${g.quantite} | الوزن: ${g.poids}<br>`;
        });
    }else{
        html += "لا توجد بضائع";
    }

    html += `
        <hr>
        <button onclick="completeTrip(${trip.trip_id})"
            style="padding:8px 15px;background:green;color:white;border:none">
            تأكيد الوصول
        </button>
    </div>`;

    document.getElementById('trip-result').innerHTML = html;
}

// ===== Complete Trip =====
function completeTrip(trip_id){

    const dest = document.getElementById('destination-select').value;

    if(!dest){
        alert("اختر الوجهة أولاً");
        return;
    }

    if(!confirm("هل تريد إنهاء الرحلة؟")) return;

    fetch("complete_trip.php",{
        method:"POST",
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:"id="+trip_id+"&destination="+dest
    })
    .then(r => r.json())
    .then(res=>{
        if(res.success){
            alert("تم إنهاء الرحلة بنجاح");
            location.reload();
        }else{
            alert(res.message);
        }
    })
    .catch(()=> alert("حدث خطأ أثناء إنهاء الرحلة"));
}

window.addEventListener('DOMContentLoaded', initScanner);
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
