


<?php

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';

$bdd = connectBdd('root', 'root', 'blog_db');

// Utilisation de la bibliotheque Faker
$faker = Faker\Factory::create();

// Preparation de la requete d'insertion de comments
$insertUser = $bdd->prepare("INSERT INTO comments (content, comment_date, user_id, article_id) VALUES (:content, :comment_date, :user_id, :article_id)");

// Selectionne tous les utilisateurs
$query = $bdd->query("SELECT id FROM users");
$users = $query->fetchAll();

// Selectionne tous les articles
$query = $bdd->query("SELECT id, publication_date FROM articles");
$articles = $query->fetchAll();

// Generer 200 comments
for ($i = 0; $i < 200; $i++) {

    // Selectionne un utilisateur aleatoirement
    $user = $faker->randomElement($users);

    // Selectionne un article aleatoirement
    $article = $faker->randomElement($articles);

    // Genere une date entre date creation de l'article et aujourdhui
    $date = $faker->dateTimeBetween($article['publication_date'])->format('Y-m-d H:i:s');

    $insertUser->bindValue(':content', $faker->text);
    $insertUser->bindValue(':comment_date', $date);
    $insertUser->bindValue(':user_id', $user['id']);
    $insertUser->bindValue(':article_id', $article['id']);
    $insertUser->execute();
}