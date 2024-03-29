<?php
/**
 *  This file is part of phynx.

 *  phynx is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.

 *  phynx is distributed in the hope that it will be useful,
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

/**
 * Entfernen Sie das #-Zeichen vor der nächsten Zeile, um MSSQL (experimentell) zu aktivieren
 */
#define("PHYNX_MAIN_STORAGE","MSSQL");

require_once dirname(__FILE__)."/basics.php";

if(!defined("PHYNX_MAIN_STORAGE"))
	if(function_exists("mysqli_connect"))
		define("PHYNX_MAIN_STORAGE","MySQL");
	else
		define("PHYNX_MAIN_STORAGE","MySQLo");

$GLOBALS["phynxLogPhpErrors"] = true;

$physion = "default";
if(isset($_GET["physion"]))
	$physion = $_GET["physion"];

if(session_name() == get_cfg_var("session.name"))
	session_name("phynx_".sha1(__FILE__).($physion != "default" ? "_$physion" : ""));

if((isset($_POST["class"]) AND isset($_POST["method"]) AND $_POST["class"] == "Users" AND $_POST["method"] == "doLogin")
	OR (isset($_GET["class"]) AND isset($_GET["method"]) AND $_GET["class"] == "Users" AND $_GET["method"] == "doLogin"))
	unset($_COOKIE[ini_get("session.name")]);

if(!defined("PHYNX_NO_SESSION_RELOCATION")
	AND ini_get("session.save_path") != ""
	AND (ini_get("open_basedir") == "" OR strpos(ini_get("open_basedir"), ini_get("session.save_path")) !== false) 
	AND isset($_COOKIE[ini_get("session.name")]) 
	AND !file_exists(ini_get("session.save_path")."/sess_".$_COOKIE[ini_get("session.name")])
	AND (!isset($_COOKIE["phynx_relocate"]) OR time() - $_COOKIE["phynx_relocate"] >= 3)
	AND file_exists(ini_get("session.save_path"))){

	unset($_COOKIE[ini_get("session.name")]);
	session_start();
	if(basename($_SERVER["SCRIPT_FILENAME"]) == "index.php") {
		setcookie("phynx_relocate", time(), time() + 600);
		if($_SERVER["HTTP_HOST"] != "cloud.furtmeier.it")
			header("location: index.php");
		else
			header("location: /wolke_$_GET[cloud]");
		exit();
	} else die("SESSION EXPIRED");
}

ini_set("zend.ze1_compatibility_mode","Off");
ini_set("display_errors", "On");

header("Content-Type: text/html; charset=UTF-8");
error_reporting(E_ALL);
set_error_handler("log_error");
if(function_exists('date_default_timezone_set'))
	date_default_timezone_set('Europe/Berlin');


if(!function_exists("array_fill_keys"))
	emoFatalError("I'm sorry, but your PHP version is too old.", "You need at least PHP version 5.2.0 to run this program.<br />You are using ".phpversion().". Please talk to your provider about this.", "phynx");

function log_error($errno, $errmsg, $filename, $linenum) {
	if(!$GLOBALS["phynxLogPhpErrors"]) return;
	
	#if(strpos($filename, "PortscanGUI.class.php") !== false AND strpos($errmsg,"fsockopen") !== false) return;
	
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
	
	if(!isset($_SESSION["phynx_errors"]))
		$_SESSION["phynx_errors"] = array();
	
	$_SESSION["phynx_errors"][] = array($errortype[$errno], $errmsg, $filename, $linenum);
	try {
		SysMessages::log($errortype[$errno].": ".$errmsg."\n$filename:$linenum", "PHP");
	} catch(Exception $e){}
}

session_start();

if(isset($_COOKIE["phynx_customer"]) AND isset($_GET["cloud"]) AND $_COOKIE["phynx_customer"] != $_GET["cloud"]){ //if someone switches the cloud, kick him and reinitialize
	session_destroy();
	session_start();
}

if(!isset($_SESSION["classPaths"])) 
	$_SESSION["classPaths"] = array();
	

function __autoload($class_name) {
	try {
		return findClass($class_name);
	} catch (ClassNotFoundException $e){
		$_SESSION["classPaths"] = array();
		return findClass($class_name);
	}
}

if(!isset($_SESSION["S"]) OR !isset($_SESSION["applications"]) OR $_SESSION["applications"]->numAppsLoaded() == 0){
	Session::init();
	
	if(Session::isPluginLoaded("mAutoLogin"))
		mAutoLogin::doAutoLogin();
}

if($physion != "default" AND isset($_GET["application"]) AND isset($_GET["plugin"]))
	Session::physion($_GET["physion"], $_GET["application"], $_GET["plugin"]);

?>