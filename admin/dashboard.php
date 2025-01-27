<?php

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', 'root', 'blog_db');

// Sélectionne tous les articles avec leurs catégories
$query = $bdd->prepare("
    SELECT 
        articles.id, articles.title, articles.publication_date, 
        GROUP_CONCAT(categories.name, ', ') AS categories 
    FROM articles 
    LEFT JOIN articles_categories ON articles_categories.article_id = articles.id 
    LEFT JOIN categories ON categories.id = articles_categories.category_id 
    WHERE user_id = :id
    GROUP BY articles.id
    ORDER BY articles.publication_date DESC
");
$query->bindValue(':id', $_SESSION['user']['id']);
$query->execute();

$articles = $query->fetchAll();

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Administration</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </head>
    <body>
        <nav class="navbar bg-primary" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="dashboard.php">Administration</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php">Déconnexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-4">Liste des articles</h2>
            <a href="add.php" class="btn btn-success">Nouvel article</a>
        </div>

        <!-- Message de succès -->
        <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Catégories</th>
                        <th>Date de publication</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($articles as $article): ?>
                        <tr>
                            <td><?php echo $article['id']; ?></td>
                            <td><?php echo $article['title']; ?></td>
                            <td><?php echo $article['categories']; ?></td>
                            <td>
                                <?php
                                    $date = DateTime::createFromFormat('Y-m-d H:i:s', $article['publication_date']);
                                    echo $date->format('d.m.Y');
                                ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $article['id']; ?>" class="btn btn-light btn-sm">Editer</a>
                                <a
                                    href="delete_article.php?id=<?php echo $article['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');"
                                >  Supprimer </a>                          
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>