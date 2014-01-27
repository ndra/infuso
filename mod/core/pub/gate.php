<?

// Загружаем основной класс приложения
include("../class/app.php");

// Создаем приложение
$p = $_SERVER["REQUEST_URI"];
$server = $_SERVER["SERVER_NAME"];
$url = "http://{$server}{$p}";
$app = new \infuso\core\app(array(
    "url" => $url,
    "post" => $_POST,
    "files" => $_FILES,
));

// Выполняем приложение
$app->exec();
