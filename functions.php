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
	$img = str_replace('data:image/jpeg;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);

	$file_name = uniqid() . '.jpg';

	$upload_dir = wp_upload_dir();
	$upload_path = $upload_dir['basedir']. '/merged/';
	$upload_url = $upload_dir['baseurl'];

	if (!is_dir($upload_path)) {
	  // dir doesn't exist, make it
	  mkdir($upload_path);
	}

	$success = file_put_contents($upload_path . $file_name, $data);
	$img_url = $upload_url . '/merged/' . $file_name;
	if ($success) {
		wp_send_json(array('status' => 'success', 'data' => $img_url));
	} else {
		wp_send_json(array('status' => 'fail', 'data' => $success));
	}
}
?>