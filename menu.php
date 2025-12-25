<?php
/**
* TÂCHE 2 - DYNAMIC_MENU.php
* Menu qui change si l'utilisateur est connecté ou non
*/

require_once ('colletions.php');

echo "=== BLOGCMS CONSOLE AVEC AUTHENTIFICATION ===\n";

$db = new Collection();
// createTestUsers($db); // Ajoute les utilisateurs de test
$running = true;

while ($running) {
// AFFICHAGE DE L'EN-TÊTE AVEC ÉTAT DE CONNEXION
if ($db->isLoggedIn()) {
$user = $db->getCurrentUser();
echo "\n--- Connecté en tant que: {$user->getusername()} ({$user->role}) ---\n";
} else {
echo "\n--- MENU VISITEUR (non connecté) ---\n";
}
// MENU DYNAMIQUE - CHANGE SELON L'ÉTAT DE CONNEXION
if (!$db->isLoggedIn()) {
// Menu visiteur (non connecté)
echo "1. Voir tous les articles\n";
echo "2. Se connecter\n";
echo "0. Quitter\n";
} else {
// Menu utilisateur connecté
echo "1. Voir tous les articles\n";
echo "2. Créer un nouvel article\n";
echo "3. Voir mes informations\n";
echo "4. Se déconnecter\n";
echo "0. Quitter\n";
}
$choice = readline("Votre choix : ");
// TRAITEMENT DES CHOIX
if (!$db->isLoggedIn()) {
// Traitement menu visiteur
switch ($choice) {
case '1': // Voir tous les articles
// TODO: Appeler une méthode pour afficher tous les articles
break;
case '2': // Se connecter
$username = readline("Username : ");
$password = readline("Password : ");
if ($db->login($username, $password)) {
echo "Connexion réussie !\n";
} else {
echo "Échec de connexion\n";
}
break;
case '0':
$running = false;
echo "Au revoir !\n";
break;
default:
echo "Choix invalide\n";
}
} else {
// Traitement menu utilisateur connecté
switch ($choice) {
case '1': // Voir tous les articles
// TODO: Afficher tous les articles
break;
case '2': // Créer un nouvel article
// TODO: Demander titre et contenu, puis créer l'article
// Utiliser le current_user comme auteur
break;
case '3': // Voir mes infos
$user = $db->getCurrentUser();
echo "👤 Username: {$user->username}\n";
echo "🎭 Rôle: {$user->role}\n";
break;
case '4': // Se déconnecter
$db->logout();
echo "Déconnexion réussie\n";
break;
case '0':
$running = false;
echo "Au revoir !\n";
break;
default:
echo "Choix invalide\n";
}
}
}?>