<?php
/*
	Plugin Name: JAS Top Comments
	Description: A block containing user-selected top comments in a post
	Version: 0.1
	Author: Jonathan Sutcliffe
	Author URI: http://www.jonathansutcliffe.com
	License: GPL2

    Copyright 2013 Jonathan Sutcliffe (email : jonathan.a.sutcliffe@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*
Expected functionality:
1) Ability to add comments to a "top comments" area
2) Ability to remove comments from a "top comments" area
3) List comments in a sensible WP-standard way (this won't work for RPS)
4) Do not provide styling -- just use classes and IDs appropriately and leave look & feel up to the user
*/

function jas_top_comments() {
	global $post;
	
	if(get_post_meta($post->ID, 'jas_top_comments', true)) {
		$comment_IDs = explode(',',get_post_meta($post->ID, 'jas_top_comments', true));
	}

	if(sizeof($comment_IDs) > 0) {
		?>
		<div id="top-comments" class="block no-back">
			<h3>Top comments</h3>
			<ol class="commentlist">
				<?php
					foreach ($comment_IDs as $comment_ID) {
						$comment = get_comment($comment_ID);
						$GLOBALS['comment'] = $comment; 
						?>
						<li>
							<div id="top-comment-<?php echo $comment_ID; ?>">
								<div class="avatar-container">
									<?php echo get_avatar( $comment, 32 ); ?>
								</div>
								<div class="comment-content">
									<p>
										<span class="comment-author-vcard">
										<span class="comment-meta commentmetadata"><a style="color: #999" href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a>
										</span>
										<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
										</span>
									</p>
								<div>
									<?php echo nl2br($comment->comment_content); ?>
								</div>
                                <?php if ( current_user_can('edit_post', $post->ID) ) {
								?>
                                <form id="highlight-comment-<?php echo $comment_ID; ?>" method="POST" action="<?php echo plugins_url() . '/jas-top-comments/add.php'; ?>">
                                    <input type="hidden" value="remove" name="mode" />
                                    <input type="hidden" value="<?php echo $post->ID; ?>" name="post-id" />
                                    <input type="hidden" value="<?php echo $comment_ID; ?>" name="comment-id" />
                                    <input class="highlight-button" type="submit" value="Remove" />
                                </form>
								<?php
								}
								?>
							</div>
						</li>
						<?php
					} // end foreach
				?>
			</ol>
		</div>
	<?php } // end comment_IDs length > 0 
}

if (!is_feed()) {
	add_filter('comment_text', 'add_highlight_link', '1008'); //Low priority so other HTML can be added first
}

function add_highlight_link($content) {
	global $comment, $post;
	$url = plugins_url() . '/jas-top-comments/add.php';
	$highlightButton = '';
	if ( current_user_can('edit_post', $post->ID) ) {
	$highlightButton = '
	<form id="highlight-comment-'.$comment->comment_ID.'" method="POST" action="'.$url.'">
	<input type="hidden" value="add" name="mode" />
	<input type="hidden" id="post-id" value="'.$post->ID.'" name="post-id" />
	<input type="hidden" id="comment-id" value="'.$comment->comment_ID.'" name="comment-id" />
	<input class="highlight-button" name="comment-id'.$comment->comment_ID.'" id="highlight-'.$comment->comment_ID.'" type="submit" value="Highlight Comment" />
	
	</form>';
	}
	return $content . $highlightButton;
}

?>