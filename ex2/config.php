<?PHP

  ob_start();

  @session_start();

  define('USER_SESSIONS', session_id());
  define("DB_HOST" , "localhost:3306");
  define("DB_NAME" , "radius");
  define("DB_USER" , "radius");
  define("DB_PASS" , "radpass");
  define("COOKIE_TIME" , "21600");

  include_once "include/functions.php";
  include_once "include/setup.php";

?>