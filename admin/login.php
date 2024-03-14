
<?php

// Demarrage de la session
// Doit etre placé au plus haut possible dans le code
session_start();

/** login.php
 * permet de verifier si un utilisateur
 * peut acceder à l'administration
 */

 /**
  * Logique :
  * 1 verifier si le formulaire est complet -> sinon erreur
  * 2 nettoyer les donnees issues du formulaire
  * 3 selectionner l'utilisateur en BDD via son email -> sinon erreur
  * 4 verifier si mot de passe du formulaire correspond à celui en BDD
  * 5 rediriger l'utilisateur vers la page "dashboard.php
  */


  require_once '../connexion.php';
  // Verifier si le formulaire est complet

  $error = null;

  if (!empty($_POST['email']) && !empty($_POST['password'])) {

    // Nettoyer les donnees issues du formulaire
    $email = htmlspecialchars(strip_tags($_POST['email']));
    $password = htmlspecialchars(strip_tags($_POST['password']));

    // Selectionner l'utilisateur en BDD via son email
    $bdd = connectBdd('root', 'root', 'blog_db');
    $query = $bdd->prepare("SELECT * FROM users WHERE email = :email");
    $query->bindValue(':email', $email);
    $query->execute();

    /**  
     * fetch() retourne un tableau associatif contenant soit :
     * - les infos d'un utilisateur
     * - false
     */
    $user = $query->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Stocker les infos de l'utilisateur en session
        $_SESSION['user'] = $user;
        // Verifier si le mot de passe du formulaire correspond à celui en BDD
        // redirection vers le fichier dashboard.php
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Identifiants invalides';
    }

} else {
    $error = 'Tous les champs sont obligatoires';
}

// Gestion des erreurs
if($error !== null) {
    $_SESSION['error'] = $error;
    // Declaration d'une session contenant l'erreur
        header('Location: index.php');
        exit;
}