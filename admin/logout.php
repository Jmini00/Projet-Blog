
<?php

/** logout.php
 * Deconnexion de l'utilisateur
 */

 // Demarrage de la session
 session_start();

 // Destruction de la session "user"
 unset($_SESSION['user']);

 // Redirection vers le formulaire de connexion
 header('Location: index.php');