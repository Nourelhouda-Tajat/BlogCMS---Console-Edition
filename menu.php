<?php
require_once('collections.php');

echo "******* BLOGCMS CONSOLE EDITION  *******\n";

$db = new Collection();
$running = true;

while ($running) {
    // Affichage de l'en-tête avec état de connexion
    echo "\n";
    if ($db->isLoggedIn()) {
        $user = $db->getCurrentUser();
        echo " Connecté: {$user->getUsername()} ({$user->getRole()}) \n";
    } else {
        echo " VISITEUR (Mode non connecté) \n";
    }
    
    // Menu dynamique selon le rôle
    if (!$db->isLoggedIn()) {
        // ========== MENU VISITEUR ==========
        echo "\nMENU VISITEUR:\n";
        echo "1. Voir les articles publiés\n";
        echo "2. Ajouter un commentaire\n";
        echo "3. Se connecter\n";
        echo "0. Quitter\n";
    } else {
        $role = $db->getCurrentUser()->getRole();
        
        if ($role === 'Author') {
            // ========== MENU AUTEUR ==========
            echo "\nMENU AUTEUR:\n";
            echo "1. Voir tous les articles\n";
            echo "2. Voir mes articles\n";
            echo "3. Créer un nouvel article\n";
            echo "4. Modifier mon article\n";
            echo "5. Supprimer mon article\n";
            echo "6. Ajouter un commentaire\n";
            echo "7. Se déconnecter\n";
            echo "0. Quitter\n";
            
        } elseif ($role === 'Editor') {
            // ========== MENU ÉDITEUR ==========
            echo "\nMENU ÉDITEUR:\n";
            echo "1. Voir tous les articles\n";
            echo "2. Publier un article\n";
            echo "3. Archiver un article\n";
            echo "4. Supprimer un article\n";
            echo "5. Gérer les catégories\n";
            echo "6. Créer une catégorie\n";
            echo "7. Modérer les commentaires\n";
            echo "8. Se déconnecter\n";
            echo "0. Quitter\n";
            
        } elseif ($role === 'Admin') {
            // ========== MENU ADMINISTRATEUR ==========
            echo "\nMENU ADMINISTRATEUR:\n";
            echo "1. Voir tous les articles\n";
            echo "2. Gérer les utilisateurs\n";
            echo "3. Créer un utilisateur\n";
            echo "4. Supprimer un utilisateur\n";
            echo "5. Gérer les catégories\n";
            echo "6. Modérer les commentaires\n";
            echo "7. Statistiques système\n";
            echo "8. Se déconnecter\n";
            echo "0. Quitter\n";
        }
    }
    
    $choice = readline("\nVotre choix : ");
    echo "\n";
    
    if (!$db->isLoggedIn()) {
        //Menu visiteur
        switch ($choice) {
            case '1': // Voir articles publiés
                $articles = $db->getPublishedArticles();
                if (empty($articles)) {
                    echo "Aucun article publié.\n";
                } else {
                    foreach ($articles as $article) {
                        echo "ID: {$article->getIdArticle()} | {$article->getTitle()}\n";
                        echo "Par: {$article->getAuthor()->getUsername()}\n";
                        echo "{$article->getExcerpt(80)}\n";
                        echo "---\n";
                    }
                }
                break;
                
            case '2': // Ajouter un commentaire (visiteur peut commenter)
                $id = (int)readline("ID de l'article à commenter : ");
                $article = $db->findArticleById($id);
                if ($article) {
                    $content = readline("Votre commentaire : ");
                    $username = readline("Votre nom : ");
                    // Visiteur anonyme - juste un nom en string, pas de User
                    $comment = new Comment($content, $username, $article);
                    $db->addComment($comment);
                    echo "Commentaire ajouté (en attente de modération)\n";
                } else {
                    echo "Article introuvable.\n";
                }
                break;
                
            case '3': // Se connecter
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
        $role = $db->getCurrentUser()->getRole();
        
        if ($role === 'Author') {
            // Menu author
            switch ($choice) {
                case '1': // Voir tous les articles
                    $db->displayAllArticles();
                    break;
                    
                case '2': // Voir mes articles
                    $myArticles = $db->getArticlesByAuthor($db->getCurrentUser()->getIdUser());
                    if (empty($myArticles)) {
                        echo "Vous n'avez pas encore d'articles.\n";
                    } else {
                        foreach ($myArticles as $article) {
                            echo "ID: {$article->getIdArticle()} | {$article->getTitle()} [{$article->getStatus()}]\n";
                        }
                    }
                    break;
                    
                case '3': // Créer un article
                    $title = readline("Titre de l'article : ");
                    $content = readline("Contenu : ");
                    echo "Catégories disponibles:\n";
                    $db->displayAllCategories();
                    $catIds = readline("IDs des catégories (séparés par des virgules) : ");
                    $catIds = array_map('intval', explode(',', $catIds));
                    
                    $categories = [];
                    foreach ($catIds as $catId) {
                        $cat = $db->findCategoryById($catId);
                        if ($cat) {
                            $categories[] = $cat;
                        }
                    }
                    
                    if (empty($categories)) {
                        echo "Vous devez sélectionner au moins une catégorie.\n";
                    } else {
                        $author = $db->getCurrentUser();
                        $article = $author->createOwnArticle($title, $content, $categories);
                        $db->addArticle($article);
                        echo "Article créé avec succès (ID: {$article->getIdArticle()})\n";
                    }
                    break;
                    
                case '4': // Modifier mon article
                    $id = (int)readline("ID de votre article : ");
                    $article = $db->findArticleById($id);
                    if ($article && $article->getAuthor()->getIdUser() === $db->getCurrentUser()->getIdUser()) {
                        $newTitle = readline("Nouveau titre : ");
                        $newContent = readline("Nouveau contenu : ");
                        $article->setTitle($newTitle);
                        $article->setContent($newContent);
                        echo "Article modifié\n";
                    } else {
                        echo "Article introuvable ou vous n'êtes pas l'auteur\n";
                    }
                    break;
                    
                case '5': // Supprimer mon article
                    $id = (int)readline("ID de votre article à supprimer : ");
                    $article = $db->findArticleById($id);
                    if ($article && $article->getAuthor()->getIdUser() === $db->getCurrentUser()->getIdUser()) {
                        $confirm = readline("Confirmer la suppression? (oui/non) : ");
                        if (strtolower($confirm) === 'oui') {
                            $db->deleteArticle($id);
                            echo "Article supprimé\n";
                        }
                    } else {
                        echo "Article introuvable ou vous n'êtes pas l'auteur\n";
                    }
                    break;
                    
                case '6': // Ajouter un commentaire
                    $id = (int)readline("ID de l'article : ");
                    $article = $db->findArticleById($id);
                    if ($article) {
                        $content = readline("Votre commentaire : ");
                        $comment = new Comment($content, $db->getCurrentUser(), $article);
                        $db->addComment($comment);
                        echo "Commentaire ajouté\n";
                    } else {
                        echo "Article introuvable\n";
                    }
                    break;
                    
                case '7': // Déconnexion
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
            
        } elseif ($role === 'Editor') {
            // Menu editeur
            switch ($choice) {
                case '1': // Voir tous les articles
                    $db->displayAllArticles();
                    break;
                    
                case '2': // Publier un article
                    $id = (int)readline("ID de l'article à publier : ");
                    $article = $db->findArticleById($id);
                    if ($article) {
                        $editor = $db->getCurrentUser();
                        if ($editor->publishArticle($article)) {
                            echo "Article publié\n";
                        } else {
                            echo "L'article est déjà publié\n";
                        }
                    } else {
                        echo "Article introuvable\n";
                    }
                    break;
                    
                case '3': // Archiver un article
                    $id = (int)readline("ID de l'article à archiver : ");
                    $article = $db->findArticleById($id);
                    if ($article) {
                        $editor = $db->getCurrentUser();
                        if ($editor->archiveArticle($article)) {
                            echo "Article archivé\n";
                        } else {
                            echo "L'article n'est pas publié\n";
                        }
                    } else {
                        echo "Article introuvable\n";
                    }
                    break;
                    
                case '4': // Supprimer un article
                    $id = (int)readline("ID de l'article à supprimer : ");
                    $confirm = readline("Confirmer la suppression? (oui/non) : ");
                    if (strtolower($confirm) === 'oui') {
                        if ($db->deleteArticle($id)) {
                            echo "Article supprimé\n";
                        } else {
                            echo "Article introuvable\n";
                        }
                    }
                    break;
                    
                case '5': // Gérer les catégories
                    $db->displayAllCategories();
                    echo "\nActions disponibles:\n";
                    echo "1. Supprimer une catégorie\n";
                    echo "2. Retour au menu\n";
                    $action = readline("Votre choix : ");
                    if ($action === '1') {
                        $catId = (int)readline("ID de la catégorie à supprimer : ");
                        $confirm = readline("Confirmer la suppression? (oui/non) : ");
                        if (strtolower($confirm) === 'oui') {
                            $editor = $db->getCurrentUser();
                            if ($editor->deleteCategory($catId, $db)) {
                                echo "Catégorie supprimée\n";
                            } else {
                                echo "Catégorie introuvable\n";
                            }
                        }
                    }
                    break;
                    
                case '6': // Créer une catégorie
                    $name = readline("Nom de la catégorie : ");
                    $description = readline("Description : ");
                    $editor = $db->getCurrentUser();
                    $category = $editor->createCategory($name, $description);
                    $db->addCategory($category);
                    echo "Catégorie créée (ID: {$category->getIdCategory()})\n";
                    break;
                    
                case '7': // Modérer les commentaires
                    $allComments = $db->getAllComments();
                    if (empty($allComments)) {
                        echo "Aucun commentaire.\n";
                    } else {
                        foreach ($allComments as $comment) {
                            $createdAt = $comment->getCreatedAt()->format('Y-m-d H:i:s');
                            echo "\n-------------------------------\n";
                            echo "ID: {$comment->getIdComment()}\n";
                            echo "Article: {$comment->getArticle()->getTitle()}\n";
                            echo "Auteur: {$comment->getAuthorName()}\n";
                            echo "Date: {$createdAt}\n";
                            echo "Statut actuel: {$comment->getStatus()}\n";
                            echo "Contenu: {$comment->getContent()}\n";
                            echo "---------------------------------\n";
                            $action = readline("Action (a=approuver, r=rejeter, s=spam, n=suivant) : ");
                            $editor = $db->getCurrentUser();
                            if ($action === 'a') {
                                $editor->approveComment($comment);
                                echo "Commentaire approuvé\n";
                            } elseif ($action === 'r') {
                                $comment->setStatus('rejected');
                                echo "Commentaire rejeté\n";
                            } elseif ($action === 's') {
                                $comment->setStatus('spam');
                                echo "Commentaire spam\n";
                            }
                        }
                    }
                    break;
                    
                case '8': // Déconnexion
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
            
        } elseif ($role === 'Admin') {
            //Menu administrateur
            switch ($choice) {
                case '1': // Voir tous les articles
                    $db->displayAllArticles();
                    break;
                    
                case '2': // Gérer les utilisateurs
                    $db->displayAllUsers();
                    break;
                    
                case '3': // Créer un utilisateur
                    $username = readline("Username : ");
                    $email = readline("Email : ");
                    $password = readline("Password : ");
                    $role = readline("Rôle (Author/Editor/Admin) : ");
                    
                    $admin = $db->getCurrentUser();
                    
                    if ($role === 'Author') {
                        $bio = readline("Bio : ");
                        $newUser = $admin->createUser($username, $email, $password, 'Author', $bio);
                    } elseif ($role === 'Editor') {
                        $level = readline("Niveau (junior/senior/chief) : ");
                        $newUser = $admin->createUser($username, $email, $password, 'Editor', $level);
                    } elseif ($role === 'Admin') {
                        $isSuperStr = readline("Super admin? (oui/non) : ");
                        $isSuper = strtolower($isSuperStr) === 'oui';
                        $newUser = $admin->createUser($username, $email, $password, 'Admin', $isSuper);
                    } else {
                        $newUser = $admin->createUser($username, $email, $password, 'Visitor');
                    }
                    
                    $db->addUser($newUser);
                    echo "Utilisateur créé (ID: {$newUser->getIdUser()})\n";
                    break;
                    
                case '4': // Supprimer un utilisateur
                    $db->displayAllUsers();
                    $id = (int)readline("ID de l'utilisateur à supprimer : ");
                    $confirm = readline("Confirmer la suppression? (oui/non) : ");
                    if (strtolower($confirm) === 'oui') {
                        $admin = $db->getCurrentUser();
                        if ($admin->deleteUser($db, $id)) {
                            echo "Utilisateur supprimé\n";
                        } else {
                            echo "Utilisateur introuvable\n";
                        }
                    }
                    break;
                    
                case '5': // Gérer les catégories
                    $db->displayAllCategories();
                    echo "\nActions disponibles:\n";
                    echo "1. Supprimer une catégorie\n";
                    echo "2. Retour au menu\n";
                    $action = readline("Votre choix : ");
                    if ($action === '1') {
                        $catId = (int)readline("ID de la catégorie à supprimer : ");
                        $confirm = readline("Confirmer la suppression? (oui/non) : ");
                        if (strtolower($confirm) === 'oui') {
                            $admin = $db->getCurrentUser();
                            if ($admin->deleteCategory($catId, $db)) {
                                echo "Catégorie supprimée\n";
                            } else {
                                echo "Catégorie introuvable\n";
                            }
                        }
                    }
                    break;
                    
                case '6': // Modérer les commentaires
                    $allComments = $db->getAllComments();
                    if (empty($allComments)) {
                        echo "Aucun commentaire.\n";
                    } else {
                        foreach ($allComments as $comment) {
                            $createdAt = $comment->getCreatedAt()->format('Y-m-d H:i:s');
                            echo "\n-----------------------------------------\n";
                            echo "ID: {$comment->getIdComment()}\n";
                            echo "Article: {$comment->getArticle()->getTitle()}\n";
                            echo "Auteur: {$comment->getAuthorName()}\n";
                            echo "Date: {$createdAt}\n";
                            echo "Statut actuel: {$comment->getStatus()}\n";
                            echo "Contenu: {$comment->getContent()}\n";
                            echo "------------------------------------------\n";
                            $action = readline("Action (a=approuver, s=spam, d=supprimer, n=suivant) : ");
                            $admin = $db->getCurrentUser();
                            if ($action === 'a') {
                                $admin->approveComment($comment);
                                echo "Commentaire approuvé\n";
                            } elseif ($action === 's') {
                                $comment->setStatus('spam');
                                echo "Commentaire spam\n";
                            } elseif ($action === 'd') {
                                $admin->deleteComment($comment);
                                echo "Commentaire supprimé\n";
                            }
                        }
                    }
                    break;
                    
                case '7': // Statistiques système
                    $admin = $db->getCurrentUser();
                    $stats = $admin->getSystemStats($db);
                    echo "\n---------- STATISTIQUES -----------\n";
                    echo "Utilisateurs: {$stats['total_users']}\n";
                    echo "Articles: {$stats['total_articles']}\n";
                    echo "Catégories: {$stats['total_categories']}\n";
                    echo "Commentaires: {$stats['total_comments']}\n";
                    echo "------------------------------------\n";
                    break;
                    
                case '8': // Déconnexion
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
    }
}
?>