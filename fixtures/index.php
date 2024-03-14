
<?php

/**
 * index.php
 * Generer toutes les fixtures
 * 
 * Liste des fichiers afin de generer les jeux de donnees d'essai
 * dans l'ordre d'insertion en BDD
 */


 /**
  * http://projet_blog.test/fixtures/index.php?truncate=1
  * Si le parametre "truncate" est present dans l'url, on vide nos tables SQL
  */
if (isset($_GET['truncate'])) {
    // Connexion à la base de données
    require '../connexion.php';
    $bdd = connectBdd('root', 'root', 'blog_db');


/**  Requetes pour vider les tables SQL
 * Attention l'ordre est TRES important afin de ne pas avoir d'erreurs 
 * sur cles etrangeres reliees sur d'autres tables SQL
 * 
 * SET FOREIGN_KEY_CHECKS : permet d'activer/desactiver 
 * la verif des contraintes des cles etrangeres
*/
$bdd->query("
    SET FOREIGN_KEY_CHECKS = 0;
    TRUNCATE articles_categories;
    TRUNCATE comments;
    TRUNCATE articles;
    TRUNCATE categories;
    TRUNCATE users;

    ");
}

 require_once 'users_fixtures.php';
 require_once 'categories_fixtures.php';
 require_once 'articles_fixtures.php';
 require_once 'comments_fixtures.php';
 require_once 'articles_categories_fixtures.php';