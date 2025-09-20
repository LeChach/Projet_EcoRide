<?php
/**
 * fonction qui permet de remplacer iconv()
 */
function transliterate($str) {
    $transliterationTable = [
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c', 'ñ' => 'n'
    ];
    return strtr($str, $transliterationTable);
}



/** Formate les noms de villes pour la recherche et linsertion
 * @param string le nom de la ville a formater
 * @return string le nom de la ville formater
 */
function formaterVille(string $ville) : string {

    $new_ville = mb_strtolower($ville,'UTF-8');
    $new_ville = transliterate($new_ville);
    $new_ville = str_replace(' ','-',$new_ville);
    $new_ville = preg_replace('/\s+/',' ',trim($new_ville));
    $new_ville = mb_convert_case($new_ville,MB_CASE_TITLE,'UTF-8');
    return $new_ville;
}

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

