<?php
require_once('index.php');

class Collection {
private $users = [];
public function __construct() {
        $this->users = [
           $user1= new Editor(1, 'editor', 'editor@blogcms.com', 'editor123', 'senior'),
            $user2 = new Author(2, 'author', 'author@blogcms.com', 'author123', 'Rédacteur passionné'),
        ];
}
private $articles = [];
private $categories = [];
private $current_user = null; // Ajoutez cet attribut
// ... (constructeur et autres méthodes existantes) ...
// MÉTHODE À IMPLÉMENTER :
public function login($username, $password) {
// 1. Parcourir le tableau $this->users
// 2. Pour chaque utilisateur, vérifier si:
// - Le username correspond
// - Le password correspond (utiliser password_verify)
// 3. Si trouvé, définir $this->current_user = $user
// 4. Retourner true si connexion réussie, false sinon

    foreach ($this->users as $user) {
        if($user->checklogin($username, $password)){
            $this->current_user=$user;
            return true;
        };
        
    }
    return false;
    

}
public function logout() {
// Définir $this->current_user = null
$this->current_user=Null;
}
public function getCurrentUser() {
// Retourner l'utilisateur connecté (ou null)
    return $this->current_user;
}
public function isLoggedIn() {
// Retourner true si un utilisateur est connecté, false sinon
if ($this->current_user) {
    return true;
}return false;
}
// AUTRE MÉTHODE UTILE :
public function displayAllArticles() {
// Afficher tous les articles du tableau $articles
// Format: "1. [Titre] par [Auteur]"
// Note: Pour l'instant, les articles n'ont pas d'auteur
// On affichera juste le titre
}
}
// TEST FINAL
$collection = new Collection();
// createTestUsers($collection);
// Test 1: Connexion réussie
$result = $collection->login('author', 'author123');
echo $result ? "Connexion alice OK" : "Échec connexion alice";
// Test 3: Vérification état connexion
if ($collection->isLoggedIn()) {
$user = $collection->getCurrentUser();
echo "Utilisateur connecté: " . $user->getusername();
}
// Test 4: Déconnexion
$collection->logout();
echo !$collection->isLoggedIn() ? "Déconnexion OK" : "Problème déconnexion"
?>