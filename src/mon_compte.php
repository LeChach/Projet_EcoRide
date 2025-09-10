<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'classes/MonCompte.php';
require_once 'classes/Connexion.php';
require_once 'function_php/fonction.php';


$info_utilisateur = MonCompte::recupDonnee($pdo,$id_utilisateur);
if(!$info_utilisateur['success']){
    $_SESSION['erreur_connexion'] = $info_utilisateur['message'];
    header('location: connexion.php');
    exit;
}

//gestion des differents POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch ($_POST['type_POST']){

        case 'MAJ_type_utilisateur':
            $message = MonCompte::changerTypeUtilisateur($pdo,$id_utilisateur,$_POST['type_u']);
            if(!$message['success']){
                $_SESSION['erreur'] = $message['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'MAJ_preferences':
            $message = MonCompte::changerPréférence($pdo,$id_utilisateur,$_POST);
            if(!$message['success']){
                $_SESSION['erreur'] = $message['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'supprimer_voiture':
            $message = MonCompte::supprimerVoiture($pdo,$id_utilisateur,$_POST['id_voiture']);    
            if(!$message['success']){
                $_SESSION['erreur'] = $message['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'ajouter_voiture':
            $message = MonCompte::ajouterVoiture($pdo,$id_utilisateur,$_POST);    
            if(!$message['success']){
                $_SESSION['erreur'] = $message['message'];
            }
            header('Location: mon_compte.php');
            exit;

        case 'demarrer_covoiturage':
        case 'terminer_covoiturage':
        case 'annuler_covoiturage':

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
        <!--Partie invisible si n'est pas minimum conducteur-->
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
            <h2>MA VOITURES</h2>
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
        <div class="covoiturage_conducteur">
            <h2>Covoiturage en cours</h2>

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
                        <span><?=htmlspecialchars($covoiturage['nb_place_dispo'])?> passager<?php echo ($covoiturage['nb_place_dispo']>1)? 's':''; ?></span>
                        <?php if($covoiturage['statut_covoit'] === 'planifier'):?>
                            <form method="POST">
                                <input type="hidden" name="type_POST" value="demarrer_covoiturage">
                                <input type="hidden" name="id_covoiturage" value="<?=$covoiturage['id_covoiturage']?>">
                                <button type="submit">Démarrer</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="type_POST" value="annuler_covoiturage">
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

            <a href="ajouter_covoiturage.php">Ajoutez un covoiturage</a>
        </div>
        <?php endif;?>
        <p></p>
        <p></p>
        <div class="covoiturage_passager">

        </div>

 
        </div>     
        <p></p>
        <p></p>
        <p></p>
        <p></p>

    <?php endif;?>

    <?php include 'includes/footer.php' ?>

</body>

</html>