

<?php

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';

$bdd = connectBdd('root', 'root', 'blog_db');

// Utilisation de la bibliotheque Faker
$faker = Faker\Factory::create();

// Preparation de la requete d'insertion d'articles
$insertUser = $bdd->prepare("INSERT INTO articles (title, content, cover, publication_date, user_id) VALUES (:title, :content, :cover, :publication_date, :user_id)");

// Selectionne tous les utilisateurs
$query = $bdd->query("SELECT id FROM users");
$users = $query->fetchAll();

// Generer 50 articles
for ($i = 0; $i < 50; $i++) {

    // Selectionne un utilisateur aleatoirement
    $user = $faker->randomElement($users);

    // Genere une date entre, il y a 2 ans et aujourdhui
    $date = $faker->dateTimeBetween('-2 years')->format('Y-m-d H:i:s');

    $insertUser->bindValue(':title', $faker->sentence);
    $insertUser->bindValue(':content', $faker->paragraph(6, true));
    $insertUser->bindValue(':cover', $faker->imageUrl);
    $insertUser->bindValue(':publication_date', $date);
    $insertUser->bindValue(':user_id', $user['id']);
    $insertUser->execute();
}