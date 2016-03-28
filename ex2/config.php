<?PHP

  ob_start();

  @session_start();

  define('USER_SESSIONS', session_id());
  define("DB_HOST" , "localhost:3306");
  define("DB_NAME" , "radius");
  define("DB_USER" , "radius");
  define("DB_PASS" , "radpass");
  define("COOKIE_TIME" , "21600");
  define("RADIUS_SECRET" , "testing123");
  define("UAM_SECRET" , "wasa");
  define("UAM_IP" , "192.168.182.1");
  define("ROOT_PATH" , "/var/www/html/");

  include_once ROOT_PATH."include/functions.php";
  include_once ROOT_PATH."include/setup.php";

?>