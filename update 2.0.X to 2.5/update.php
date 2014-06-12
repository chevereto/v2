<?php
/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @version	2.6.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>

  Copyright (c) Rodolfo Berrios <inbox@rodolfoberrios.com>
  
  Licensed under the MIT license
  http://opensource.org/licenses/MIT

  --------------------------------------------------------------------- */

/*** Update script 2.0.X to 2.5 ***/

define('access', 'update');
define('SKIP_MAINTENANCE', true);

/*** Load Chevereto ***/
if(!@include_once('includes/chevereto.php')) die('Can\'t find includes/chevereto.php');

if(!defined('__CHV_VERSION__') || preg_match('/2\.0/', version(true))) {
	chevereto_die('', 'Version mismatch', array('This script is designed to make the update from 2.0.X to 2.5 but in order to proceed you need to have installed at least 2.5', 'Please make sure that you have uploaded the 2.5 script files.'));
}

define('__CHV_OLD_FOLDER__', __CHV_FOLDER_IMAGES__.'/old');
define('__CHV_OLD_PATH__', __CHV_PATH_IMAGES__.'old/');

@session_start();

/*** get count ***/
function imageCount() {
	$image_dir = new DirectoryIterator(__CHV_PATH_IMAGES__);
	$imageCount = 0;

	foreach($image_dir as $image) {
		if(!$image->isFile() || !in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), array('jpg','png','gif')) || file_exists(__CHV_OLD_PATH__.$image->getFilename())) continue;
		$imageCount++;
	}
	return $imageCount;
}

if(!is_admin()) {
	include(__CHV_PATH_SYSTEM__.'login.php');
	die;
}

/*** Require the dB ***/
if($dB->dead) {
	chevereto_die(array($dB->error),'dB error', array('There is a problem with the dB. The error reported is:', 'Please check your dB settings in the config file, if the problem persist go to our <a href="http://chevereto.com/support">Tech Support</a> area')); //
}

// This is actually the update tool
if(isset($_GET['ajax-update'])) {
	
	if(!$dB->get_option('maintenance')) {
		die(json_output(array('status_code'=>403, 'status_txt'=>'Maintenance mode is not enabled.')));
	}
		
	if($_SESSION['update_info']['update_completed']>=$_SESSION['update_info']['update_queue']) {
		die(json_output(array('status_code'=>200,'status_txt'=>'Update completed','completed'=>'100%')));
	}
	
	if(!check_permissions(array(__CHV_OLD_PATH__))) {
		die('Wrong permissions in '.__CHV_OLD_PATH__);
	}
	
	define('__CHV_PATH_THUMBS__', __CHV_ROOT_DIR__.$config['folder_thumbs'].'/');
	
	$image_dir = new DirectoryIterator(__CHV_PATH_IMAGES__);
	$imageCount = $_SESSION['update_info']['update_completed'];
	foreach($image_dir as $image) {
		if(!$image->isFile() || !in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), array('jpg','png','gif')) || file_exists(__CHV_OLD_PATH__.$image->getFilename())) continue;
		
		// Check image
		$get_info = get_info($image->getPathname());
		if(!check_value($get_info['width']) || !check_value($get_info['height'])) {
			@unlink($image->getPathname());
			continue;
		}
		
		$mime = $get_info['mime'];
		if ($mime=='image/gif')	{ $ext = 'gif'; }
		if ($mime=='image/png')	{ $ext = 'png'; }
		if ($mime=='image/pjeg' or $mime=='image/jpeg')		{ $ext = 'jpg'; }
		if ($mime=='image/bmp'	or $mime=='image/x-ms-bmp')	{ $ext = 'bmp'; }
		
		$image_info = array(
			'image_name'	=> preg_replace('/(\.jpg|\.png|\.gif)$/', '', $image->getFilename()),
			'image_type'	=> $ext,
			'image_width'	=> $get_info['width'],
			'image_height'	=> $get_info['height'],
			'image_bytes'	=> $get_info['bytes'],
			'image_date'	=> date('Y-m-d H:i:s', $image->getCTime()),
			'storage_id'	=> '1',
			'uploader_ip'	=> '0.0.0.0'
		);
		if(rename($image->getPathname(), __CHV_OLD_PATH__.$image->getFilename())) {
			@rename(__CHV_PATH_THUMBS__.$image->getFilename(), preg_replace('/(.*)(\.jpg|\.png|\.gif)$/', '$1.th$2', __CHV_OLD_PATH__.$image->getFilename()));
			if(!$dB->insert_file($image_info)) {
				die(json_output(array('status_code'=>403,'status_txt'=>'Can\'t insert image '.$image->getFilename().' to the dB')));
			}
		} else {
			die(json_output(array('status_code'=>403,'status_txt'=>'Can\'t move image '.$image->getFilename().' to the old folder')));
		}
		$imageCount++;
		$_SESSION['update_info']['update_completed'] = $imageCount;
		$completion = round(100*$imageCount/$_SESSION['update_info']['update_queue']).'%';
		die(json_output(array('status_code'=>200,'status_txt'=>'Processing image '.$imageCount.' of '.$_SESSION['update_info']['update_queue'].'','completed'=>$completion)));
	}	
}

if(isset($_GET['do_update'])) {
	
	$maintenance = $dB->query("UPDATE chv_options SET option_value=? WHERE option_key='maintenance'", 1);
	
	if(!file_exists(__CHV_OLD_PATH__)) {
		@mkdir(__CHV_OLD_PATH__);
	}	
	
	if(file_exists(__CHV_OLD_PATH__)) {
		if(!check_value($_SESSION['update_info'])) {
			$_SESSION['update_info'] = array(
				'update_queue' => imageCount(),
				'update_completed' => 0
			);
		}
		$doctitle = 'Update in progress';
		$content = '<div id="update-status"><span id="update-caption">Preparing to process '.$_SESSION['update_queue'].' images...</span><span id="percent" style="float: right;">0%</span></div>
		<div id="progress-bar"><div id="current-progress" style="width: 0%"></div></div>';	
	} else {
		$doctitle = 'Can\'t create the '.__CHV_OLD_FOLDER__.' folder';
		$content = '<h1>Can\'t create the old folder</h1>
		<p>The update script can\'t create the <code>'.__CHV_OLD_FOLDER__.'</code> folder. Please manually create this folder and try again.</p>';	
	}
	
	if(!check_permissions(array(__CHV_OLD_PATH__))) {
		$doctitle = 'No write permission in the '.__CHV_OLD_FOLDER__.' folder';
		$content = '<h1>Can\'t write in the old folder</h1>
		<p>The update script can\'t write in the <code>'.__CHV_OLD_FOLDER__.'</code> folder. Please check the permissions.</p>';	
	}
	
	if(!$maintenance) {
		$doctitle = 'Can\'t set the maintenance mode';
		$content = '<h1>The update Script can set the maintenance mode</h1>
		<p>Please try again in a few moments. This is caused for this SQL error: <code>'.$dB->error.'</code>';
	}
	
} else {
	
	$imageCount = imageCount();
		
	if(isset($_GET['completed'])) {
		if($imageCount==0) {
			
			$doctitle = 'Update completed';
			$content = '<h1>Your website is now running Chevereto 2.5!</h1>
				<p>You have successfully update your old image files to the new 2.5 version. Now you can manage this files on the <a href="'.__CHV_BASE_URL__.'admin">File Manager</a>.</p>';
			
			$htaccess_rules = "RewriteRule ^".__CHV_FOLDER_IMAGES__."/([a-zA-Z0-9_-]+\.)(jpg|png|gif)$ ".__CHV_FOLDER_IMAGES__."/old/$1$2 [L] #legacy images"."\n"."RewriteRule ^".__CHV_FOLDER_IMAGES__."/thumbs/([a-zA-Z0-9_-]+\.)(jpg|png|gif)$ ".__CHV_FOLDER_IMAGES__."/old/$1th.$2 [L] #legacy thumbs";
			$htaccess_before = "RewriteRule ^api$ api.php [L]";
			$htaccess = htaccess($htaccess_rules, __CHV_ROOT_DIR__, $htaccess_before);
			
			if($htaccess) {
				$htaccess_code = file_get_contents(__CHV_ROOT_DIR__.'.htaccess');
				$content .= '<h1>.htaccess rules updated</h1>
					<p>Your root <code>.htaccess</code> file has been updated. The new rules are listed below:</p>
					<p><code style="display: block;">'.nl2br($htaccess_code).'</code></p>
					<p style="color: red;">Please do not remove the new rules as these enable the old image links to work.</p>';
			} else {
				$htaccess_fgc = file_get_contents(__CHV_ROOT_DIR__.'.htaccess');
				$htaccess_code = str_replace($htaccess_before, $htaccess_rules."\n".$htaccess_before, $htaccess_fgc);
				$content .= '<h1>You need to edit the .htaccess root file</h1>
					<p>To preserve your old URLs, please replace your whole root <code>.htaccess</code> file with this:<p>
					<p><code style="display: block;">'.nl2br($htaccess_code).'</code></p>';
			}
			
			$maintenance = $dB->query("UPDATE chv_options SET option_value=? WHERE option_key='maintenance'", 0);
			if(!$maintenance) {
				$content .= '<h1>Maintenance mode is still on</h1>
					<p>Due to a SQL error the maintenance mode is still on. The reported erros says <code>'.$dB->error.'</code> You can try to refresh this page or you can manually change this on your dB (table <ode>chv_options</code>).</p>'; 
			}
			
		} else {
			unset($_SESSION['update_info']);
			header('Location: '.absolute_to_url(__FILE__).'?do_update'); die;
		}
	} else {
		$doctitle = 'Chevereto Update Script - 2.0.X to 2.5';
		if($imageCount==0) {
			$content = '<h1>It seems that you don\'t need to update</h1>
				<p>The update script have found that you don\'t need to update. If you think that this is wrong is because the update script is trying to find old files in the <code>'.__CHV_FOLDER_IMAGES__.'</code> folder. Please set in config the right folder where you have old files.';
		} else {
			unset($_SESSION['update_info']);
			$_SESSION['update_info'] = array(
				'update_queue' => $imageCount,
				'update_completed' => 0
			);
			$content = '<h1>Welcome to the 2.5 update</h1>
				<p>This script will update your current folder and file structure to the lastest 2.5 version of Chevereto. Many new features have been introduced, the biggest one being the new <a href="'.__CHV_BASE_URL__.'admin">File Manager</a> which will enable you to easily manage and edit uploaded images.</p>
				<h1>Do I need to update?</h1>
				<p>This script has detected that <span class="highlight">'.$imageCount.' images need importing</span> it is <strong>strongly recommended</strong that you proceed with this update.</p>
				<h1>What will be preserved and what will changed?</h1>
				<p>All your images and thumbs will be preserved but some things will change:</p>
				<ul>
					<li>The 2.0 images will be moved from <code>'.$config['folder_images'].'</code> to <code>'.$config['folder_images'].'/old</code></li>
					<li>Viewer links will be redirected from <code>?v=filename.ext</code> to <code>/'.$config['virtual_folder_image'].'/&lt;ID&gt;</code></li>
					<li>Thumbs will be moved to <code>'.$config['folder_images'].'/old</code> with the suffix <code>.th.ext</code> in the file name</li>
				</ul>
				<h1>What about existing image links?</h1>
				<p>All images currently uploaded will be moved and automatically redirected (301 redirection) and any links which currently exist will continue to do so. This will also work with search engines such as Google and no penalty will be applied.</p>
				<h1>How long will it take?</h1>
				<p>This really depends on how many images your website currently has. Once you start the process you will see the completed percentage and your website will be put into "maintenance mode" which means no one will be able to upload new files until the process has completed.</p>
				<div style="text-align: center;"><a id="update-button" href="'.absolute_to_url(__FILE__).'?do_update">Update now</a></div>';
		}
		
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM__); ?>style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM__); ?>favicon.ico" rel="shortcut icon" type="image/x-icon" />
<script type="text/javascript" language="javascript" src="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM_JS__); ?>jquery.min.js"></script>

<title><?php echo $doctitle; ?></title>

<?php if(isset($_GET['do_update'])) : ?>
<script type="text/javascript">
$(function (){
	// update total uploads count
	var ajax_update = function () {
		if(!$("#update-status").length > 0) return false;
		$.ajax({url: "<?php echo absolute_to_url(__FILE__); ?>?ajax-update",
			success: function(response) {
				if(response.status==403) {
					console.log(response.status_txt);
				} else {
					document.title = "Update in progress - "+response.completed;
					$("#percent").text(response.completed);
					$("#update-caption").text(response.status_txt);
					$("#current-progress").animate({width: response.completed}, 0);
				}
				if(parseInt(response.completed)<100) {
					ajax_update();
				} else {
					setTimeout(function () {
						window.location = "<?php echo absolute_to_url(__FILE__); ?>?completed";
					}, 1000); // Wait 1sec
				}
			}
		});		
	}
	ajax_update();
	
})
</script>
<?php endif; ?>

</head>

<body>

<div id="main">
	<div id="top"><a href="<?php echo absolute_to_url(__FILE__); ?>"><img src="<?php echo absolute_to_relative(__CHV_PATH_SYSTEM__); ?>img/logo.png" id="logo" alt="Chevereto"/></a> <span id="update_tool">Update 2.0.X to 2.5</span></div>
    <div id="update_content"><?php echo $content; ?></div>
</div>

</body>
</html>