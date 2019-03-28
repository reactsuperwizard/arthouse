<?php
add_action('wp_enqueue_scripts', 'rsm_enqueue_script');
function rsm_enqueue_script() {
	wp_localize_script( 'jquery', 'ajax_url', admin_url('admin-ajax.php') );
	wp_enqueue_script('jquery');
}

add_action('wp_ajax_rsm_save_image', 'rsm_upload_image');
// add_action('wp_ajax_nopriv_rsm_save_image', 'rsm_upload_image');

function rsm_upload_image() {
	if ( empty($_POST['imgBase64']) ) {
		echo 'wrong data';
		return;
	}
	$img = $_POST['imgBase64'];
	$img = str_replace('data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$file_name = uniqid() . '.png';

	$dir = wp_upload_dir();
	$upload_dir = $dir['basedir']. '/merged/';
	if (!is_dir($upload_dir)) {
	  // dir doesn't exist, make it
	  mkdir($upload_dir);
	}

	$success = file_put_contents($upload_dir . $file_name, $data);
	print $success ? $file : 'Unable to save the file.';
}
?>