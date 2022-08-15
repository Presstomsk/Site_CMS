<?php

require( "config.php" );
session_start();
$action = isset( $_GET['action'] ) ? $_GET['action'] : "";
$username = isset( $_SESSION['username'] ) ? $_SESSION['username'] : "";

if ( $action != "login" && $action != "logout" && $action != "registration" && !$username ) {
  login();
  exit;
}

switch ( $action ) {
  case 'login':
    login();
    break;
  case 'registration':
    registration();
    break;
  case 'logout':
    logout();
    break;
  case 'newArticle':
    newArticle();
    break;
  case 'editArticle':
    editArticle();
    break;
  case 'deleteArticle':
    deleteArticle();
    break;
  default:
    listArticles();
}


function login() {

  $results = array();
  $results['pageTitle'] = "Вход администратора";  
  

  if ( isset( $_POST['login'] ) ) {

    // Пользователь получает форму входа: попытка авторизировать пользователя
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] );
    $query = $conn->prepare("SELECT * FROM users WHERE username=:username");
    $query->bindParam("username", $_POST['username'], PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);    
    if (!$result) {
        $results['errorMessage'] = "Incorrect username or password. Please try again or register.";
        require( TEMPLATE_PATH . "/admin/loginForm.php" );
    } else {
        if (password_verify($_POST['password'], $result['pass'])) {
          $_SESSION['username'] = $_POST['username'];
          header( "Location: admin.php" );
        } else {
          $results['errorMessage'] = "Incorrect username or password. Please try again or register.";
          require( TEMPLATE_PATH . "/admin/loginForm.php" );
        }
    }  
  }
  else {

    // Пользователь еще не получил форму: выводим форму
    require( TEMPLATE_PATH . "/admin/loginForm.php" );
  }  
}

function registration(){ 
  $results = array();
  $results['pageTitle'] = "Регистрация нового пользователя";
  $username = $_POST['username'];
  $pass = $_POST['password'];  
  $password_hash = password_hash($pass, PASSWORD_BCRYPT);

  if ( isset( $_POST['registration'] ) ) {

    // Пользователь получает форму регистрации: попытка зарегистрировать пользователя

    if ( !empty($username) && !empty($pass)) {
      $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
      $query = $conn->prepare("SELECT * FROM users WHERE username=:username");
      $query->bindParam("username", $username, PDO::PARAM_STR);
      $query->execute();
      if ($query->rowCount() > 0) {
        $results['errorMessage'] = "Incorrect username. Please set other username.";
        require( TEMPLATE_PATH . "/admin/registrationForm.php" );
    }
    if ($query->rowCount() == 0) {
      $sql = "INSERT INTO users ( username, pass  ) VALUES ( '".$username."', '".$password_hash."' )";
      $st = $conn->exec($sql);     
      // Регистрация прошла успешно: создаем сессию и перенаправляем на страницу администратора
      $_SESSION['username'] = $_POST['username'];
      header( "Location: admin.php" );      
    }
    //Заносим данные в БД       
    }     
  } else {
    // Пользователь еще не получил форму: выводим форму
    require( TEMPLATE_PATH . "/admin/registrationForm.php" );
  }  
}


function logout() {
  unset( $_SESSION['username'] );
  header( "Location: admin.php" );
}


function newArticle() {

  $results = array();
  $results['pageTitle'] = "Новая статья";
  $results['formAction'] = "newArticle";

  if ( isset( $_POST['saveChanges'] ) ) {

    // Пользователь получает форму редактирования статьи: сохраняем новую статью
    $article = new Article;
    $article->storeFormValues( $_POST );
    $article->insert();
    header( "Location: admin.php?status=changesSaved" );

  } elseif ( isset( $_POST['cancel'] ) ) {

    // Пользователь сбросид результаты редактирования: возвращаемся к списку статей
    header( "Location: admin.php" );
  } else {

    // Пользователь еще не получил форму редактирования: выводим форму
    $results['article'] = new Article;
    require( TEMPLATE_PATH . "/admin/editArticle.php" );
  }

}


function editArticle() {

  $results = array();
  $results['pageTitle'] = "Редактор статьи";
  $results['formAction'] = "editArticle";

  if ( isset( $_POST['saveChanges'] ) ) {

    // Пользователь получил форму редактирования статьи: сохраняем изменения

    if ( !$article = Article::getById( (int)$_POST['articleId'] ) ) {
      header( "Location: admin.php?error=articleNotFound" );
      return;
    }

    $article->storeFormValues( $_POST );
    $article->update();
    header( "Location: admin.php?status=changesSaved" );

  } elseif ( isset( $_POST['cancel'] ) ) {

    // Пользователь отказался от результатов редактирования: возвращаемся к списку статей
    header( "Location: admin.php" );
  } else {

    // Пользвоатель еще не получил форму редактирования: выводим форму
    $results['article'] = Article::getById( (int)$_GET['articleId'] );
    require( TEMPLATE_PATH . "/admin/editArticle.php" );
  }

}


function deleteArticle() {

  if ( !$article = Article::getById( (int)$_GET['articleId'] ) ) {
    header( "Location: admin.php?error=articleNotFound" );
    return;
  }

  $article->delete();
  header( "Location: admin.php?status=articleDeleted" );
}


function listArticles() {
  $results = array();
  $data = Article::getList();
  $results['articles'] = $data['results'];
  $results['totalRows'] = $data['totalRows'];
  $results['pageTitle'] = "Все статьи";

  if ( isset( $_GET['error'] ) ) {
    if ( $_GET['error'] == "articleNotFound" ) $results['errorMessage'] = "Error: Article not found.";
  }

  if ( isset( $_GET['status'] ) ) {
    if ( $_GET['status'] == "changesSaved" ) $results['statusMessage'] = "Your changes have been saved.";
    if ( $_GET['status'] == "articleDeleted" ) $results['statusMessage'] = "Article deleted.";
  }

  require( TEMPLATE_PATH . "/admin/listArticles.php" );
}

?>
