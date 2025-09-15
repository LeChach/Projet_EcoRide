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
        case 'valider_avis':
            $fonction = MonCompte::changerAvis($pdo,$_POST['id_avis'],$_POST);
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
    <title>Connexion - Eco Ride</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php' ?>

    <!--affiche l'erreur de chargement des données-->
    <?php if(!$info_utilisateur['success']): ?>
        <p><?= htmlspecialchars($info_utilisateur['message'])?></p>
        <?php if ($erreur_avis): ?>
            <p style="color:red;"><?= htmlspecialchars($erreur_avis) ?></p>
        <?php endif; ?>
    <?php else :?>


        <!--affiche l'erreur d'un des POST-->
        <?php if(isset($erreur)): ?>
            <p><?= htmlspecialchars($erreur)?></p>
        <?php endif;?>

        <div class="info_utilisateur">

            <div class="pp_pseudo_info">

                <div class="pp_pseudo">
                    <div class="profil_avatar">
                        <img src="assets/pp/<?= htmlspecialchars($info_utilisateur['info_utilisateur']['photo'])?>" alt="Photo de profil">
                    </div>
                    <h2 class="pseudo"><?= htmlspecialchars($info_utilisateur['info_utilisateur']['pseudo'])?></h2>
                </div>

                <div class="info_principales">
                    <div class="info_ligne">
                        <span>Émail :</span>
                        <span><?= htmlspecialchars($info_utilisateur['info_utilisateur']['email'])?></span>
                    </div>
                    <div class="info_ligne">
                        <span>Crédit :</span>
                        <span><?= htmlspecialchars($info_utilisateur['info_utilisateur']['credit'])?></span>
                    </div>
                </div>

            </div>

        </div>
        <p></p>
        <p></p>
        <div class="deconnexion">
            <form method="POST" action="connexion/deconnexion.php">
                <button type="submit">se déconnecter</button>
            </form>
        </div>
        <p></p>
        <p></p>
        <?php if ($info_utilisateur['info_role']['libelle'] === 'Employe'):?>
        <div class="employe">
           <span><?= htmlspecialchars($info_utilisateur['info_avis_attente']['nb_attente'])?> avis en attente de validation</span> 
           <a href="gerer_avis.php">Voir et gérer</a>
        </div>
        <?php endif;?>
        <p></p>
        <p></p>
        <div class="type_utilisateur">
            <h2>MON TYPE UTILISATEUR</h2>
            <p></p>
                <span>Type utilisateur :</span>
                <span><?= htmlspecialchars($info_utilisateur['info_utilisateur']['type_utilisateur'])?></span>

                <div class="MAJ_type_utilisateur">
                    <form method="POST">
                        <input type="hidden" name="type_POST" value="MAJ_type_utilisateur">

                        <label for="selection_type_u">Je veux être :</label>
                        <select name="type_u">
                            <option value="">-- Sélctionner un nouveau type --</option>
                            <option value="Passager">Passager</option>
                            <option value="Conducteur">Conducteur</option>
                            <option value="Passager et Conducteur">Les deux à la fois</option>
                        </select>
                        <button type="submit">Confirmé</button>  
                    </form>                  
                </div>
        </div>
        <p></p>
        <p></p>
        <!--Partie invisible si = passager -->
        <?php if($info_utilisateur['info_utilisateur']['type_utilisateur'] !== 'Passager' ) : ?>
        <div class="preference">
            <h2>MES PREFERENCES</h2>
            <p></p>
            <div class="liste_preference">
                <form method = "POST">
                    <input type="hidden" name="type_POST" value="MAJ_preferences">

                    <?php foreach ($info_utilisateur['info_preference'] as $preference => $valeur_pref):?>
                            <img class="icone" src="<?= cheminImgPreference($preference)?>" alt="<?= htmlspecialchars($preference)?>">
                            <span><?= htmlspecialchars($preference) ?></span>
                            <input type="checkbox" name="<?=htmlspecialchars($preference)?>" value="accepter" <?php echo ($valeur_pref == 'accepter') ? 'checked' : ''; ?>><br>
                    <?php endforeach; ?>
                    <button type="submit">Valider mes préférences</button>
                </form>
            </div>
        </div> 
        <p></p>
        <p></p>
        <div class="voiture">
            <h2><?php echo (count($info_utilisateur['info_voiture']) > 1) ? "Mes Voitures" : "Ma Voiture"; ?></h2>            
            <p></p>
            <?php foreach ($info_utilisateur['info_voiture'] as $voiture):?>

                <div class="carte_voiture" >
                    <div class = "info_energie">
                        <?php if($voiture['energie'] === 'Hybride' || $voiture['energie'] === 'Electrique') : ?>
                            <img src="assets/icons/icon_card_voiture_verte.png" alt="Voiture écologique">
                        <?php else :?>
                            <img src="assets/icons/icon_card_voiture.png" alt="Voiture Thermique">   
                        <?php endif;?>
                        <p><?=htmlspecialchars($voiture['energie'])?></p>
                        <form method="POST">
                            <input name="id_voiture" type="hidden" value="<?= $voiture['id_voiture']?>">
                            <input name="type_POST" type="hidden" value="supprimer_voiture">
                            <button type="submit">Supprimer</button>
                        </form>
                    </div>
                    <div class = "info_voiture">

                        <div class="liste_info_voiture">
                            <span><?=htmlspecialchars($voiture['marque'])?></span>
                            <span><?=htmlspecialchars($voiture['modele'])?></span>
                        </div>
                        <div class="liste_info_voiture">
                            <span>Immatriculation</span>
                            <span><?=htmlspecialchars($voiture['immat'])?></span>
                        </div>
                        <div class="liste_info_voiture">
                            <span>Couleur</span>
                            <span><?=htmlspecialchars($voiture['couleur'])?></span>
                        </div>
                        <div class="liste_info_voiture">
                            <span>Place disponible</span>
                            <span><?=htmlspecialchars($voiture['nb_place'])?></span>
                        </div>
                    </div>
                </div>

            <?php endforeach;?>
            
            <a href="ajouter_voiture.php">Ajouter une nouvelle voiture</a>

        </div>
        <p></p>
        <p></p>
        <div class="covoiturage_conducteur_en_cours">
            <h2>Mes Covoiturage en cours</h2>
            <?php if(empty($info_utilisateur['info_covoiturage_c'])):?>
                <span>Aucun covoiturage créé</span>
            <?php else:?>
            <?php foreach ($info_utilisateur['info_covoiturage_c'] as $covoiturage) : ?>
                <?php if($covoiturage['statut_covoit'] === 'terminer' || $covoiturage['statut_covoit'] === 'annuler'){continue;}?>    
                <div class="carte_covoiturage">
                    <div class="date_covoit">
                        <span>Le <?= date('d/m/Y',strtotime($covoiturage['date_depart']))?></span>
                    </div>
                    <div class="duree_voyage">
                        <div class="depart_arrive">
                            <span><?=htmlspecialchars($covoiturage['lieu_depart'])?></span>
                            <span><?=substr($covoiturage['heure_depart'], 0, 5) ?></span>
                        </div>
                        <div class="stick">
                            <span>----</span>
                            <span></span>
                        </div>
                        <div class="depart_arrive">
                            <span><?=substr($covoiturage['duree_voyage'], 0, 5) ?></span>
                            <img class="icone" src="assets/icons/icon_car_profil.png" alt="icone voiture">
                        </div>
                        <div class="stick">
                            <span>----</span>
                            <span></span>
                        </div>
                        <div class="depart_arrive">
                            <span><?=htmlspecialchars($covoiturage['lieu_arrive'])?></span>
                            <span><?= date('H:i', strtotime($covoiturage['heure_depart']) + strtotime($covoiturage['duree_voyage']) - strtotime('00:00:00')) ?></span>

                        </div>

                    </div>
                    <div class="carte_voiture_droit">
                        <span><?=htmlspecialchars($covoiturage['nb_place_dispo'])?> place disponible<?php echo ($covoiturage['nb_place_dispo']>1)? 's':''; ?></span>
                        <?php if($covoiturage['statut_covoit'] === 'planifier'):?>
                            <form method="POST">
                                <input type="hidden" name="type_POST" value="demarrer_covoiturage">
                                <input type="hidden" name="id_covoiturage" value="<?=$covoiturage['id_covoiturage']?>">
                                <button type="submit">Démarrer</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="type_POST" value="supprimer_covoiturage">
                                <input type="hidden" name="id_covoiturage" value="<?=$covoiturage['id_covoiturage']?>">
                                <button type="submit">Annuler</button>
                            </form>
                        <?php elseif($covoiturage['statut_covoit'] === 'en_cours'):?>
                            <form method="POST">
                                <input type="hidden" name="type_POST" value="terminer_covoiturage">
                                <input type="hidden" name="id_covoiturage" value="<?=$covoiturage['id_covoiturage']?>">
                                <button type="submit">Terminer</button>
                            </form>
                        <?php endif;?>
                    </div>
                </div>
            <?php endforeach;?>
            <?php endif;?>
            <a href="ajouter_covoiturage.php">Ajoutez un covoiturage</a>
        </div>
        <p></p>
        <!--fin de la partie cache si pas conducteur-->
        <?php endif;?>
        <p></p>
        <p></p>
        <?php if($info_utilisateur['info_utilisateur']['type_utilisateur'] !== 'Conducteur'):?>
        <div class="covoiturage_passager_en_cours">
            <h2>Mes Participation covoiturage en cours</h2>
            <?php if(empty($covoit_planifier_encours)):?>
                <span>Vous ne participez a aucun covoiturage</span>
            <?php else:?>
            <?php foreach ($covoit_planifier_encours as $covoiturage) : ?>
                <?php if($covoiturage['statut_covoit'] === 'terminer' || $covoiturage['statut_covoit'] === 'annuler'){continue;}?>

                <div class="carte_covoiturage">
                    <div class="date_covoit">
                        <span>Le <?= date('d/m/Y',strtotime($covoiturage['date_depart']))?></span>
                    </div>
                    <div class="duree_voyage">
                        <div class="depart_arrive">
                            <span><?=htmlspecialchars($covoiturage['lieu_depart'])?></span>
                            <span><?=substr($covoiturage['heure_depart'], 0, 5) ?></span>
                        </div>
                        <div class="stick">
                            <span>----</span>
                            <span></span>
                        </div>
                        <div class="depart_arrive">
                            <span><?=substr($covoiturage['duree_voyage'], 0, 5) ?></span>
                            <img class="icone" src="assets/icons/icon_car_profil.png" alt="icone voiture">
                        </div>
                        <div class="stick">
                            <span>----</span>
                            <span></span>
                        </div>
                        <div class="depart_arrive">
                            <span><?=htmlspecialchars($covoiturage['lieu_arrive'])?></span>
                            <span><?= date('H:i', strtotime($covoiturage['heure_depart']) + strtotime($covoiturage['duree_voyage']) - strtotime('00:00:00')) ?></span>

                        </div>

                    </div>
                    <div class="carte_voiture_droit">
                        <span><?=htmlspecialchars($covoiturage['nb_place_reserve'])?> place<?php echo ($covoiturage['nb_place_reserve']>1)? 's':''; ?> reservée<?php echo ($covoiturage['nb_place_reserve']>1)? 's':''; ?></span>
                        <?php if($covoiturage['statut_covoit'] === 'planifier'):?>
                            <form method="POST">
                                <input type="hidden" name="type_POST" value="annuler_covoiturage">
                                <input type="hidden" name="id_covoiturage" value="<?=$covoiturage['id_covoiturage']?>">
                                <button type="submit">Annuler</button>
                            </form>
                        <?php elseif($covoiturage['statut_covoit'] === 'en_cours'):?>
                            <span> Covoiturage en cours</span>
                        <?php endif;?>
                    </div>
                </div>
            <?php endforeach;?>
            <?php endif;?>
        </div>
        <?php endif;?>
        <p></p>
        <p></p>                    
        <div class="div_historique">
            <!--div historique pour passager et passager conducteur-->
            <?php if($info_utilisateur['info_utilisateur']['type_utilisateur'] !== 'Conducteur'):?>
                <div class="historique_passager">
                    <h2>Mon Historique de participation au covoiturage</h2>
                    <?php if(empty($info_utilisateur['info_historique_p'])):?>
                        <span>Vous n'avez participé a aucun covoiturage</span>
                    <?php else:?>
                        <?php foreach($info_utilisateur['info_historique_p'] as $h_covoit_p):?>

                            <div class="carte_covoiturage">
                                <div class="date_covoit">
                                    <span>Le <?= date('d/m/Y',strtotime($h_covoit_p['date_depart']))?></span>
                                </div>
                                <div class="duree_voyage">
                                    <div class="depart_arrive">
                                        <span><?=htmlspecialchars($h_covoit_p['lieu_depart'])?></span>
                                        <span><?=substr($h_covoit_p['heure_depart'], 0, 5) ?></span>
                                    </div>
                                    <div class="stick">
                                        <span>----</span>
                                        <span></span>
                                    </div>
                                    <div class="depart_arrive">
                                        <span><?=substr($h_covoit_p['duree_voyage'], 0, 5) ?></span>
                                        <img class="icone" src="assets/icons/icon_car_profil.png" alt="icone voiture">
                                    </div>
                                    <div class="stick">
                                        <span>----</span>
                                        <span></span>
                                    </div>
                                    <div class="depart_arrive">
                                        <span><?=htmlspecialchars($h_covoit_p['lieu_arrive'])?></span>
                                        <span><?= date('H:i', strtotime($h_covoit_p['heure_depart']) + strtotime($h_covoit_p['duree_voyage']) - strtotime('00:00:00')) ?></span>

                                    </div>

                                </div>
                                <div class="carte_voiture_droit">
                                    <span><?=htmlspecialchars($h_covoit_p['nb_place_reserve'])?> passager<?php echo ($h_covoit_p['nb_place_reserve']>1)? 's':''; ?></span>
                                    <span><?=htmlspecialchars($h_covoit_p['statut_covoit'])?></span>
                                    <?php if($h_covoit_p['statut_covoit'] === 'terminer'):?>
                                        <a href="donner_avis.php?id_c=<?=$h_covoit_p['id_covoiturage']?>">Donner un avis</a>
                                    <?php endif;?>
                                    
                                </div>
                            </div>

                        <?php endforeach;?>
                    <?php endif;?>
                </div>
            <?php endif?>

            <!--div historique pour conducteur et passager conducteur-->
            <?php if($info_utilisateur['info_utilisateur']['type_utilisateur'] !== 'Passager'):?>
                <div class="historique_conducteur">
                    <h2>Mon Historique de covoiturage</h2>
                    <?php if(empty($info_utilisateur['info_historique_c'])):?>
                        <span>Vous n'avez terminé aucun covoiturage</span>
                    <?php else:?>
                        <?php foreach($info_utilisateur['info_historique_c'] as $h_covoit_c):?>

                            <div class="carte_covoiturage">
                                <div class="date_covoit">
                                    <span>Le <?= date('d/m/Y',strtotime($h_covoit_c['date_depart']))?></span>
                                </div>
                                <div class="duree_voyage">
                                    <div class="depart_arrive">
                                        <span><?=htmlspecialchars($h_covoit_c['lieu_depart'])?></span>
                                        <span><?=substr($h_covoit_c['heure_depart'], 0, 5) ?></span>
                                    </div>
                                    <div class="stick">
                                        <span>----</span>
                                        <span></span>
                                    </div>
                                    <div class="depart_arrive">
                                        <span><?=substr($h_covoit_c['duree_voyage'], 0, 5) ?></span>
                                        <img class="icone" src="assets/icons/icon_car_profil.png" alt="icone voiture">
                                    </div>
                                    <div class="stick">
                                        <span>----</span>
                                        <span></span>
                                    </div>
                                    <div class="depart_arrive">
                                        <span><?=htmlspecialchars($h_covoit_c['lieu_arrive'])?></span>
                                        <span><?= date('H:i', strtotime($h_covoit_c['heure_depart']) + strtotime($h_covoit_c['duree_voyage']) - strtotime('00:00:00')) ?></span>

                                    </div>

                                </div>
                                <div class="carte_voiture_droit">
                                    <span><?=htmlspecialchars($h_covoit_c['nb_place_dispo'])?> passager<?php echo ($h_covoit_c['nb_place_dispo']>1)? 's':''; ?></span>
                                    <span><?=htmlspecialchars($h_covoit_c['statut_covoit'])?></span>
                                    <?php if($h_covoit_c['statut_covoit'] === 'terminer'):?>
                                        <a href="voir_avis.php?id_c=<?=$h_covoit_c['id_covoiturage']?>">Voir les Avis</a>
                                    <?php endif;?>
                                </div>
                            </div>

                        <?php endforeach;?>
                    <?php endif;?>
                </div>
            <?php endif?>
        </div>
        <p></p>
        <p></p>
        <p></p>
        <p></p>

    <?php endif;?>

    <?php include 'includes/footer.php' ?>

</body>
</html>