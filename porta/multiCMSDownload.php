<?php
/*
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
error_reporting(E_ALL);
require "./multiCMSData/connect.php";

if(isset($_GET["filedl"])) {
	$DL = new Download($_GET["filedl"]);
	$DL->makeDownload();
	header("Location: ".$DL->getA()->url);
	exit();
}

if(isset($_GET["newestdl"])) {
	$aC = new anyC();
	$aC->setCollectionOf("Download");
	$aC->addAssocV3("ContentID","=",$_GET["newestdl"]);
	$aC->addOrderV3("datum","DESC");
	$aC->setLimitV3("1");
	$DL = $aC->getNextEntry();
	$DL = new Download($DL->getID());
	
	if(!isset($_GET["getLink"])) {
		$DL->makeDownload();
		header("Location: ".$DL->getA()->url);
		exit();
	}
	else die($DL->getA()->url);
}
?>