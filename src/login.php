<?php
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pseudo = htmlspecialchars($_POST['pseudo'], ENT_QUOTES, 'UTF-8'); //converti automatiquement en full caractere
    $mot_de_passe = $_POST['password']; //converti automatiquement en full caractere

    //CONNEXION BBD
    $dsn = "mysql:host=localhost;dbname=bdd_eco-ride;charset=utf8mb4";  //info de connexion
    $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    try {
        $pdo = new PDO($dsn, 'root' , '', $options);

        //PREPARATION DE LA REQUETE
        $pdo_prep = $pdo->prepare("SELECT * from utilisateur WHERE pseudo = ? AND mot_de_passe = ? "); //prepare l'info a mettre a la place de ?
        $pdo_prep->execute([$pseudo]); //execute en remplacent ? par $pseudo
        $user = $pdo_prep->fetch();

        if($user){

            //verification seulement ici du mdp si = mdp hache sur la bdd
            if(password_verify($mot_de_passe,$user['mot_de_passe'])){
                $_SESSION['user_id'] = $user['id_utilisateur'];
                header("Location: mon_compte.php");
                exit;
            } else {
                $erreur = "pseudo ou mot de passe incorrect";
            }
        } else {
            $erreur = "pseudo ou mot de passe incorrect";
        }

    } catch (PDOException $e) {
        die("Erreur BDD : " . $e->getMessage());
    }

}

?>