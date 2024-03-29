<?php
/**
 *  This file is part of multiCMS.

 *  multiCMS is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.

 *  multiCMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 *  2007 - 2012, Rainer Furtmeier - Rainer@Furtmeier.de
 */

/**
 * Entfernen Sie das #-Zeichen vor der nächsten Zeile, um MySQLi zu deaktivieren
 */
#define("PHYNX_MAIN_STORAGE","MySQLo");
$GLOBALS["phynxLogPhpErrors"] = true;

if(function_exists("mysqli_connect")) define("PHYNX_MAIN_STORAGE","MySQL");
else define("PHYNX_MAIN_STORAGE","MySQLo");
session_name("mCMSSID");
session_start();
if(!isset($phpFWPath)) $phpFWPath = "./multiCMS";

if(!isset($paths)) $paths = array();
$paths[] = "$phpFWPath/classes/backend";
$paths[] = "$phpFWPath/classes/exceptions/";
$paths[] = "$phpFWPath/classes/frontend/";
$paths[] = "$phpFWPath/classes/interfaces/";
$paths[] = "$phpFWPath/classes/toolbox/";

$paths[] = "$phpFWPath/libraries/";
$paths[] = "$phpFWPath/libraries/mailer/";

$paths[] = "$phpFWPath/specifics/";

$paths[] = "$phpFWPath/multiCMS/Handler/";
$paths[] = "$phpFWPath/multiCMS/Downloads/";
$paths[] = "$phpFWPath/multiCMS/Tracker/";
$paths[] = "$phpFWPath/multiCMS/Domains/";
$paths[] = "$phpFWPath/multiCMS/Content/";
$paths[] = "$phpFWPath/multiCMS/Seiten/";
$paths[] = "$phpFWPath/multiCMS/Templates/";
$paths[] = "$phpFWPath/multiCMS/Website/";

$paths[] = "$phpFWPath/plugins/Installation/";
$paths[] = "$phpFWPath/plugins/Userdata/";

#set_include_path(implode(PATH_SEPARATOR, $paths));
$_SESSION["Paths"] = $paths;

require $phpFWPath."/system/basics.php";
define("PHYNX_VIA_INTERFACE", true);

function addClassPath($path){
	if(!isset($_SESSION["UserPaths"]))
		$_SESSION["UserPaths"] = array();
	
	if($path{strlen($path) - 1} != "/") $path .= "/";
	
	$_SESSION["UserPaths"][basename($path)] = $path;
}

function __autoload($c) {
	for($i=0;$i<count($_SESSION["Paths"]);$i++){
		if($_SESSION["Paths"][$i]{strlen($_SESSION["Paths"][$i]) - 1} != "/") $_SESSION["Paths"][$i] .= "/";
		$p = $_SESSION["Paths"][$i].$c.".class.php";
		if(is_file($p)){
			require $p;
			return 1;
		}
	}
	
	if(isset($_SESSION["UserPaths"]))
		foreach($_SESSION["UserPaths"] AS $p){
			$path = $p.$c.".class.php";
			if(is_file($path)){
				require_once $path;
				return 1;
			}
		}

	eval('class '.$c.' { ' .
		'    public function __construct() { ' .
		'        throw new ClassNotFoundException("'.$c.'"); ' .
		'    } ' .
		'} ');
}

function log_error($errno, $errmsg, $filename, $linenum) {
	#if(defined('E_DEPRECATED') AND $errno == E_DEPRECATED) return;
	if(!$GLOBALS["phynxLogPhpErrors"]) return;

	if(strpos($filename, "PortscanGUI.class.php") !== false AND strpos($errmsg,"fsockopen") !== false) return;

	$errortype = Array(
		E_ERROR => 'Error',
		E_WARNING => 'Warning',
		E_PARSE => 'Parsing Error',
		E_NOTICE => 'Notice',
		E_CORE_ERROR => 'Core Error',
		E_CORE_WARNING => 'Core Warning',
		E_COMPILE_ERROR => 'Compile Error',
		E_COMPILE_WARNING => 'Compile Warning',
		E_USER_ERROR => 'User Error',
		E_USER_WARNING => 'User Warning',
		E_USER_NOTICE => 'User Notice',
		E_STRICT => 'Runtime Notice'
	);

	if(defined('E_RECOVERABLE_ERROR'))
		$errortype[E_RECOVERABLE_ERROR] = 'Catchable Fatal Error';

	if(defined('E_DEPRECATED'))
		$errortype[E_DEPRECATED] = 'Function Deprecated';

	if(strpos($errmsg, "mysql_pconnect() [<a href='function.mysql-pconnect'>function.mysql-pconnect</a>]: Access denied") !== false AND strpos($filename,"classes/backend/DBStorageU.class.php") !== null) return;
	if(strpos($errmsg, "mysqli::mysqli() [<a href='function.mysqli-mysqli'>function.mysqli-mysqli</a>]: (28000/1045): Access denied") !== false AND strpos($filename,"classes/backend/DBStorage.class.php") !== null) return;
	if(strpos($errmsg, "mysqli::mysqli() [<a href='function.mysqli-mysqli'>function.mysqli-mysqli</a>]: (42000/1044): Access denied") !== false AND strpos($filename,"classes/backend/DBStorage.class.php") !== null) return;
	
	if(!isset($_SESSION["phynx_errors"]))
		$_SESSION["phynx_errors"] = array();

	$_SESSION["phynx_errors"][] = array($errortype[$errno], $errmsg, $filename, $linenum);

	if(count($_SESSION["phynx_errors"]) > 20){
		$tempArray = array_reverse($_SESSION["phynx_errors"]);
		array_pop($tempArray);
		$_SESSION["phynx_errors"] = array_reverse($tempArray);
	}
}

set_error_handler("log_error");
#if(!isset($_SESSION["S"])) {
	#$_SESSION["S"] = new Session();
	Session::init();
	#if(!isset($_SESSION["DBData"]))
		$_SESSION["DBData"] = $_SESSION["S"]->getDBData("$phpFWPath/system/DBData/");
#}
?>
