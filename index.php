<?php 
class User {
    protected int $idUser;
    protected string $username;
    protected string $email;
    protected string $password;
    protected string $role;
    protected DateTime $createdAt;
    protected ?DateTime $lastLogin;

    public function __construct($username, $email, $password, $role){
        global $users;
        $this->idUser = cont($users)+1;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = new DateTime('today');
        $this->lastLogin = Null;
    }
    public function checklogin($username, $password){
            if ($username===$this->username && $password===$this->password) {
                return true;
            }
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
    public function ReadArticle(){

    }
    public function writeComment(){

    }
    public function checkUnique($email, $username){

    }
    
    
    

}

class Author extends User{
    private string $bio;
    private array $articles = [];

    public function __construct($idUser, $username, $email, $password, $bio, $articles=[]){
        parent::__construct($idUser, $username, $email, $password, 'Author');
        $this->bio = $bio;
        $this->articles=$articles;
    }
    public function creatOwnArticle(Article $article){
        $this->articles[] = $article;
    }
    public function findArticle($id){
        foreach ($articles as $article) {
            if ($article->getIdArticle()===$id) {
                return $article;
            }   
        }
    }
    public function updateOwnArticle(){


    }
    public function deleteOwnArticle(){

    }
    
}
class Moderateur extends User {
    public function ceartAssignArticle(){

    }
    public function updateArticle(){

    }
    public function publishArticle($idArticle){

    }
    public function deleteArticle(){

    }
    public function creatCategory($nameCategory, $parent){

    }
    public function approvedComment($idComment){

    }
    public function deleteComment($idComment){

    }
}



class Editor extends Moderateur {
    private string $moderationLevel;//'junior', 'senior', 'chief'

    public function __construct($idUser, $username, $email, $password, $moderationLevel){
        parent::__construct($idUser, $username, $email, $password, 'Editor');
        $this->moderationLevel = $moderationLevel;

    }
}
class Admin extends Moderateur {
    private bool $isSuperAdmin;
    public function __construct(){
        
    }
    public function creatUser(){

    }
    public function updateRole(){

    }
    public function deleteUser(){

    }
}
class Article {
    private int $idArticle;
    private string $title;
    private string $content;
    private string $category;
    private string $status; // 'draft', 'published', 'archived'
    private array $comments;
    private DateTime $updatedAt;
    private DateTime $publishedAt; 

    public function __construct($title, $content, $category){
        $this->idArticle=$idArticle+1;
        $this->title=$title;
        $this->content=$content;
        $this->category=$category;
        $this->status='draft';
        $this->comments=[];
        $this->updatedAt=Null;
        $this->publishedAt= new DateTime('today');
    }
    
    public function getIdArticle(){
        return $this->idArticle;
    }
    public function addCategory(){

    }
    public function removeCategory(){

    }
    public function publish(){

    }
    public function archiver(){

    }
}

class Category{
    private int $idCategory;
    private string $name;
    private string $description;

    public function __construct($idCategory, $name, $description){
        $this->idCategory=$idCategory;
        $this->name=$name;
        $this->description=$description;

    }
    public function addSubCategory(){

    }
}



    

?>
