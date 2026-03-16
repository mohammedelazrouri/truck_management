<?php
// No variables required; this view relies on client-side requests to dashboard_pro.php
?>

<?php require __DIR__ . '/../../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="text-center">Situation Chantier Dashboard</h1>

    <!-- Filters Card -->
    <div class="card">
        <div class="card-header">Professional Filters</div>

        <div class="card-body">
            <!-- Top Buttons -->
            <div class="d-flex justify-content-end mb-3 btn-group-custom">
                <button class="btn btn-success" id="exportBtn">Export CSV</button>
                <button class="btn btn-primary" id="refreshBtn">Refresh</button>
            </div>

            <!-- Filter Inputs -->
            <div class="row g-2">
                <div class="col-md-3"><label>Conducteur</label><input type="text" class="form-control" id="conducteur" placeholder="Filter by conducteur"></div>
                <div class="col-md-3"><label>Matricule</label><input type="text" class="form-control" id="matricule" placeholder="Filter by matricule"></div>
                <div class="col-md-3"><label>Bon Livraison</label><input type="text" class="form-control" id="bon_livraison" placeholder="Filter by bon livraison"></div>
                <div class="col-md-3"><label>Agregat</label><input type="text" class="form-control" id="agregat" placeholder="Filter by agregat"></div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-3"><label>Fournisseur</label><input type="text" class="form-control" id="fournisseur" placeholder="Filter by fournisseur"></div>
                <div class="col-md-3"><label>Date From</label><input type="date" class="form-control" id="date_from"></div>
                <div class="col-md-3"><label>Date To</label><input type="date" class="form-control" id="date_to"></div>
                <div class="col-md-3 d-flex flex-column gap-2">
                    <button class="btn btn-primary w-100" id="filterBtn">Apply Filters</button>
                    <button class="btn btn-secondary w-100" id="resetBtn">Reset Filters</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DateLivraison</th>
                        <th>Conducteur</th>
                        <th>Matricule</th>
                        <th>Bon Pour</th>
                        <th>Bon Livraison</th>
                        <th>Agregat</th>
                        <th>Fournisseur</th>
                        <th>Quantite</th>
                        <th>Prix</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody id="data-table">
                    <tr><td colspan="11" class="text-center">Loading data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function fetchData() {
        let filters = {
            conducteur: $('#conducteur').val(),
            matricule: $('#matricule').val(),
            bon_livraison: $('#bon_livraison').val(),
            agregat: $('#agregat').val(),
            fournisseur: $('#fournisseur').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val()
        };
        $.ajax({
            url: 'dashboard_pro.php',
            type: 'GET',
            data: filters,
            dataType: 'json',
            success: function(data) {
                let rows = '';
                if(data.length > 0){
                    data.forEach(row => {
                        let montant = parseFloat(row.montant) || 0;
                        let rowClass = '';
                        if(montant > 5000) rowClass = 'row-high';
                        else if(montant > 1000) rowClass = 'row-medium';
                        else rowClass = 'row-low';
                        
                        rows += `<tr class="${rowClass}">
                            <td>${row.id}</td>
                            <td>${row.date_livraison}</td>
                            <td>${row.conducteur}</td>
                            <td>${row.matricule}</td>
                            <td>${row.bon_pour ? parseFloat(row.bon_pour).toFixed(2) : 'N/A'}</td>
                            <td>${row.bon_livraison}</td>
                            <td>${row.agregat}</td>
                            <td>${row.fournisseur}</td>
                            <td>${parseFloat(row.quantite).toFixed(2)}</td>
                            <td>${parseFloat(row.prix).toFixed(2)}</td>
                            <td><strong>${parseFloat(row.montant).toFixed(2)}</strong></td>
                        </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="11" class="text-center">No data found for selected filters.</td></tr>';
                }
                $('#data-table').html(rows);
            },
            error: function() {
                $('#data-table').html('<tr><td colspan="11" class="text-center text-danger">Error fetching data.</td></tr>');
            }
        });
    }

    // Initial load
    fetchData();

    // Buttons
    $('#filterBtn').click(fetchData);
    $('#resetBtn').click(function(){
        $('#conducteur,#matricule,#bon_livraison,#agregat,#fournisseur,#date_from,#date_to').val('');
        fetchData();
    });
    $('#refreshBtn').click(fetchData);

    $('#exportBtn').click(function(){
        let csv = [];
        $("table tr").each(function(){
            let row = [];
            $(this).find("th,td").each(function(){ row.push($(this).text()); });
            csv.push(row.join(","));
        });
        let blob = new Blob([csv.join("\n")], { type: 'text/csv' });
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'situation_chantier.csv';
        link.click();
    });
});
</script>

<?php require __DIR__ . '/../../templates/footer.php'; ?>
