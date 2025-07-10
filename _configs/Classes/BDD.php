<?php
session_start();
date_default_timezone_set('Africa/Abidjan');
if(isset($_SERVER['HTTP_HOST'])) {
    define('ACTIVE_URL', "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
    define('URL', 'https://developpement.ipscnam.ci/ecmu/');
    define('DIR', $_SERVER['DOCUMENT_ROOT'] . '/ecmu/');
    define('URL_ECNAM', 'https://developpement.ipscnam.ci/ecnam/');

define('CONFIGS', URL.'_configs/');
define('PUBLICS', URL.'_publics/');
define('NODE_MODULES', PUBLICS.'node_modules/');
define('CSS', PUBLICS.'css/');
define('JS', PUBLICS.'js/');
define('IMAGES', PUBLICS.'images/');
}
class BDD
{

    /*private $host = "10.10.0.144";
    private $pass = "Oracle_2020";

    private $host = "10.30.0.42";
    private $pass = "oracle2020";

    */
    private $host = "10.10.0.145";
    //private $host = "10.30.0.42";
    private $pass = "oracle2020";

    private $user = "user_cnam";
    private $dbname = "dbcmu";


    public function __construct($host = null, $user = null, $pass = null, $dbname = null)
    {
        if ($host != null) {
            $this->host = $host;
            $this->user = $user;
            $this->pass = $pass;
            $this->dbname = $dbname;
        }
        try {
            $this->bdd = new PDO('oci:dbname='.$this->host.'/'.$this->dbname.';charset=UTF8', $this->user, $this->pass);
        } catch (PDOException $e) {
            die('{"status": "failed","code": 500,"message": "IMPOSSIBLE DE SE CONNECTER A LA BASE DE DONNEES"}');
        }
    }

    public function query($sql, $data = array())
    {
        $req = $this->bdd->prepare($sql);
        $req->execute($data);
        return $req->fetchAll(PDO::FETCH_OBJ);
    }

    public function getBdd()
    {
        return $this->bdd;
    }
}
?>
