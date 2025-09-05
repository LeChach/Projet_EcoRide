<?php
/** Formate lechemin absolue poru chaque image
 * @param string le nom de la preference a trouver le chemin de son icone
 * @return le nom du chemin de l'icone
 */
function cheminImgPreference(string $preference) : string {
    switch ($preference){
        case ('etre_fumeur'):
            $chemin_pref = 'assets/icons/cigarette.png';
            break;
        case ('avoir_animal'):
            $chemin_pref = 'assets/icons/pattes.png';
            break;
        case ('avec_silence'):        
            $chemin_pref = 'assets/icons/silence.png';
            break;
        case ('avec_musique'):
            $chemin_pref = 'assets/icons/note-de-musique.png';
            break;
        case ('avec_climatisation'):
            $chemin_pref = 'assets/icons/climatisation.png';
            break;
        case ('avec_velo'): 
            $chemin_pref = 'assets/icons/bicyclette.png';
            break;
        case ('place_coffre'):
            $chemin_pref = 'assets/icons/bonhomme-allumette.png';
            break;
        case ('ladies_only'):
            $chemin_pref = 'assets/icons/femme.png';
            break;
    }
    return $chemin_pref;
}
?>