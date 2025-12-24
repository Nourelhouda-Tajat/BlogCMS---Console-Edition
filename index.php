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
        $this->lastLogin = Null;
    }

    public function ReadArticle(){

    }
    public function writeComment(){

    }
    public function checkUnique($email, $username){

    }
    
    public login($email, $password){

    }
    

}

class Author extends User{
    private string $bio;

    public function __construct($idUser, $username, $email, $password, $role, $bio){
        parent::__construct($idUser, $username, $email, $password, 'Author');
        $this->bio = $bio;

    }
    public creatOwnArticle(){

    }
    public updateOwnArticle(){

    }
    public deleteOwnArticle(){

    }
    
}
class Moderateur extends User {
    public ceartAssignArticle(){

    }
    public updateArticle(){

    }
    public publishArticle($idArticle){

    }
    public deleteArticle(){

    }
    public creatCategory($nameCategory, $parent){

    }
    public approvedComment($idComment){

    }
    public deleteComment($idComment){

    }
}



class Editor extends Moderateur {
    private string $moderationLevel;//'junior', 'senior', 'chief'

    public function __construct(){
        
    }
}
class Admin extends Moderateur {
    private bool $isSuperAdmin
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
    private string $status; // 'draft', 'published', 'archived'
    private array $comments;
    private DateTime $updatedAt;
    private DateTime $publishedAt; 

    public function __construct(){

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
    private ?int $parent;

    public function __construct(){

    }
    public function addSubCategory(){

    }
}


?>
