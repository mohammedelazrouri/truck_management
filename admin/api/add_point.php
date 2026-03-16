<?php 
require_once 'session_check.php';
require_once '../config/db.php';
include 'templates/header.php';

// Data
$drivers = $pdo->query("SELECT id, nom FROM Drivers")->fetchAll(PDO::FETCH_ASSOC);
$origins = $pdo->query("SELECT id, nom FROM Points WHERE type IN ('origin','both')")->fetchAll(PDO::FETCH_ASSOC);
$destinations = $pdo->query("SELECT id, nom FROM Points WHERE type IN ('destination','both')")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
  <h2>Créer un nouveau voyage</h2>

  <!-- QR -->
  <div id="qr-reader" style="max-width:450px;height:300px;border:1px solid #ccc;"></div>
  <div id="qr-msg" style="color:green;margin-bottom:15px;"></div>

  <form action="actions/trip_actions.php?action=add" method="post" onsubmit="return validateForm()">

    <label>Camion *</label>
    <select name="truck_id" id="truck_id" required>
      <option value="">Scanner le camion...</option>
    </select>

    <label>Conducteur (optionnel)</label>
    <select name="driver_id">
      <option value="">-- non spécifié --</option>
      <?php foreach($drivers as $d): ?>
        <option value="<?= $d['id'] ?>"><?= $d['nom'] ?></option>
      <?php endforeach; ?>
    </select>

    <label>Origine *</label>
    <select name="origin" id="origin" required>
      <option value="">-- origine --</option>
      <?php foreach($origins as $o): ?>
        <option value="<?= $o['id'] ?>"><?= $o['nom'] ?></option>
      <?php endforeach; ?>
    </select>

    <label>Destination *</label>
    <select name="destination" id="destination" required>
      <option value="">-- destination --</option>
      <?php foreach($destinations as $d): ?>
        <option value="<?= $d['id'] ?>"><?= $d['nom'] ?></option>
      <?php endforeach; ?>
    </select>

    <hr>
    <h3>Marchandises</h3>
    <div id="goods-container"></div>
    <button type="button" onclick="addGoodField()">+ Ajouter</button>

    <br><br>
    <button type="submit">Créer le voyage</button>
  </form>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
let goodCounter = 0;
let goodIds = [];

function addGoodField(){
  goodCounter++;
  goodIds.push(goodCounter);
  goods_container.innerHTML += `
    <div id="good_${goodCounter}">
      <input name="goods[${goodCounter}][nom]" placeholder="nom" required>
      <input name="goods[${goodCounter}][quantite]" type="number" placeholder="quantité" required>
      <input name="goods[${goodCounter}][poids]" type="number" placeholder="poids">
      <button type="button" onclick="removeGoodField(${goodCounter})">X</button>
    </div>`;
}

function removeGoodField(id){
  document.getElementById('good_'+id).remove();
  goodIds = goodIds.filter(i => i !== id);
}

function validateForm(){
  if(goodIds.length === 0){
    alert("Ajoutez au moins une marchandise");
    return false;
  }
  if(origin.value === destination.value){
    alert("Origine ≠ destination");
    return false;
  }
  if(!truck_id.value){
    alert("Scanner le camion");
    return false;
  }
  return true;
}

// QR
const scanner = new Html5QrcodeScanner("qr-reader",{fps:10,qrbox:250});
scanner.render((txt)=>{
  const data = JSON.parse(txt);
  truck_id.innerHTML = `<option value="${data.id}" selected>${data.plaque}</option>`;
  scanner.clear();
  qr_msg.innerHTML = "Camion sélectionné : " + data.plaque;
});

window.onload = addGoodField;
</script>

<?php include 'templates/footer.php'; ?>
