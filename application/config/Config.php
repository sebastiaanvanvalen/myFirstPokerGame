<?php
namespace application\config;;
/**
 * Config should:
 * -
 *
 * @author S. Gerritse <s.gerritse@int2.nl>
 * @copyright Copyright (c) 2020 INT2
 */


class Config {
	private $environment;
	private $namespaces;
	private $namespacesRegistered = false;
	private $databases;
   private $ftpservers;
	
	public function __construct(){
		$source = json_decode(file_get_contents('/Users/baxxie/Sites/myFirstPokerGame/application/config/config.json'), true);
		
		$this->environment	    = $source['environment'];
		$this->namespaces		= $source['namespaces'];
		$this->databases		= $source['databases'];
	}
	
	public function registerNamespaces() {	
		/* get and create the Composer autoloader */
		/* @var $loader Composer\Autoload\ClassLoader */
		$loader = require_once $this->environment['autoloader'];

		/* register the base directories for the namespace prefix */
		foreach ($this->namespaces as $namespace) {
			$prefix = str_replace("\\", "\\\\", $namespace['name'])."\\";
			$loader->addPsr4($prefix, $namespace['path']);
		}
	
		$this->namespacesRegistered = true;
	}
	
	public function getConnection($name, $role){
        if(isset($this->databases[$name])){
            /* connection config */
            $char     = $this->databases[$name]['charset'];

            /* account */
            $database = $this->databases[$name]['database'];
            $user 	 = $this->databases[$name][$role]['user'];
            $pass 	 = $this->databases[$name][$role]['pass'];

            /* SHAZAM! */
				$connection = new \PDO('mysql:host=localhost;dbname='.$database.';charset='.$char, $user, $pass, array(\PDO::MYSQL_ATTR_LOCAL_INFILE => true));
				$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				// $connection->setFetchMode(\PDO::FETCH_ASSOC);
        }else{
            /* ALL WRONG */
            $connection = null;
        }

        return $connection;
	}
    
    public function getFtpConnection($name){
        $connection = ftp_ssl_connect($this->ftpservers[$name]['host'], 10021);
        $login = ftp_login($connection, $this->ftpservers[$name]['user'], $this->ftpservers[$name]['pass']);

        if(!$login) {
            die("No FTP at this moment.");
        }else{
            if(isset($this->ftpservers[$name]['base-directory'])){
                ftp_chdir($connection, $this->ftpservers[$name]['base-directory']);
            }
            ftp_pasv($connection, true);
            return $connection;
        }
    }
}