
<?php

/**
 * update_article.php
 * Mise à jour d'un article en BDD
 */

 /**
  * 1 - Seule une personne connectee peut y acceder
  * 2 - Verifier si methode du formulaire recue est bien "POST" avec "$_SERVER"
  * 3 - Connexion à la base de donnees
  * 4 - Recuperer et nettoyer les donnees
  * 5 - Mise à jour du titre et contenu de l'article dans la table "articles"
  * 6 - Redirection vers le formulaire d'edition avec un message de succes
  */



// Démarrer une session
session_start();

// Chargement dependances PHP
require_once '../vendor/autoload.php';

// Vérifie si l'utilisateur peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Verifier si methode du formulaire recue est bien "POST" avec "$_SERVER"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Connexion à la base de données
    require_once '../connexion.php';
    $bdd = connectBdd('root', 'root', 'blog_db');

    // Recuperer et nettoyer les donnees
    $id = $_GET['id'];
    $title = htmlspecialchars(strip_tags($_POST['title']));
    $content = htmlspecialchars(strip_tags($_POST['content']));
    
    // Applique une fonction sur les valeurs d'un tableau
    $categories = array_map('strip_tags', ($_POST['content']));
    // $error = null;

    // Verifier si les champs sont completes
    if (!empty($title) && !empty($content)) {

    // Selectionner le nom de l'image actuellement en BDD
    $selectCoverQuery = $bdd->prepare("SELECT cover FROM articles WHERE id = :id");
    $selectCoverQuery->bindValue(':id', $id);
    $selectCoverQuery->execute();

    // Recuperation de la valeur de la colonne et stockage de l'info dans une variable
    $cover = $selectCoverQuery->fetchColumn();
    
    // Verifie si un upload doit etre fait
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {

        $typeExt = [
            'png' => 'image/png',
            'jpg' => 'image/jpg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
        ];

        $sizeMax = 1 * 1024 * 1024;
        $extension = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));

        // Verifier si le fichier est bien une image autorisee
        if(array_key_exists($extension, $typeExt) && in_array($_FILES['cover']['type'], $typeExt)) {

            // Verifier si le poids de l'image ne depasse pas la limite fixee
            if ($_FILES['cover']['size'] <= $sizeMax) {

                // Supprime l'ancienne image
                if (file_exists("../public/uploads/$cover")) {
                    // Supprime l'image à l'endroit indiqué
                    unlink("../public/uploads/$cover");
                }

                // Renomme le nom de l'image
                $slugify = new \Cocur\Slugify\Slugify(); 
                $newName = $slugify->slugify("$title_$id");
                $cover = "$newName.$extension";

                // Telecharge la nouvelle image sous le nouveau nom
                move_uploaded_file(
                    $_FILES['cover']['tmp_name'], "../public/uploads/$cover"
                );
            } else {
                $_SESSION['error'] = "L'image ne doit pas depasser les 1Mo";
                header("Location: edit.php?id=$id");
            }

        } else {
            $_SESSION['error'] = "Le fichier n'est pas une image conforme";
            header("Location: edit.php?id=$id");
            exit;
        }
    }


    // Mise à jour du titre et contenu de l'article dans la table "articles"
    $query = $bdd->prepare("
        UPDATE articles SET title = :title, content = :content, cover = :cover WHERE id = :id");
    $query->bindValue(':title', $title);
    $query->bindValue(':content', $content);
    $query->bindValue(':cover', $cover);
    $query->bindValue(':id', $id);
    $query->execute();

    // Mise à jour des categories liees  à l'article
        $deleteQuery = $bdd->prepare("
        DELETE FROM articles_categories WHERE article_id = :id");
        $deleteQuery->bindValue(':id', $id);
        $deleteQuery->execute();

        $insertCategoryQuery = $bdd->prepare("
        INSERT INTO articles_categories (article_id, category_id) 
        VALUES (:article_id, :category_id)
        ");

        foreach ($categories as $category) {
            $insertCategoryQuery->bindValue(':article_id', $id);
            $insertCategoryQuery->bindValue(':category_id', $category);
            $insertCategoryQuery->execute();
        }

    // Message de succes
    $_SESSION['success'] = 'Les modifications ont bien été prises en compte';

    } else {
        $_SESSION['error'] = 'Le titre et le contenu sont obligatoires';
    }

    // Redirection vers le formulaire d'edition
    header("Location: edit.php?id=$id");
    exit;

} else {
    header('Location: dashboard.php');
    exit;
}


