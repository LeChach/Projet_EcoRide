<?php
require_once 'config/database.php';
require_once 'connexion/session_prive.php';
require_once 'classes/MonCompte.php';

$admin_info = MonCompte::chargerUtilisateurAdmin($pdo,$id_utilisateur);
if(!$admin_info['success']){
    $_SESSION['erreur'] = $admin_info['message'];
    header("location: mon_compte.php");
    exit;
}

$covoitParJour = $admin_info['info_nb_covoit_j'];
$creditsParJour = $admin_info['info_credit_j'];
$totalCredits = $admin_info['info_credit_total'];
$utilisateurs =  $admin_info['info_utilisateur'];


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if($_POST['type_POST'] === 'ban'){
        $ban = MonCompte::suspendreUtilisateur($pdo,$id_utilisateur,$_POST['id_utilisateur']);
        if(!$ban['success']){
            $_SESSION['erreur'] = $ban['message'];
            header("location: mon_compte.php");
            exit;
        }
        header("location: admin.php");
        exit;   
    }
    if($_POST['type_POST'] === 'nouveau_employe'){
        $promotion = MonCompte::nouvelEmploye($pdo,$id_utilisateur,$_POST['id_utilisateur']);
        if(!$promotion['success']){
            $_SESSION['erreur'] = $promotion['message'];
            header("location: mon_compte.php");
            exit;
        }
        header("location: admin.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Admin - Eco Ride</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body>
    <?php include 'includes/header.php' ?>

    <main>
        <div class="admin-dashboard">
            <h1>Espace Administrateur</h1>

            <!-- Section Graphiques -->
            <div class="dashboard-graphs">
                <div class="graph-card">
                    <h3>Covoiturages par jour</h3>
                    <canvas id="graphCovoit"></canvas>
                </div>
                <div class="graph-card">
                    <h3>Crédits gagnés par jour</h3>
                    <canvas id="graphCredits"></canvas>
                </div>
            </div>

            <!-- Nombre total de crédits -->
            <div class="credits-total">
                Crédits totaux de la plateforme : <?= number_format(htmlspecialchars($totalCredits)) ?> 💰
            </div>

            <!-- Gestion des comptes -->
            <div class="user-management">
                <h2>👥 Gestion des comptes</h2>
                
                <!-- En-tête pour les colonnes -->
                <div class="user-header">
                    <span>Pseudo</span>
                    <span>Email</span>
                    <span>Sexe</span>
                    <span>Téléphone</span>
                    <span>Crédits</span>
                    <span>Inscription</span>
                    <span>Note</span>
                    <span>Rôle</span>
                </div>

                <?php foreach($utilisateurs as $user): ?>
                    <div class="user-card">
                        <span><?= htmlspecialchars($user['pseudo']) ?></span>
                        <span><?= htmlspecialchars($user['email']) ?></span>
                        <span><?= htmlspecialchars($user['sexe']) ?></span>
                        <span><?= htmlspecialchars($user['telephone']) ?></span>
                        <span><?= htmlspecialchars($user['credit']) ?> €</span>
                        <span><?= htmlspecialchars(date('d/m/Y', strtotime($user['date_inscription']))) ?></span>
                        <span><?= htmlspecialchars($user['note']) ?>/5 ⭐</span>
                        <span class="status-badge status-<?= strtolower($user['role']) ?>">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>

                        <!-- Actions pour chaque utilisateur -->
                        <form action="admin.php" method="POST" style="display: inline;">
                            <input type="hidden" name="type_POST" value="nouveau_employe">
                            <input type="hidden" name="id_utilisateur" value="<?= $user['id_utilisateur'] ?>">
                            <button type="submit">Promouvoir Employé</button>
                        </form>

                        <form method="POST" action="admin.php" style="display: inline;">
                            <input type="hidden" name="type_POST" value="ban">
                            <input type="hidden" name="id_utilisateur" value="<?= $user['id_utilisateur'] ?>">
                            <button type="submit">Suspendre</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="admin-close-section">
                <a href="mon_compte.php" class="admin-close-button">Fermer l'administration</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php' ?>

    <script>
        // Configuration des graphiques Chart.js
        const covoitData = <?= json_encode($covoitParJour); ?>;
        const creditsData = <?= json_encode($creditsParJour); ?>;

        // Configuration commune pour les graphiques
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Poppins',
                            size: 14
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    },
                    ticks: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    },
                    ticks: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                }
            }
        };

        // Graphique des covoiturages
        const ctxCovoit = document.getElementById('graphCovoit').getContext('2d');
        new Chart(ctxCovoit, {
            type: 'line',
            data: {
                labels: Object.keys(covoitData),
                datasets: [{
                    label: 'Nombre de covoiturages',
                    data: Object.values(covoitData),
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4CAF50',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: commonOptions
        });

        // Graphique des crédits
        const ctxCredits = document.getElementById('graphCredits').getContext('2d');
        new Chart(ctxCredits, {
            type: 'line',
            data: {
                labels: Object.keys(creditsData),
                datasets: [{
                    label: 'Crédits gagnés (€)',
                    data: Object.values(creditsData),
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2196F3',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: commonOptions
        });
    </script>
</body>
</html>