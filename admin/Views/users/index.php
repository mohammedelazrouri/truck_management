<?php
// This view is loaded by UsersController.
// The page relies on client-side API requests (actions/get_users.php and actions/update_user.php).
?>

<?php require __DIR__ . '/../../templates/header.php'; ?>

<div class="container-fluid">
    <h1>Gestion des Utilisateurs</h1>

    <!-- Search & Filter Card -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header bg-primary text-white">
            <strong>Recherche des Utilisateurs</strong>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <label for="search-nom">Nom</label>
                    <input type="text" class="form-control" id="search-nom" placeholder="Rechercher par nom...">
                </div>
                <div class="col-md-3">
                    <label for="search-email">Email</label>
                    <input type="text" class="form-control" id="search-email" placeholder="Rechercher par email...">
                </div>
                <div class="col-md-3">
                    <label for="search-role">Rôle</label>
                    <select class="form-control" id="search-role">
                        <option value="">-- Tous les rôles --</option>
                        <option value="principal">Principal</option>
                        <option value="admin">Admin</option>
                        <option value="pointer">Pointer</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex flex-column gap-2" style="justify-content: flex-end;">
                    <button class="btn btn-primary w-100" id="search-btn">🔍 Rechercher</button>
                    <button class="btn btn-secondary w-100" id="reset-btn">✔️ Réinitialiser</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <div style="display:flex; justify-content: space-between; align-items: center;">
                <span><strong>Liste des Utilisateurs</strong></span>
                <span id="user-count" style="font-size:12px; color:#666;"></span>
            </div>
        </div>
        <div class="card-body" style="overflow-x: auto;">
            <table class="table table-striped table-bordered table-hover" id="users-table">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 20%;">Nom</th>
                        <th style="width: 25%;">Email</th>
                        <th style="width: 15%;">Rôle</th>
                        <th style="width: 10%;">Vérifié</th>
                        <th style="width: 15%;">Créé le</th>
                        <th style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr><td colspan="7" style="text-align:center; padding: 20px;">Chargement...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for editing user details (role / email) -->
<div id="editRoleModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px;">Modifier l'utilisateur</h3>
        <input type="hidden" id="edit-user-id">
        <div style="margin-bottom: 15px;">
            <label for="edit-user-name" style="display: block; margin-bottom: 5px; font-weight: bold;">Utilisateur</label>
            <input type="text" id="edit-user-name" class="form-control" disabled style="background: #f5f5f5;">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="edit-user-email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
            <input type="email" id="edit-user-email" class="form-control" required>
        </div>
        <div style="margin-bottom: 20px;">
            <label for="edit-user-role" style="display: block; margin-bottom: 5px; font-weight: bold;">Nouveau Rôle</label>
            <select id="edit-user-role" class="form-control">
                <option value="admin">Admin</option>
                <option value="principal">Principal</option>
                <option value="pointer">Pointer</option>
            </select>
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button onclick="closeEditModal()" class="btn btn-secondary">Annuler</button>
            <button onclick="saveRole()" class="btn btn-primary">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Message notification -->
<div id="message-notification" style="display:none; position: fixed; bottom: 20px; right: 20px; padding: 15px 20px; border-radius: 5px; color: white; z-index: 2000; min-width: 300px;">
</div>

<script>
let allUsers = [];

function loadUsers(nom = '', email = '', role = '') {
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 20px;">Chargement...</td></tr>';

    let params = new URLSearchParams();
    if(nom) params.append('nom', nom);
    if(email) params.append('email', email);
    if(role) params.append('role', role);

    fetch('actions/get_users.php?' + params.toString())
        .then(r => r.json())
        .then(data => {
            if(!data.success){
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color: red;">Erreur lors du chargement</td></tr>';
                return;
            }

            allUsers = data.data || [];
            document.getElementById('user-count').textContent = `Total: ${allUsers.length}`;

            if(allUsers.length === 0){
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Aucun utilisateur trouvé</td></tr>';
                return;
            }

            let html = '';
            allUsers.forEach(user => {
                const isVerified = user.is_verified == 1 ? '✅ Oui' : '❌ Non';
                const roleBadge = getRoleBadge(user.role);
                const createdDate = formatDate(user.created_at);

                html += `<tr>
                    <td>${user.id}</td>
                    <td><strong>${escapeHtml(user.nom)}</strong></td>
                    <td>${escapeHtml(user.email)}</td>
                    <td>${roleBadge}</td>
                    <td>${isVerified}</td>
                    <td>${createdDate}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="openEditModal(${user.id}, '${escapeHtml(user.nom)}', '${user.role}', '${escapeHtml(user.email)}')">Modifier</button>
                    </td>
                </tr>`;
            });

            tbody.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color: red;">Erreur réseau</td></tr>';
        });
}

function getRoleBadge(role) {
    const colors = {
        'principal': 'green',
        'admin': 'blue',
        'pointer': 'orange'
    };
    const color = colors[role] || 'gray';
    return `<span style="background: ${color}; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;">${role.toUpperCase()}</span>`;
}

function formatDate(dateStr) {
    if(!dateStr) return 'N/A';
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('fr-FR', { year: 'numeric', month: '2-digit', day: '2-digit' });
    } catch(e) {
        return dateStr;
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function openEditModal(userId, userName, currentRole, userEmail) {
    document.getElementById('edit-user-id').value = userId;
    document.getElementById('edit-user-name').value = userName;
    document.getElementById('edit-user-role').value = currentRole;
    document.getElementById('edit-user-email').value = userEmail;
    document.getElementById('editRoleModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editRoleModal').style.display = 'none';
}

function saveRole() {
    const userId = document.getElementById('edit-user-id').value;
    const newRole = document.getElementById('edit-user-role').value;
    const newEmail = document.getElementById('edit-user-email').value.trim();

    if(!userId || !newRole || !newEmail) {
        showMessage('Erreur: données invalides', 'error');
        return;
    }

    const bodyParams = new URLSearchParams();
    bodyParams.append('user_id', userId);
    bodyParams.append('role', newRole);
    bodyParams.append('email', newEmail);

    fetch('actions/update_user.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: bodyParams.toString()
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            showMessage(data.message || 'Utilisateur mis à jour avec succès', 'success');
            closeEditModal();
            loadUsers(
                document.getElementById('search-nom').value,
                document.getElementById('search-email').value,
                document.getElementById('search-role').value
            );

            // if email was changed or server requested, redirect to verification
            if(data.redirect || data.email_changed) {
                window.location.href = 'verify_email.php';
            }
        } else {
            showMessage(data.message || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showMessage('Erreur réseau', 'error');
    });
}

function showMessage(msg, type) {
    const notif = document.getElementById('message-notification');
    notif.textContent = msg;
    notif.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
    notif.style.display = 'block';

    setTimeout(() => {
        notif.style.display = 'none';
    }, 4000);
}

document.getElementById('search-btn').addEventListener('click', () => {
    loadUsers(
        document.getElementById('search-nom').value,
        document.getElementById('search-email').value,
        document.getElementById('search-role').value
    );
});

// Initial load
loadUsers();
</script>

<?php require __DIR__ . '/../../templates/footer.php'; ?>
