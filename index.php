<?php 
class User {
    protected static int $idCounter = 0;
    protected int $idUser;
    protected string $username;
    protected string $email;
    protected string $password;
    protected string $role;
    protected DateTime $createdAt;
    protected ?DateTime $lastLogin;

    public function __construct($username, $email, $password, $role){
        self::$idCounter++;
        $this->idUser = self::$idCounter;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = new DateTime('now');
        $this->lastLogin = null;
    }
    
    public function checklogin($username, $password){
        if ($username === $this->username && $password === $this->password) {
            $this->lastLogin = new DateTime('now');
            return true;
        }
        return false;
    }
    
    public function getIdUser() {
        return $this->idUser;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getRole() {
        return $this->role;
    }
    
    public function setPassword($newPassword) {
        $this->password = $newPassword;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function getLastLogin() {
        return $this->lastLogin;
    }
}

class Author extends User {
    private string $bio;
    private array $articles = [];

    public function __construct($username, $email, $password, $bio, $articles = []){
        parent::__construct($username, $email, $password, 'Author');
        $this->bio = $bio;
        $this->articles = $articles;
    }
    
    public function getBio() {
        return $this->bio;
    }
    
    public function setBio($newBio) {
        $this->bio = $newBio;
    }
    
    public function createOwnArticle($title, $content, $categories) {
        $article = new Article($title, $content, $categories, $this);
        $this->articles[] = $article;
        return $article;
    }
    
    public function getMyArticles() {
        return $this->articles;
    }
    
    public function findMyArticle($id) {
        foreach ($this->articles as $article) {
            if ($article->getIdArticle() === $id) {
                return $article;
            }
        }
        return null;
    }
    
    public function updateOwnArticle($id, $title, $content, $categories) {
        $article = $this->findMyArticle($id);
        if ($article) {
            $article->setTitle($title);
            $article->setContent($content);
            $article->setCategories($categories);
            return true;
        }
        return false;
    }
    
    public function deleteOwnArticle($id) {
        $newArticles = [];
        foreach ($this->articles as $article) {
            if ($article->getIdArticle() !== $id) {
                $newArticles[] = $article;
            }
        }
        if (count($newArticles) < count($this->articles)) {
            $this->articles = $newArticles;
            return true;
        }
        return false;
    }
    
    public function countArticles() {
        return count($this->articles);
    }
}


class Moderateur extends User {
    
    public function canModifyArticle($article) {
        return true; 
    }
    
    public function canDeleteArticle($article) {
        return true; 
    }
    
    public function publishArticle($article) {
        if ($article->getStatus() === 'draft') {
            $article->publish();
            return true;
        }
        return false;
    }
    
    public function archiveArticle($article) {
        if ($article->getStatus() === 'published') {
            $article->archive();
            return true;
        }
        return false;
    }
    
    public function createCategory($name, $description) {
        return new Category($name, $description);
    }
    
    public function deleteCategory($category, $collection) {
        return $collection->removeCategoryFromSystem($category);
    }
    
    public function approveComment($comment) {
        $comment->setStatus('approved');
    }
    
    public function deleteComment($comment) {
        $comment->setStatus('deleted');
    }
}

class Editor extends Moderateur {
    private string $moderationLevel; 

    public function __construct($username, $email, $password, $moderationLevel){
        parent::__construct($username, $email, $password, 'Editor');
        $this->moderationLevel = $moderationLevel;
    }
    
    public function getModerationLevel() {
        return $this->moderationLevel;
    }
    
    public function setModerationLevel($level) {
        $this->moderationLevel = $level;
    }
}

class Admin extends Moderateur {
    private bool $isSuperAdmin;
    
    public function __construct($username, $email, $password, $isSuperAdmin = false){
        parent::__construct($username, $email, $password, 'Admin');
        $this->isSuperAdmin = $isSuperAdmin;
    }
    
    public function isSuperAdmin() {
        return $this->isSuperAdmin;
    }
    
    public function createUser($username, $email, $password, $role, $extraData = null) {
        switch($role) {
            case 'Author':
                $bio = $extraData ?? 'Pas de bio';
                return new Author($username, $email, $password, $bio);
            case 'Editor':
                $level = $extraData ?? 'junior';
                return new Editor($username, $email, $password, $level);
            case 'Admin':
                $isSuper = $extraData ?? false;
                return new Admin($username, $email, $password, $isSuper);
            default:
                return new User($username, $email, $password, 'Visitor');
        }
    }
    
    public function deleteUser($collection, $userId) {
        if ($userId === $this->idUser) {
            return false; 
        }
        return $collection->removeUser($userId);
    }
    
    public function updateUserRole($user, $newRole) {
        return false; 
    }
    
    public function getSystemStats($collection) {
        return [
            'total_users' => $collection->countUsers(),
            'total_articles' => $collection->countArticles(),
            'total_categories' => $collection->countCategories(),
            'total_comments' => $collection->countComments()
        ];
    }
}

class Article {
    private static int $idCounter = 0;
    private int $idArticle;
    private string $title;
    private string $content;
    private array $categories; 
    private string $status; 
    private User $author;
    private array $comments = [];
    private DateTime $createdAt;
    private ?DateTime $updatedAt;
    private ?DateTime $publishedAt;

    public function __construct($title, $content, $categories, $author){
        self::$idCounter++;
        $this->idArticle = self::$idCounter;
        $this->title = $title;
        $this->content = $content;
        $this->categories = is_array($categories) ? $categories : [$categories];
        $this->author = $author;
        $this->status = 'draft';
        $this->createdAt = new DateTime('now');
        $this->updatedAt = null;
        $this->publishedAt = null;
    }
    
    public function getIdArticle(){
        return $this->idArticle;
    }
    
    public function getTitle(){
        return $this->title;
    }
    
    public function setTitle($newTitle){
        $this->title = $newTitle;
        $this->updatedAt = new DateTime('now');
    }
    
    public function getContent(){
        return $this->content;
    }
    
    public function setContent($newContent){
        $this->content = $newContent;
        $this->updatedAt = new DateTime('now');
    }
    
    public function getCategories(){
        return $this->categories;
    }
    
    public function setCategories($newCategories){
        $this->categories = is_array($newCategories) ? $newCategories : [$newCategories];
        $this->updatedAt = new DateTime('now');
    }
    
    public function addCategory($category){
        if (!in_array($category, $this->categories)) {
            $this->categories[] = $category;
        }
    }
    
    public function removeCategory($category){
        $newCategories = [];
        foreach ($this->categories as $cat) {
            if ($cat !== $category) {
                $newCategories[] = $cat;
            }
        }
        $this->categories = $newCategories;
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    public function getAuthor(){
        return $this->author;
    }
    
    public function publish(){
        $this->status = 'published';
        $this->publishedAt = new DateTime('now');
    }
    
    public function archive(){
        $this->status = 'archived';
    }
    
    public function addComment($comment){
        $this->comments[] = $comment;
    }
    
    public function getComments(){
        return $this->comments;
    }
    
    public function getCreatedAt(){
        return $this->createdAt;
    }
    
    public function getPublishedAt(){
        return $this->publishedAt;
    }
    
    public function getExcerpt($length = 100){
        return substr($this->content, 0, $length) . '...';
    }
}


class Category {
    private static int $idCounter = 0;
    private int $idCategory;
    private string $name;
    private string $description;
    private array $articles = [];

    public function __construct($name, $description){
        self::$idCounter++;
        $this->idCategory = self::$idCounter;
        $this->name = $name;
        $this->description = $description;
    }
    
    public function getIdCategory(){
        return $this->idCategory;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function setName($newName){
        $this->name = $newName;
    }
    
    public function getDescription(){
        return $this->description;
    }
    
    public function setDescription($newDescription){
        $this->description = $newDescription;
    }
    
    public function addArticle($article){
        if (!in_array($article, $this->articles)) {
            $this->articles[] = $article;
        }
    }
    
    public function removeArticle($article){
        $newArticles = [];
        foreach ($this->articles as $art) {
            if ($art !== $article) {
                $newArticles[] = $art;
            }
        }
        $this->articles = $newArticles;
    }
    
    public function getArticles(){
        return $this->articles;
    }
    
    public function countArticles(){
        return count($this->articles);
    }
}


class Comment {
    private static int $idCounter = 0;
    private int $idComment;
    private string $content;
    private $author;
    private Article $article;
    private DateTime $createdAt;
    private string $status; 

    public function __construct($content, $author, $article){
        self::$idCounter++;
        $this->idComment = self::$idCounter;
        $this->content = $content;
        $this->author = $author;
        $this->article = $article;
        $this->createdAt = new DateTime('now');
        $this->status = 'pending';
    }
    
    public function getIdComment(){
        return $this->idComment;
    }
    
    public function getContent(){
        return $this->content;
    }
    
    public function getAuthor(){
        return $this->author;
    }
    
    public function getAuthorName(){
        if (is_string($this->author)) {
            return $this->author;
        }
        return $this->author->getUsername();
    }
    
    public function getArticle(){
        return $this->article;
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    public function setStatus($newStatus){
        $this->status = $newStatus;
    }
    
    public function getCreatedAt(){
        return $this->createdAt;
    }
    
    public function isApproved(){
        return $this->status === 'approved';
    }
}
?>