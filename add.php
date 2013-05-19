<?php
/**
 * code to handle blocking a user
 *
 * @package WordPress
 */

/*if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	header('Allow: POST');
	header('HTTP/1.1 405 Method Not Allowed');
	header('Content-Type: text/plain');
	exit;
}*/
/** Sets up the WordPress Environment. Would be nice to tidy this up. */
require( '../../../wp-load.php' ); 

if(isset($_POST['post-id'])) {
	
	$post = get_post($_POST['post-id']);

	$comment_IDs = array();
	
	$meta_exists = false;
	
	if(get_post_meta($post->ID, 'jas_top_comments', true) != '') {
		$meta_exists = true;
		$existing_comment_IDs = explode(',',get_post_meta($post->ID, 'jas_top_comments', true));
		foreach($existing_comment_IDs as $id) {
			array_push($comment_IDs, $id);
		}
	}

	if(isset($_POST['comment-id'])) {
		$comment_ID = $_POST['comment-id'];
		
		if($_POST['mode'] == 'add') {
			array_push($comment_IDs, $comment_ID);
		} elseif($_POST['mode'] == 'remove') {
			$index = array_search($comment_ID, $comment_IDs);
			array_splice($comment_IDs, $index, 1);
		}
		
		// now save that metadata value on the post
		$post_id = $_POST['post-id'];
		$meta_key = 'jas_top_comments';
		sort($comment_IDs);
		$meta_value = implode(',',array_unique($comment_IDs));
		
		if(sizeof($comment_IDs) == 0) {
			delete_post_meta($post_id, $meta_key, $meta_value);
		} else {
			if($meta_exists) {
				update_post_meta($post_id, $meta_key, $meta_value);
			} else {
				add_post_meta($post_id, $meta_key, $meta_value);
			}
		}
	}
	
	header("Location: ".$_SERVER['HTTP_REFERER'] .'#top-comments');
	
}
?>