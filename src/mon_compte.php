<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'classes/MonCompte.php';
require_once 'classes/Connexion.php';
require_once 'classes/Covoiturage.php';
require_once 'fonction_php/fonction.php';

$info_utilisateur = MonCompte::recupDonnee($pdo,$id_utilisateur);

if(!$info_utilisateur['success']){
    $_SESSION['erreur_connexion'] = $info_utilisateur['message'];
    session_destroy();
    header('location: connexion.php');
    exit;
}

//besoin de gerer la participation des covoit a cause du annuler
$covoit_planifier_encours = array_filter($info_utilisateur['info_covoiturage_p'], function($c){
    return $c['statut_covoit'] === 'en_cours' || $c['statut_covoit'] === 'planifier';
});

//gestion des differents POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch ($_POST['type_POST']){
        case 'MAJ_type_utilisateur':
            $fonction = MonCompte::changerTypeUtilisateur($pdo,$id_utilisateur,$_POST['type_u']);
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'MAJ_preferences':
            $fonction = MonCompte::changerPréférence($pdo,$id_utilisateur,$_POST);
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'supprimer_voiture':
            $fonction = MonCompte::supprimerVoiture($pdo,$id_utilisateur,$_POST['id_voiture']);    
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'ajouter_voiture':
            $fonction = MonCompte::ajouterVoiture($pdo,$id_utilisateur,$_POST);    
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'supprimer_covoiturage':
            $fonction = Covoiturage::supprimerCovoiturage($pdo,$id_utilisateur,$_POST['id_covoiturage']);
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'demarrer_covoiturage':
            $fonction = Covoiturage::demarrerCovoiturage($pdo,$id_utilisateur,$_POST['id_covoiturage']);
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'terminer_covoiturage':
            $fonction = Covoiturage::terminerCovoiturage($pdo,$id_utilisateur,$_POST['id_covoiturage']);
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            foreach($fonction['email'] as $email){
                $subject = 'Mise à jour de votre Covoiturage';
                $message = "Bonjour,\n\nVotre covoiturage est désormais terminé !\n\nVeuillez vous rendre sur votre espace personnel si vous souhaitez donner votre avis !\n\nCordialement,\nL'équipe Eco-Ride";
                $headers = 'From: noreply@eco-ride.com' . "\r\n" .'Content-Type: text/plain; charset=UTF-8';
                mail($email, $subject, $message, $headers);
                }
            header('Location: mon_compte.php');
            exit;
            
        case 'annuler_covoiturage':
            $fonction = Covoiturage :: annulerCovoiturage($pdo,$id_utilisateur,$_POST['id_covoiturage']);
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'ajouter_avis':
            $fonction = Covoiturage::donnerAvis($pdo,$_POST['id_covoiturage'],$id_utilisateur,$_POST);
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;
            $fonction2 = Covoiturage::confirmerCovoiturage($pdo, $id_utilisateur, $_POST['id_covoiturage']);
            if(!$fonction2['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            
        case 'valider_avis':
            $fonction = MonCompte::changerAvis($pdo,$_POST['id_avis'],$_POST);
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;
        case 'confirmer_covoiturage':
            $fonction = Covoiturage::confirmerCovoiturage($pdo, $id_utilisateur, $_POST['id_covoiturage']);
            if(!$fonction['success']){
                $_SESSION['erreur'] = $fonction['message'];
            }
            header('Location: mon_compte.php');
            exit;
        default:
            header('Location: mon_compte.php');
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte - Eco Ride</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <main>
        <!-- Gestion des erreurs -->
        <?php if(!$info_utilisateur['success']): ?>
            <div class="message message-error"><?= htmlspecialchars($info_utilisateur['message']) ?></div>
        <?php endif; ?>

        <?php if(isset($erreur)): ?>
            <div class="message message-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <?php if(isset($erreur_avis)): ?>
            <div class="message message-error"><?= htmlspecialchars($erreur_avis) ?></div>
        <?php endif; ?>

        <?php if($info_utilisateur['success']): ?>
        <div class="account-container">

            <!-- Section profil utilisateur -->
            <section class="user-profile">
                <div class="user-header">
                    <img src="assets/pp/<?= htmlspecialchars($info_utilisateur['info_utilisateur']['photo'])?>" 
                         alt="Photo de profil" class="user-avatar">
                    <div class="user-info">
                        <h2><?= htmlspecialchars($info_utilisateur['info_utilisateur']['pseudo'])?></h2>
                    </div>
                </div>
                
                <div class="user-details">
                    <div class="user-detail-item">
                        <span>Email</span>
                        <span><?= htmlspecialchars($info_utilisateur['info_utilisateur']['email'])?></span>
                    </div>
                    <div class="user-detail-item">
                        <span>Crédit</span>
                        <span><?= htmlspecialchars($info_utilisateur['info_utilisateur']['credit'])?> €</span>
                    </div>
                </div>
            </section>

            <!-- Bouton déconnexion -->
            <section class="account-section logout-section">
                <form method="POST" action="connexion/deconnexion.php">
                    <button type="submit" class="btn btn-secondary">Se déconnecter</button>
                </form>
            </section>

             <!--Section role-->
            <?php 
                $isEmploye = false;
                $isAdministrateur = false;
                foreach($info_utilisateur['info_role'] as $valeur_role){
                    if($valeur_role['libelle'] === 'Employe'){ $isEmploye = true;}
                    if($valeur_role['libelle'] === 'Administrateur'){ $isAdministrateur = true; $isEmploye = true;}
                }
            ?>

             <!-- Section employé -->
            <?php if ($isAdministrateur): ?>
            <section class="account-section employee-section">
                <h2>Espace Administrateur</h2>
                <div class="employee-info"> 
                    <a href="admin.php" class="btn btn-primary">Voir et gérer</a>
                </div>
            </section>
            <?php endif; ?>

            <!-- Section employé -->
            <?php if ($isEmploye): ?>
            <section class="account-section employee-section">
                <h2>Espace Employé</h2>
                <div class="employee-info">
                    <span><?= htmlspecialchars($info_utilisateur['info_avis_attente']['nb_attente'])?> avis en attente de validation</span> 
                    <a href="gerer_avis.php" class="btn btn-primary">Voir et gérer</a>
                </div>
            </section>
            <?php endif; ?>

            <!-- Type utilisateur -->
            <section class="account-section">
                <h2>Mon Type d'Utilisateur</h2>
                
                <div class="user-type-current">
                    <strong>Type actuel :</strong> <?= htmlspecialchars($info_utilisateur['info_utilisateur']['type_utilisateur'])?>
                </div>

                <form method="POST" class="user-type-form">
                    <input type="hidden" name="type_POST" value="MAJ_type_utilisateur">
                    
                    <select name="type_u" required>
                        <option value="">-- Choisir un nouveau type --</option>
                        <option value="Passager">Passager</option>
                        <option value="Conducteur">Conducteur</option>
                        <option value="Passager et Conducteur">Les deux à la fois</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Confirmer</button>
                </form>
            </section>

            <!-- Section Statut utilisateur -->
            <?php 
            $isPassenger = false;
            $isDriver = false;
            switch ($info_utilisateur['info_utilisateur']['type_utilisateur']) {
                case 'Passager': $isPassenger = true; break;
                case 'Conducteur' : $isDriver = true; break;
                case 'Passager et Conducteur': $isPassenger = true; $isDriver=true; break;
            }
            ?>

            <!-- Préférences (visible si Conducteur) -->
            <?php if($isDriver) : ?>
            <section class="account-section">
                <h2>Mes Préférences</h2>
                
                <div class="preferences-toggle">
                    <button type="button" class="preferences-button" onclick="togglePreferences()">
                        Modifier mes préférences
                    </button>
                </div>

                <div class="preferences-content" id="preferencesContent">
                    <form method="POST">
                        <input type="hidden" name="type_POST" value="MAJ_preferences">
                        
                        <div class="preferences-grid">
                            <?php foreach ($info_utilisateur['info_preference'] as $preference => $valeur_pref): ?>
                                <?php if($info_utilisateur['info_utilisateur']['sexe'] !== 'Femme'){
                                    if($preference === 'ladies_only'){continue;}
                                }?>
                            <div class="preference-item">
            
                                <img src="<?= cheminImgPreference($preference)?>" alt="<?= htmlspecialchars($preference)?>">
                                <?php $pref_label = ucfirst(str_replace('_', ' ', $preference)); $pref_label = ucfirst(str_replace('E', 'Ê', $pref_label))?>

                                <label for="pref_<?= htmlspecialchars($preference) ?>">
                                    <?= htmlspecialchars($pref_label) ?>
                                </label>
                                <input type="checkbox" 
                                       id="pref_<?= htmlspecialchars($preference) ?>"
                                       name="<?= htmlspecialchars($preference) ?>" 
                                       value="accepter" 
                                       <?= ($valeur_pref == 'accepter') ? 'checked' : '' ?>>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="margin-top: 20px; width: 100%;">
                            Valider mes préférences
                        </button>
                    </form>
                </div>
            </section>

            <!-- Mes voitures -->
            <section class="account-section">
                <h2><?= (count($info_utilisateur['info_voiture']) > 1) ? "Mes Voitures" : "Ma Voiture" ?></h2>
                
                <div class="cars-grid">
                    <?php foreach ($info_utilisateur['info_voiture'] as $voiture): ?>
                    <div class="car-card">
                        <div class="car-delete">
                            <form method="POST" style="display: inline;">
                                <input name="id_voiture" type="hidden" value="<?= $voiture['id_voiture']?>">
                                <input name="type_POST" type="hidden" value="supprimer_voiture">
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette voiture ?')">
                                    Supprimer
                                </button>
                            </form>
                        </div>

                        <div class="car-header">
                            <img src="<?= ($voiture['energie'] == 'Hybride' || $voiture['energie'] == 'Electrique') ? 
                                     'assets/icons/icon_card_voiture_verte.png' : 'assets/icons/icon_card_voiture.png' ?>" 
                                 alt="<?= $voiture['energie'] ?>">
                            <p><?= htmlspecialchars($voiture['energie']) ?></p>
                        </div>

                        <div class="car-details">
                            <div class="car-detail-row">
                                <span>Marque</span>
                                <span><?= htmlspecialchars($voiture['marque']) ?></span>
                            </div>
                            <div class="car-detail-row">
                                <span>Modèle</span>
                                <span><?= htmlspecialchars($voiture['modele']) ?></span>
                            </div>
                            <div class="car-detail-row">
                                <span>Immatriculation</span>
                                <span><?= htmlspecialchars($voiture['immat']) ?></span>
                            </div>
                            <div class="car-detail-row">
                                <span>Couleur</span>
                                <span><?= htmlspecialchars($voiture['couleur']) ?></span>
                            </div>
                            <div class="car-detail-row">
                                <span>Places disponibles</span>
                                <span><?= htmlspecialchars($voiture['nb_place']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <a href="ajouter_voiture.php" class="btn btn-primary add-link">Ajouter une nouvelle voiture</a>
            </section>
            <?php endif; ?>

            <!-- Covoiturages conducteur en cours -->
            <?php if($isDriver): ?>
            <section class="account-section">
                <h2>Mes Covoiturages en Cours</h2>
                
                <?php if(empty($info_utilisateur['info_covoiturage_c'])): ?>
                    <div class="empty-state">Aucun covoiturage créé</div>
                <?php else: ?>
                    <div class="covoiturage-grid">
                        <?php foreach ($info_utilisateur['info_covoiturage_c'] as $covoiturage): ?>
                            <?php if($covoiturage['statut_covoit'] === 'terminer' || $covoiturage['statut_covoit'] === 'annuler') continue; ?>
                            
                            <div class="covoiturage_card">
                                <div class="div_covoit">
                                    <div class="date_covoit">
                                        <span>Le <?= date('d/m/Y',strtotime($covoiturage['date_depart']))?></span>
                                    </div>
                                    
                                    <div class="trajet_heures">
                                        <div class="trajet">
                                            <p><?= htmlspecialchars($covoiturage['lieu_depart'])?></p>
                                            <p><?= substr($covoiturage['duree_voyage'], 0, 5) ?></p>
                                            <p><?= htmlspecialchars($covoiturage['lieu_arrive'])?></p>
                                        </div>
                                        <div class="heure">
                                            <p><?= substr($covoiturage['heure_depart'], 0, 5) ?></p>
                                            <p>🚗</p>
                                            <p><?= date('H:i', strtotime($covoiturage['heure_depart']) + strtotime($covoiturage['duree_voyage']) - strtotime('00:00:00')) ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="actions_covoit">
                                        <span><?= htmlspecialchars($covoiturage['nb_place_dispo'])?> place<?= ($covoiturage['nb_place_dispo'] > 1) ? 's' : '' ?> disponible<?= ($covoiturage['nb_place_dispo'] > 1) ? 's' : '' ?></span>
                                        
                                        <?php if($covoiturage['statut_covoit'] === 'planifier'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="type_POST" value="demarrer_covoiturage">
                                                <input type="hidden" name="id_covoiturage" value="<?=$covoiturage['id_covoiturage']?>">
                                                <button type="submit" class="btn btn-primary btn-small">Démarrer</button>
                                            </form>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="type_POST" value="supprimer_covoiturage">
                                                <input type="hidden" name="id_covoiturage" value="<?=$covoiturage['id_covoiturage']?>">
                                                <button type="submit" class="btn btn-secondary btn-small" onclick="return confirm('Êtes-vous sûr ?')">Annuler</button>
                                            </form>
                                        <?php elseif($covoiturage['statut_covoit'] === 'en_cours'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="type_POST" value="terminer_covoiturage">
                                                <input type="hidden" name="id_covoiturage" value="<?=$covoiturage['id_covoiturage']?>">
                                                <button type="submit" class="btn btn-primary btn-small">Terminer</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a href="ajouter_covoiturage.php" class="btn btn-primary add-link">Ajouter un covoiturage</a>
            </section>
            <?php endif; ?>

            <!-- Covoiturages passager en cours -->
            <?php if($isPassenger): ?>
                <section class="account-section">
                    <h2>Mes Participations en Cours</h2>

                    <?php 
                    $covoit_planifier_encours = array_filter($info_utilisateur['info_covoiturage_p'], function($c){
                    return in_array($c['statut_covoit'], ['planifier', 'en_cours', 'terminer']) 
                        && $c['statut_reservation'] === 'active';
                    });
                    ?>

                    <?php if(empty($info_utilisateur['info_covoiturage_p'])): ?>
                        <div class="empty-state">Vous ne participez à aucun covoiturage</div>
                    <?php else: ?>
                        <div class="covoiturage-grid">
                            <?php foreach ($info_utilisateur['info_covoiturage_p'] as $covoiturage): ?>
                                <?php if($covoiturage['statut_reservation'] !== 'active') continue; ?>
                                <div class="covoiturage_card">
                                    <div class="div_covoit">
                                        <div class="date_covoit">
                                            <span>Le <?= date('d/m/Y', strtotime($covoiturage['date_depart'])) ?></span>
                                        </div>

                                        <div class="trajet_heures">
                                            <div class="trajet">
                                                <p><?= htmlspecialchars($covoiturage['lieu_depart']) ?></p>
                                                <p><?= substr($covoiturage['duree_voyage'], 0, 5) ?></p>
                                                <p><?= htmlspecialchars($covoiturage['lieu_arrive']) ?></p>
                                            </div>
                                            <div class="heure">
                                                <p><?= substr($covoiturage['heure_depart'], 0, 5) ?></p>
                                                <p>🚗</p>
                                                <p><?= date('H:i', strtotime($covoiturage['heure_depart']) + strtotime($covoiturage['duree_voyage']) - strtotime('00:00:00')) ?></p>
                                            </div>
                                        </div>

                                        <div class="actions_covoit">
                                            <span>
                                                <?= htmlspecialchars($covoiturage['nb_place_reserve']) ?>
                                                place<?= ($covoiturage['nb_place_reserve'] > 1) ? 's' : '' ?> réservée<?= ($covoiturage['nb_place_reserve'] > 1) ? 's' : '' ?>
                                            </span>

                                            <?php if($covoiturage['statut_covoit'] === 'planifier'): ?>
                                                <form method="POST" action="mon_compte.php" style="display: inline;">
                                                    <input type="hidden" name="type_POST" value="annuler_covoiturage">
                                                    <input type="hidden" name="id_covoiturage" value="<?=$covoiturage['id_covoiturage']?>">
                                                    <button type="submit" class="btn btn-secondary btn-small" onclick="return confirm('Êtes-vous sûr ?')">Annuler</button>
                                                </form>
                                            <?php elseif($covoiturage['statut_covoit'] === 'en_cours'): ?>
                                                <span class="badge">Covoiturage en cours</span>

                                            <?php elseif($covoiturage['statut_covoit'] === 'terminer'): ?>
                                                <form method="POST" action="mon_compte.php" class="confirmation-form" style="display:inline;">
                                                    <input type="hidden" name="type_POST" value="confirmer_covoiturage">
                                                    <input type="hidden" name="id_covoiturage" value="<?= $covoiturage['id_covoiturage'] ?>">
                                                    <button type="submit" name="action" value="confirmer" class="btn btn-secondary btn-small" 
                                                    onclick="return confirm('Confirmer sans donner d\'avis ?')">
                                                    Confirmer sans donner votre avis
                                                    </button>
                                                </form>
                                                <a href="donner_avis.php?id_c=<?= $covoiturage['id_covoiturage'] ?>" class="btn btn-primary btn-small">
                                                Donner votre avis
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <!-- Section Historiques -->
            <?php if($isDriver): ?>
            <section class="account-section">
                <h2>Mon Historique de Covoiturages</h2>
                
                <?php if(empty($info_utilisateur['info_historique_c'])): ?>
                    <div class="empty-state">Vous n'avez terminé aucun covoiturage</div>
                <?php else: ?>
                    <div class="covoiturage-grid">
                        <?php foreach($info_utilisateur['info_historique_c'] as $h_covoit_c): ?>
                            <div class="covoiturage_card">
                                <div class="div_covoit">
                                    <div class="date_covoit">
                                        <span>Le <?= date('d/m/Y',strtotime($h_covoit_c['date_depart']))?></span>
                                    </div>
                                    
                                    <div class="trajet_heures">
                                        <div class="trajet">
                                            <p><?= htmlspecialchars($h_covoit_c['lieu_depart'])?></p>
                                            <p><?= substr($h_covoit_c['duree_voyage'], 0, 5) ?></p>
                                            <p><?= htmlspecialchars($h_covoit_c['lieu_arrive'])?></p>
                                        </div>
                                        <div class="heure">
                                            <p><?= substr($h_covoit_c['heure_depart'], 0, 5) ?></p>
                                            <p>🚗</p>
                                            <p><?= date('H:i', strtotime($h_covoit_c['heure_depart']) + strtotime($h_covoit_c['duree_voyage']) - strtotime('00:00:00')) ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="actions_covoit">
                                        <span><?= htmlspecialchars($h_covoit_c['nb_place_dispo'])?> place<?= ($h_covoit_c['nb_place_dispo'] > 1) ? 's' : '' ?> libre<?= ($h_covoit_c['nb_place_dispo'] > 1) ? 's' : '' ?></span>
                                        <span class="badge <?= $h_covoit_c['statut_covoit'] === 'terminer' ? 'badge-success' : 'badge-secondary' ?>"><?= htmlspecialchars($h_covoit_c['statut_covoit'])?></span>
                                        
                                        <?php if($h_covoit_c['statut_covoit'] === 'terminer'): ?>
                                            <a href="voir_avis.php?id_c=<?=$h_covoit_c['id_covoiturage']?>" class="btn btn-primary btn-small">Voir les Avis</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <!-- Historique passager -->
            <?php if($isPassenger): ?>
            <section class="account-section">
                <h2>Mon Historique de Participations</h2>
                
                <?php if(empty($info_utilisateur['info_historique_p'])): ?>
                    <div class="empty-state">Vous n'avez participé à aucun covoiturage</div>
                <?php else: ?>
                    <div class="covoiturage-grid">
                        <?php foreach($info_utilisateur['info_historique_p'] as $h_covoit_p): ?>
                            <div class="covoiturage_card">
                                <div class="div_covoit">
                                    <div class="date_covoit">
                                        <span>Le <?= date('d/m/Y',strtotime($h_covoit_p['date_depart']))?></span>
                                    </div>
                                    
                                    <div class="trajet_heures">
                                        <div class="trajet">
                                            <p><?= htmlspecialchars($h_covoit_p['lieu_depart'])?></p>
                                            <p><?= substr($h_covoit_p['duree_voyage'], 0, 5) ?></p>
                                            <p><?= htmlspecialchars($h_covoit_p['lieu_arrive'])?></p>
                                        </div>
                                        <div class="heure">
                                            <p><?= substr($h_covoit_p['heure_depart'], 0, 5) ?></p>
                                            <p>🚗</p>
                                            <p><?= date('H:i', strtotime($h_covoit_p['heure_depart']) + strtotime($h_covoit_p['duree_voyage']) - strtotime('00:00:00')) ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="actions_covoit">
                                        <span><?= htmlspecialchars($h_covoit_p['nb_place_reserve'])?> passager<?= ($h_covoit_p['nb_place_reserve'] > 1) ? 's' : '' ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>

        </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php' ?>

    <script>
        function togglePreferences() {
            const content = document.getElementById('preferencesContent');
            const button = document.querySelector('.preferences-button');
            
            if (content.classList.contains('active')) {
                content.classList.remove('active');
                button.textContent = 'Modifier mes préférences';
            } else {
                content.classList.add('active');
                button.textContent = 'Masquer les préférences';
            }
        }
    </script>
</body>
</html>