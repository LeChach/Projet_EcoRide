<?php
/** Formate les noms de villes pour la recherche et linsertion
 * @param string le nom de la ville a formater
 * @return le nom de la ville formater
 */
function formaterVille(string $ville) : string {

    $new_ville = mb_strtolower($ville,'UTF-8'); //tout en minscule
    $new_ville = iconv('UTF-8','ASCII//TRANSLIT',$new_ville); //aucun accent
    $new_ville = str_replace(' ','-',$new_ville); // tiret au lieu des espaces
    $new_ville = preg_replace('/\s+/',' ',trim($new_ville)); //enleve les espaces multiples
    $new_ville = mb_convert_case($new_ville,MB_CASE_TITLE,'UTF-8'); //mettre chaque 1erL en maj
    return $new_ville;
}
?>