<?php
require_once('index.php');

class Collection {
    private array $users = [];
    private array $categories = [];
    private ?User $current_user = null;
    
    public function __construct() {
        // Création des catégories
        $cat1 = new Category('Technologie', 'Articles sur la tech');
        $cat2 = new Category('PHP', 'Programmation PHP');
        $cat3 = new Category('Actualités', 'Dernières nouvelles');
        $cat4 = new Category('Design', 'Design et UX/UI');
        $this->categories = [$cat1, $cat2, $cat3, $cat4];
        
        // Création des utilisateurs
        $admin = new Admin('admin', 'admin@blogcms.com', 'admin123', true);
        $editor = new Editor('editor', 'editor@blogcms.com', 'editor123', 'senior');
        
        // Premier auteur avec ses articles
        $author1 = new Author('alice', 'alice@blogcms.com', 'alice123', 'Développeuse PHP passionnée');
        $article1 = $author1->createOwnArticle('Introduction à PHP 8', 'PHP 8 apporte de nombreuses nouveautés comme les attributs...', [$cat2]);
        $article1->publish();
        $article2 = $author1->createOwnArticle('Les frameworks PHP modernes', 'Laravel, Symfony, et d\'autres frameworks...', [$cat2, $cat1]);
        
        // Deuxième auteur avec ses articles
        $author2 = new Author('bob', 'bob@blogcms.com', 'bob123', 'Rédacteur tech et actualités');
        $article3 = $author2->createOwnArticle('IA et Futur de la Tech', 'L\'intelligence artificielle transforme notre monde...', [$cat1, $cat3]);
        $article3->publish();
        $article4 = $author2->createOwnArticle('Tendances Design 2024', 'Le design minimaliste revient en force...', [$cat4]);
        $article4->publish();
        $article5 = $author2->createOwnArticle('Sécurité Web en 2024', 'Les meilleures pratiques de sécurité...', [$cat1, $cat2]);
        $this->users = [$admin, $editor, $author1, $author2];
    }
    
    // Authentification
    public function login($username, $password) {
        foreach ($this->users as $user) {
            if ($user->checklogin($username, $password)) {
                $this->current_user = $user;
                return true;
            }
        }
        return false;
    }
    
    public function logout() {
        $this->current_user = null;
    }
    
    public function getCurrentUser() {
        return $this->current_user;
    }
    
    public function isLoggedIn() {
        return $this->current_user !== null;
    }
    
    //Gestion des articles
    
    public function getAllArticles() {
        $allArticles = [];
        foreach ($this->users as $user) {
            if ($user instanceof Author) {
                $articles = $user->getMyArticles();
                foreach ($articles as $article) {
                    $allArticles[] = $article;
                }
            }
        }
        return $allArticles;
    }
    
    public function getPublishedArticles() {
        $published = [];
        $allArticles = $this->getAllArticles();
        foreach ($allArticles as $article) {
            if ($article->getStatus() === 'published') {
                $published[] = $article;
            }
        }
        return $published;
    }
    
    public function findArticleById($id) {
        $allArticles = $this->getAllArticles();
        foreach ($allArticles as $article) {
            if ($article->getIdArticle() === $id) {
                return $article;
            }
        }
        return null;
    }
    
    public function displayAllArticles() {
        $allArticles = $this->getAllArticles();
        if (empty($allArticles)) {
            echo "Aucun article disponible.\n";
            return;
        }
        
        echo "\n---------- LISTE DES ARTICLES ------------\n";
        foreach ($allArticles as $article) {
            $statusSymbol = $article->getStatus() === 'published' ? 'ok' : 'edit';
            echo "{$statusSymbol} ID: {$article->getIdArticle()} | [{$article->getTitle()}]\n";
            echo "   Auteur: {$article->getAuthor()->getUsername()} | Statut: {$article->getStatus()}\n";
            echo "   Catégories: " . $this->getCategoryNames($article->getCategories()) . "\n";
            echo "   Extrait: {$article->getExcerpt(60)}\n";
            
            $approvedComments = [];
            foreach ($article->getComments() as $comment) {
                if ($comment->isApproved()) {
                    $approvedComments[] = $comment;
                }
            }
            
            if (!empty($approvedComments)) {
                echo "Commentaires approuvés (" . count($approvedComments) . "):\n";
                foreach ($approvedComments as $comment) {
                    $createdAt = $comment->getCreatedAt()->format('Y-m-d H:i');
                    echo "      • [{$comment->getAuthorName()}] {$createdAt}: {$comment->getContent()}\n";
                }
            } else {
                echo "Commentaires: " . count($article->getComments()) . " (aucun approuvé)\n";
            }
            echo "-------------------------------------------\n";
        }
    }
    
    public function displayArticleDetails($id) {
        $article = $this->findArticleById($id);
        if (!$article) {
            echo "Article introuvable.\n";
            return;
        }
        
        $createdAt = $article->getCreatedAt()->format('Y-m-d H:i:s');
        $publishedAt = $article->getPublishedAt() ? $article->getPublishedAt()->format('Y-m-d H:i:s') : 'Non publié';
        
        echo "\n---------------- DÉTAILS ARTICLE #{$id} ---------------\n";
        echo "Titre: {$article->getTitle()}\n";
        echo "Auteur: {$article->getAuthor()->getUsername()}\n";
        echo "Statut: {$article->getStatus()}\n";
        echo "Créé le: {$createdAt}\n";
        echo "Publié le: {$publishedAt}\n";
        echo "Catégories: " . $this->getCategoryNames($article->getCategories()) . "\n";
        echo "\nContenu:\n{$article->getContent()}\n";
        echo "\n--- Commentaires (" . count($article->getComments()) . ") ---\n";
        $this->displayCommentsForArticle($article);
        echo "-------------------------------------------------\n";
    }
    
    public function getArticlesByCategory($categoryId) {
        $category = $this->findCategoryById($categoryId);
        if (!$category) {
            return [];
        }
        
        $result = [];
        $allArticles = $this->getAllArticles();
        foreach ($allArticles as $article) {
            if (in_array($category, $article->getCategories())) {
                $result[] = $article;
            }
        }
        return $result;
    }
    
    public function getArticlesByAuthor($authorId) {
        $result = [];
        $allArticles = $this->getAllArticles();
        foreach ($allArticles as $article) {
            if ($article->getAuthor()->getIdUser() === $authorId) {
                $result[] = $article;
            }
        }
        return $result;
    }
    
    public function deleteArticle($articleId) {
        foreach ($this->users as $user) {
            if ($user instanceof Author) {
                if ($user->deleteOwnArticle($articleId)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    // Gestion des catégorie
    public function addCategory($category) {
        $this->categories[] = $category;
    }
    
    public function getAllCategories() {
        return $this->categories;
    }
    
    public function findCategoryById($id) {
        foreach ($this->categories as $category) {
            if ($category->getIdCategory() === $id) {
                return $category;
            }
        }
        return null;
    }
    
    public function displayAllCategories() {
        if (empty($this->categories)) {
            echo "Aucune catégorie disponible.\n";
            return;
        }
        
        echo "\n----------- LISTE DES CATÉGORIES ---------------\n";
        foreach ($this->categories as $category) {
            echo "ID: {$category->getIdCategory()} | {$category->getName()}\n";
            echo "   Description: {$category->getDescription()}\n";
            echo "-------------------------------------------\n";
        }
    }
    
    public function removeCategoryFromSystem($categoryId) {
        $newCategories = [];
        foreach ($this->categories as $category) {
            if ($category->getIdCategory() !== $categoryId) {
                $newCategories[] = $category;
            }
        }
        if (count($newCategories) < count($this->categories)) {
            $this->categories = $newCategories;
            return true;
        }
        return false;
    }
    
    private function getCategoryNames($categories) {
        $names = [];
        foreach ($categories as $category) {
            $names[] = $category->getName();
        }
        return implode(', ', $names);
    }
    
    // Gestion des comments 
    public function addComment($comment) {
        $comment->getArticle()->addComment($comment);
    }
    
    public function getAllComments() {
        $allComments = [];
        $allArticles = $this->getAllArticles();
        foreach ($allArticles as $article) {
            $comments = $article->getComments();
            foreach ($comments as $comment) {
                $allComments[] = $comment;
            }
        }
        return $allComments;
    }
    
    public function getPendingComments() {
        $pending = [];
        $allComments = $this->getAllComments();
        foreach ($allComments as $comment) {
            if ($comment->getStatus() === 'pending') {
                $pending[] = $comment;
            }
        }
        return $pending;
    }
    
    public function findCommentById($id) {
        $allComments = $this->getAllComments();
        foreach ($allComments as $comment) {
            if ($comment->getIdComment() === $id) {
                return $comment;
            }
        }
        return null;
    }
    
    public function displayCommentsForArticle($article) {
        $comments = $article->getComments();
        if (empty($comments)) {
            echo "Aucun commentaire.\n";
            return;
        }
        
        foreach ($comments as $comment) {
            $statusSymbol = $comment->isApproved() ? 'ok' : 'pending';
            $createdAt = $comment->getCreatedAt()->format('Y-m-d H:i:s');
            echo "{$statusSymbol} [{$comment->getAuthorName()}] le {$createdAt}\n";
            echo "   {$comment->getContent()}\n";
            echo "   Statut: {$comment->getStatus()}\n\n";
        }
    }
    
    public function displayAllComments() {
        $allComments = $this->getAllComments();
        if (empty($allComments)) {
            echo "Aucun commentaire.\n";
            return;
        }
        
        echo "\n-------------- TOUS LES COMMENTAIRES --------------\n";
        foreach ($allComments as $comment) {
            $createdAt = $comment->getCreatedAt()->format('Y-m-d H:i:s');
            echo "ID: {$comment->getIdComment()} | Article: {$comment->getArticle()->getTitle()}\n";
            echo "Auteur: {$comment->getAuthorName()} | Statut: {$comment->getStatus()}\n";
            echo "Date: {$createdAt}\n";
            echo "Contenu: {$comment->getContent()}\n";
            echo "-------------------------------------------\n";
        }
    }
    
    // Gestion des users
    public function addUser($user) {
        $this->users[] = $user;
    }
    
    public function getAllUsers() {
        return $this->users;
    }
    
    public function findUserById($id) {
        foreach ($this->users as $user) {
            if ($user->getIdUser() === $id) {
                return $user;
            }
        }
        return null;
    }
    
    public function removeUser($userId) {
        $newUsers = [];
        foreach ($this->users as $user) {
            if ($user->getIdUser() !== $userId) {
                $newUsers[] = $user;
            }
        }
        if (count($newUsers) < count($this->users)) {
            $this->users = $newUsers;
            return true;
        }
        return false;
    }
    
    public function displayAllUsers() {
        if (empty($this->users)) {
            echo "Aucun utilisateur.\n";
            return;
        }
        
        echo "\n----------- LISTE DES UTILISATEURS ----------\n";
        foreach ($this->users as $user) {
            $createdAt = $user->getCreatedAt()->format('Y-m-d H:i:s');
            $lastLogin = $user->getLastLogin() ? $user->getLastLogin()->format('Y-m-d H:i:s') : 'Jamais connecté';
            echo "ID: {$user->getIdUser()} | {$user->getUsername()} ({$user->getRole()})\n";
            echo "   Email: {$user->getEmail()}\n";
            echo "   Créé le: {$createdAt}\n";
            echo "   Dernière connexion: {$lastLogin}\n";
            echo "-------------------------------------------\n";
        }
    }
    
    //Statistique
    public function countUsers() {
        return count($this->users);
    }
    
    public function countArticles() {
        return count($this->getAllArticles());
    }
    
    public function countCategories() {
        return count($this->categories);
    }
    
    public function countComments() {
        return count($this->getAllComments());
    }
    
    public function displayStats() {
        echo "\n------------- STATISTIQUES SYSTÈME --------------\n";
        echo "Utilisateurs: " . $this->countUsers() . "\n";
        echo "Articles: " . $this->countArticles() . "\n";
        echo "Catégories: " . $this->countCategories() . "\n";
        echo "Commentaires: " . $this->countComments() . "\n";
        echo "Articles publiés: " . count($this->getPublishedArticles()) . "\n";
        echo "Commentaires en attente: " . count($this->getPendingComments()) . "\n";
        echo "----------------------------------------------------\n";
    }
}
?>