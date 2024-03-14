
<?php

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';

$bdd = connectBdd('root', 'root', 'blog_db');

// Utilisation de la bibliotheque Faker
$faker = Faker\Factory::create();

// Preparation de la requete d'insertion d'utilisateur
$insertUser = $bdd->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");

// Generer 3 utilisateurs
for ($i = 0; $i < 3; $i++) {
    $insertUser->bindValue(':name', $faker->name);
    $insertUser->bindValue(':email', $faker->unique()->email);
    $insertUser->bindValue(':password', password_hash('secret', PASSWORD_DEFAULT));
    $insertUser->execute();
}