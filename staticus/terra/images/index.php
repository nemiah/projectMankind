<?php
echo '<?xml version="1.0" encoding="UTF-8" ?>';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="revisit-after" content="14 days" />
		<meta http-equiv="content-encoding" content="gzip" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta name="Description" content="" />
		<meta name="keywords" content="" />

		<title>Project Mankind - Beyond the stars... - Image database</title>
		<link rel="stylesheet" type="text/css" href="http://data.project-mankind.com/portal/styles/draft.css" />

		<script type="text/javascript" src="http://data.project-mankind.com/general/swfobject.js"></script>
		<style type="text/css">
			h1 {
				padding-top:40px;
				clear:both;
			}

			.imageContainer {
				float:left;
				margin-right:15px;
				margin-top:15px;
			}

			small {
				color:grey;
			}
		</style>
	</head>
	<body>
		<div class="wrapper">
		<div class="header">
			<img src="http://data.project-mankind.com/portal/images/logo.png" />
			<div class="navi"><div class="tabcollector">
				<div class="tab inactiveT selected">

					<p><a href="http://portal.project-mankind.com">Home</a></p>
				</div></div></div>

			<div class="spacer"></div>
		</div>

		<div class="DBContent">

		<?php
		function listImages($dir){
			if ($dh = opendir($dir)){
				echo "<h1>$dir</h1>";
				while (($file = readdir($dh)) !== false) {

					if($file[0] == ".") continue;
					if($file == "index.php") continue;

					if(is_dir($dir."/".$file)) {
						$subdirs[] = $dir."/".$file;
						continue;
					}
					$gis = getimagesize($dir."/".$file);
					
					$path = str_replace(dirname(__FILE__),"",$dir."/".$file);
					echo "<div class=\"imageContainer\">$file<br /><small>$gis[0]x$gis[1], ".round(filesize($dir."/".$file)/1024,2)."KB</small><br /><img style=\"".($gis[0] > 256 ? "width:256px;" : "")."\" src=\".$path\" /></div>";
				}
				
				if(count($subdirs) > 0)
					foreach($subdirs AS $k => $v)
						listImages($v);

				closedir($dh);
			}
		}
		listImages(dirname(__FILE__));
		?>
		</div>
		<div class="footer"><small><a href="Imprint">Imprint</a> | <a href="Privacy">Privacy</a> | Project Mankind - Beyond the stars... | Made in Bavaria</small></div>

		</div>
	</body>
</html>