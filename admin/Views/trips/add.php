<?php
// Variables provided by controller:
// - $drivers
// - $origins
// - $products
?>

<?php include __DIR__ . '/../../templates/header.php'; ?>

<div class="container">
    <h2>Créer un nouveau voyage</h2>

    <!-- ===== QR SCANNER ===== -->
    <div id="qr-reader" style="width:100%;max-width:450px;height:300px;border:1px solid #ccc;margin-bottom:10px;"></div>
    <div id="qr-msg" style="color:green;margin-bottom:15px;"></div>

    <!-- ===== FORMULAIRE ===== -->
    <form action="actions/trip_actions.php?action=add" method="post" onsubmit="return validateForm()">

        <label>Camion *</label>
        <select name="truck_id" id="truck_id" required>
            <option value="">Scanner le camion...</option>
        </select>

        <label>Conducteur *</label>
        <select name="driver_id" required>
            <option value="">-- conducteur --</option>
            <?php foreach ($drivers as $d): ?>
                <option value="<?= $d['id'] ?>">
                    <?= htmlspecialchars($d['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Origine *</label>
        <select name="origin" required>
            <option value="">-- origine --</option>
            <?php foreach ($origins as $o): ?>
                <option value="<?= $o['id'] ?>">
                    <?= htmlspecialchars($o['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <hr>
        <h3>Produits</h3>
        <div id="products-container"></div>
        <button type="button" onclick="addProductField()">+ Ajouter produit</button>

        <br><br>
        <button type="submit">Créer le voyage</button>
    </form>
</div>

<!-- ===== LIB QR ===== -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
let productCounter = 0;
let productIds = [];

// ===== Gestion des produits =====
function addProductField(){
    productCounter++;
    productIds.push(productCounter);

    const container = document.getElementById('products-container');
    const div = document.createElement('div');
    div.id = 'product_'+productCounter;
    div.style = "margin-bottom:8px;border:1px solid #ddd;padding:8px";

    const productOptions = `
        <option value="">-- choisir produit --</option>
        <?php foreach ($products as $p): ?>
            <option value="<?= $p['id'] ?>">
                <?= htmlspecialchars($p['nom']) ?>
            </option>
        <?php endforeach; ?>
    `;

    div.innerHTML = `
        <select name="products[${productCounter}][produit_id]" required>
            ${productOptions}
        </select>
        <input name="products[${productCounter}][quantite]" 
               type="number" step="0.01" min="0.01" 
             placeholder="Quantité">

        <input name="products[${productCounter}][poids]" 
               type="number" step="0.01" 
               placeholder="Poids">

        <select name="products[${productCounter}][unite]">
            <option value="unite">Unité</option>
            <option value="tonne">Tonne</option>
            <option value="kg">Kg</option>
            <option value="m3">m³</option>
        </select>

        <button type="button" onclick="removeProductField(${productCounter})">X</button>
    `;

    container.appendChild(div);
}

function removeProductField(id){
    if(confirm("Supprimer ce produit ?")){
        document.getElementById('product_'+id).remove();
        productIds = productIds.filter(i => i != id);
    }
}

// ===== Validation =====
function validateForm(){
    if(productIds.length === 0){
        alert("Ajoutez au moins un produit");
        return false;
    }
    if(!document.getElementById('truck_id').value){
        alert("Scanner le camion d'abord");
        return false;
    }
    return true;
}

// ===== QR Scanner =====
function log(t){
    document.getElementById('qr-msg').innerHTML = t;
}

function initQrScanner(){
    const html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        { fps: 10, qrbox: 250 }
    );

    html5QrcodeScanner.render(
        (decodedText) => {
            try {
                const data = JSON.parse(decodedText);
                if(data.id && data.plaque){
                    document.getElementById('truck_id').innerHTML =
                        `<option value="${data.id}" selected>${data.plaque}</option>`;
                    html5QrcodeScanner.clear();
                    log("Camion sélectionné: " + data.plaque);
                } else {
                    alert("QR code non valide");
                }
            } catch(e){
                alert("QR code non valide");
            }
        },
        (error) => {}
    );
}

// ===== Init =====
window.addEventListener('DOMContentLoaded', () => {
    addProductField();
    initQrScanner();
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
