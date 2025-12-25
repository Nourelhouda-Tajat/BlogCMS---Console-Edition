<?php 
class User {
    protected int $idUser;
    protected string $username;
    protected string $email;
    protected string $password;
    protected string $role;
    protected DateTime $createdAt;
    protected ?DateTime $lastLogin;

    public function __construct($idUser, $username, $email, $password, $role){
        $this->idUser = $idUser;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = new DateTime('today');
        $this->lastLogin = new DateTime();
    }
    public function afficherInfos() {
        return "Utilisateur: $this->username ($this->email)";
    }
    public function estAuteur() {
        if ($this->role==='Auteur') {
            return true;
        }
        else {return false;}
    }
    

}


class Article {
    private string $title;
    private string $content;
    private string $status; // 'draft', 'published', 'archived'
   

    public function __construct($title, $content){
        $this->title = $title;
        $this->content = $content;
        $this->status = 'draft';
    }

    public function afficheArticle(){
        return "Tilte article : $this->title, content article : $this->content, status article : $this->status \n";
    }

    public function publier(){
        $this->status = "published";
        return "Tilte article : $this->title, content article : $this->content, status article : $this->status ";
    }
}


// $user1 = new User(1, "nour_0001", "nour@gmail.com", "password", "admin");
// print_r($user1->afficherInfos());

// $userAuteur = new User(1,"lea", "lea@blog.com", "pass","Auteur");
// $userVisiteur = new User(2,"visiteur", "v@blog.com", "pass","visiteur");

// echo "Lea peut-elle créer un article ? " . ($userAuteur->estAuteur() ? "OUI" : "NON") . "\n";
// echo "Le visiteur peut-il créer un article ? " . ($userVisiteur->estAuteur() ? "OUI" : "NON") . "\n";

// $article = new Article("First article", "content first article");

// print_r($article->afficheArticle());

// print_r($article->publier());

function firstOption(){
    echo 'first option';
}
function secondeOption(){
    echo 'seconde option';
}
function closeConsol(){
    echo 'Goodbye';
    die();
}
while (true) {
    // Affichage du menu
    echo "\n--- Menu Principal ---\n";
    echo "1. Afficher l'Option 1\n";
    echo "2. Afficher l'Option 2\n";
    echo "Q. Quitter\n";
    echo "----------------------\n";

    // Lecture de l'entrée utilisateur
    echo "Entrez votre choix : ";
    $choix = trim(fgets(STDIN)); // STDIN pour l'entrée console, trim() pour nettoyer

    // Traitement du choix
    switch ($choix) {
        case '1':
            firstOption();
            break;
        case '2':
            secondeOption();
            break;
        case '3':
            closeConsol();
            break;
        default:
            echo "Choix invalide. Veuillez réessayer.\n";
    }
}


?>




// Fichier: Menu.php
class Menu {
    private $options = [];
    private $fonctionnalites = [];

    public function ajouterOption($cle, $libelle, $fonctionnalite) {
        $this->options[$cle] = $libelle;
        $this->fonctionnalites[$cle] = $fonctionnalite; // Stocke l'objet fonctionnalité
    }

    public function afficher() {
        echo "\n--- MENU ---" . "\n";
        foreach ($this->options as $cle => $libelle) {
            echo "[$cle] $libelle\n";
        }
        echo "[Q] Quitter\n";
        echo "------------" . "\n";
    }

    public function getChoix() {
        return trim(readline("Votre choix : "));
    }

    public function executerChoix($choix) {
        if (isset($this->fonctionnalites[$choix])) {
            // On appelle la méthode executer() de l'objet fonctionnalité stocké
            $this->fonctionnalites[$choix]->executer();
        } elseif (strtoupper($choix) === 'Q') {
            echo "Au revoir !\n";
            return false; // Indique qu'il faut quitter
        } else {
            echo "Choix invalide. Veuillez réessayer.\n";
        }
        return true; // Indique qu'il faut continuer
    }
}

        // Fichier: Application.php
require_once 'Menu.php';
require_once 'Fonctionnalites/Bonjour.php';
require_once 'Fonctionnalites/AfficherDate.php';

class Application {
    private $menu;

    public function __construct() {
        $this->menu = new Menu();
        // Instancier les objets fonctionnalités
        $bonjour = new Bonjour();
        $afficherDate = new AfficherDate();
        
        // Ajouter les options au menu
        $this->menu->ajouterOption('1', 'Dire Bonjour', $bonjour);
        $this->menu->ajouterOption('2', 'Afficher la date', $afficherDate);
    }

    public function demarrer() {
        $continuer = true;
        while ($continuer) {
            $this->menu->afficher();
            $choix = $this->menu->getChoix();
            $continuer = $this->menu->executerChoix($choix);
        }
    }
}

// Point d'entrée du script
$app = new Application();
$app->demarrer();
