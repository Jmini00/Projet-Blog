
<?php

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';

$bdd = connectBdd('root', 'root', 'blog_db');

// Utilisation de la bibliotheque Faker
$faker = Faker\Factory::create();

// Preparation de la requete d'insertion de catégorie
$insertUser = $bdd->prepare("INSERT INTO categories (name) VALUES (:name)");

// Generer 10 categories
for ($i = 0; $i < 10; $i++) {
    $insertUser->bindValue(':name', $faker->word);
    $insertUser->execute();
}