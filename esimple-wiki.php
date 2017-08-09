<?php
/*
Plugin Name: eSimple Wiki
Plugin URI: http://quirm.net
Description: an extremely simple and basic Wiki
Version: 0.6
Author: Rich Pedley
Author URI: http://www.elfden.co.uk/
*/
//admin and setup
load_plugin_textdomain('esimplewiki', false, dirname( plugin_basename( __FILE__ ) ) );
add_action('init', 'ewiki_init');
function ewiki_init(){
  $labels = array(
    'name' => __('Wiki', 'esimplewiki'),
    'singular_name' => __('Wiki', 'esimplewiki'),
    'add_new' => __('Add New', 'esimplewiki'),
    'add_new_item' => __('Add New Wiki page','esimplewiki'),
    'edit_item' => __('Edit Wiki page','esimplewiki'),
    'new_item' => __('New Wiki page','esimplewiki'),
    'view'        => __( 'View Wiki', 'bbpress' ),
    'view_item' => __('View Wiki page','esimplewiki'),
    'search_items' => __('Search Wiki','esimplewiki'),
    'not_found' =>  __('No Wiki pages found','esimplewiki'),
    'not_found_in_trash' => __('No Wiki pages found in Trash','esimplewiki'), 
    'parent_item_colon' =>  __( 'Parent Wiki:', 'bbpress' )
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'wiki',
	'capabilities' => array(
		'delete_others_posts' => 'delete_others_wikis',
		'delete_post' => 'delete_wiki',
		'delete_posts' => 'delete_wikis',
		'delete_private_posts'=>'delete_private_wikis',
    	'delete_published_posts'=>'delete_published_wikis',
		'edit_others_posts' => 'edit_others_wikis',
		'edit_post' => 'edit_wiki',
		'edit_posts' => 'edit_wikis',
		'edit_published_posts' => 'edit_published_wikis',
		'edit_private_posts'=>'edit_private_wikis',
		'publish_posts' => 'publish_wikis',
		'read_post' => 'read_wiki',
		'read_private_posts' => 'read_private_wikis',
	),
    'hierarchical' => true,
    'menu_position' => '100',
    'register_meta_box_cb' => 'ewiki_meta_box',
    'supports' => array('title','editor','author','thumbnail','comments','revisions','page-attributes')
  ); 
  register_post_type('wiki',$args);

   $PrivateRole = get_role('administrator');
    $roles=array(
    'delete_others_wikis',
    'delete_private_wikis',
    'delete_published_wikis',
    'delete_wiki',
    'delete_wikis',
    'edit_others_wikis',
    'edit_private_wikis',
    'edit_published_wikis',
    'edit_wiki',
    'edit_wikis',
    'publish_wikis',
    'read_wiki',
    'read_private_wikis',
  	);
  	foreach ($roles as $role){
   		$PrivateRole -> add_cap($role);
   	}

  //might use this in future, but not right now.
 // register_taxonomy("Section", array("wiki"), array("hierarchical" => true, "label" => "Sections", "singular_label" => "Section", "rewrite" => true));
  flush_rewrite_rules();
}

//add filter to insure the text wiki is displayed when user updates a wiki 
add_filter('post_updated_messages', 'ewiki_updated_messages');
function ewiki_updated_messages( $messages ) {
	if(!isset($post_ID))$post_ID='';
	if(!isset($post->post_date))$post->post_date='';
  $messages['wiki'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Wiki updated. <a href="%s">View wiki</a>','esimplewiki'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.','esimplewiki'),
    3 => __('Custom field deleted.','esimplewiki'),
    4 => __('Wiki updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Wiki page restored to revision from %s','esimplewiki'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Wiki page published. <a href="%s">View wiki page</a>','esimplewiki'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Wiki page saved.','esimplewiki'),
    8 => sprintf( __('Wiki page submitted. <a target="_blank" href="%s">Preview Wiki page</a>','esimplewiki'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Wiki page scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Wiki page</a>','esimplewiki'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i','esimplewiki' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Wiki page draft updated. <a target="_blank" href="%s">Preview Wiki page</a>','esimplewiki'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

//display contextual help for wiki
add_filter( 'contextual_help', 'ewiki_help_text', 10, 3 );

function ewiki_help_text($contextual_help, $screen_id, $screen) { 
  //$contextual_help .= var_dump($screen); // use this to help determine $screen->id
  if ('wiki' == $screen->id ) {
    $contextual_help =
      '<p>' . __('Things to remember when adding or editing a wiki page:','esimplewiki') . '</p>' .
      '<ul>' .
      '<li>' . __('<code>[wikilist]</code> can be used anywhere on the site, and lists all the wiki pages. To list sub pages of a section add the attribute <code>sub=\'\'</code>, and to list these elsehwere just add in the id of the parent page, <code>sub=\'532\'</code>','esimplewiki') . '</li>' .
      '<li>' . __('Create simple, elegant footnotes in your wiki. Use the <code>[ref]</code> shortcode and the plugin takes care of the rest. Example usage: <code>Lorem ipsum. [ref]My note.[/ref]</code>','esimplewiki') . '</li>' .
      '<li>' . __('Automatic generation of a wiki-style menu of links to any headings in the page content.','esimplewiki') . '</li>' .
      '<li>' . __(' Simply put [[ and ]] around a post title within your post or excerpt and Interlinks will turn it into a link for you. You can also use the more advanced wiki style <code>[[Post Title|This is a link to a post]]</code>. If a link is red then the page does not exist.','esimplewiki').'</li>' .
       '<li>' . __('<code>[wikiupdate]</code> can be used to indicate a page that needs updating. ','esimplewiki').'</li>' .
	 '</ul>' .
      '<p><strong>' . __('For more information:','esimplewiki') . '</strong></p>' .
      '<ul>' .
      '<li>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>','esimplewiki') . '</li>' .
      '<li>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>','esimplewiki') . '</li>' .
      '<li>' . __('<a href="http://quirm.net/forum/" target="_blank">Quirm.net Forums</a>','esimplewiki') . '</li>' .
      '</ul>';
  }
  return $contextual_help;
  
}
//change text for revision screen
add_filter('_wp_post_revision_fields','wiki_revision');
function wiki_revision($fields){
	if( get_post_type() != 'wiki' ) return $fields;
	$fields = array(
			'post_title' => __( 'Title' ,'esimplewiki'),
			'post_content' => __( 'Content' ,'esimplewiki'),
			'post_excerpt' =>  __( 'Reason for Edit','esimplewiki' ),
		);
	return $fields;
}
function ewiki_meta_box(){
//WP revisions doesn't store post meta, so i'll use the excerpt instead - funky ;)
	add_meta_box('postexcerpt', __('Edit Reason', 'esimple_wiki'), 'esw_post_excerpt_meta_box', 'wiki', 'normal', 'high');

}
function esw_post_excerpt_meta_box($post) {
	//display last reason
	echo '<div>'.nl2br(sprintf( __('Last reason: %s','esimplewiki'),$post->post_excerpt)).'</div>';
	//and allow a new reason to be added.
?>
<label class="screen-reader-text" for="excerpt"><?php _e('Edit Reason') ?></label><textarea rows="1" cols="40" name="excerpt" id="excerpt"></textarea>
<?php
}


//end of admin side
//the main wiki class
class ewiki_magic {
    var $footnotes = array();
    function ewiki_magic() {
		add_shortcode( 'ref', array( &$this, 'shortcode_ref' ) );
		add_shortcode('wikimenu', array( &$this, 'shortcode_menu' ));
		add_shortcode('wikilist', array( &$this, 'shortcode_wikilist' ));
		add_shortcode('wikiupdate', array( &$this, 'shortcode_update' ));
       	add_filter( 'the_content', array( &$this, 'the_content' ), 12 );
    }
    function shortcode_update($atts,$content=''){
    	if($content!='')
    		$text=$content;
    	else
    		$text=__('Page in need of Updating','esimplewiki');
    	
    	return '<p class="wikiupdate">'.$text.'</p>';
    }
    function shortcode_menu(){
    	//needs special parsing
		return null;
	}
	function shortcode_wikilist($atts, $content = null){
		//simple listing of wiki pages - hopefully heirarchal
		global $post;
		extract(shortcode_atts(array('sub'=>'no'), $atts));
		if($sub=='no')
			$childof='';
		elseif($sub!='' && is_numeric($sub))
			$childof=$sub;
		else
			$childof=$post->ID;
		$custom_post_type = 'wiki';
		$args=array(
		  'post_type' => $custom_post_type,
		  'sort_column'=>'menu_order',
		  'echo'=>0,
		   'title_li'     => ''
		);
		if($childof!=''){
			$args['child_of']=$childof;
		}
		$list='<ul>'.wp_list_pages($args).'</ul>';
		return $content.$list;
	}
    function shortcode_ref( $atts, $content = null ) {
    	//the footnotes
        global $id;
        if ( null === $content )
            return;
        if ( ! isset( $this->footnotes[$id] ) )
            $this->footnotes[$id] = array( 0 => false );
        $this->footnotes[$id][] = $content;
        $note = count( $this->footnotes[$id] ) - 1;
        return ' <a class="wiki-footnote" title="' . esc_attr( wp_strip_all_tags( $content ) ) . '" id="wiki-rn-' . $id . '-' . $note . '" href="#wikiNote-' . $id . '-' . $note . '"><sup>' . $note . '</sup></a>';
    }

    function the_content( $content ) {
        global $id;
        if( get_post_type() != 'wiki' ) return $content;
        //parse for the footnotes
		if ( !empty( $this->footnotes[$id] ) ){
			$content .= '<div class="wiki-footnotes"><h2 class="wikiNotes">'.__('Notes','esimplewiki').'</h2><ol>';
			foreach ( array_filter( $this->footnotes[$id] ) as $num => $note )
				$content .= '<li id="wikiNote-' . $id . '-' . $num . '">' . do_shortcode( $note ) . ' <a href="#wiki-rn-' . $id . '-' . $num . '">&#8617;</a></li>';
			$content .= '</ol></div>';
		}
        //parse for the TOC
		if(strpos($content, "[wikimenu]") === FALSE){
			preg_match_all("/(<(h[\w]+)[^>]*>)(.*?)(<\/\\2>)/", $content, $matches, PREG_SET_ORDER);
			$headers=array("h1","h2","h3","h4","h5");
			$c=0;
			$replace=array(" ","/","&","#",";",":");
			foreach($matches as $match){
				if(in_array(trim($match[2]), $headers)){
					$theIDs[$c]['id']=str_replace($replace, "-", $match[3]);
					$theIDs[$c]['tag']=$match[2];
					$theIDs[$c]['name']=$match[3];
					$temp = str_replace($match[1], $match[1]."<span id=\"".str_replace($replace, "-", $match[3])."\">", $match[0]);
					$temp = str_replace($match[4], "</span>".$match[4], $temp);
					$content = str_replace($match[0], $temp, $content);
				}
				$c++;
			}
			if(isset($theIDs[0]['id'])){
				$wikiblock = '<div id="wikinav"><strong>Content</strong><ul class="wikitoggle">';
				for($i=0; $i<sizeof($theIDs); $i++){
					$wikiblock .= "<li class=\"".$theIDs[$i]['tag']."\"><a href=\"#".$theIDs[$i]['id']."\">".$theIDs[$i]['name']."</a></li>";
				}
				$wikiblock .= "</ul></div>";
				$content = $wikiblock.$content;//."<div id=\"wikiback\"><a href=\"#wikinav\">back to top</a></div>";
			}
		}

        //parse for the [[..]] links
        if(strpos($content, "[[")){
			preg_match_all('/(\[\[.+?\]\])/',$content,$wikilinks, PREG_SET_ORDER);
			foreach ($wikilinks as $val) {
				if(strpos($val[0], "|")){
					$pieces = explode("|", $val[0]);
					$new_val = preg_replace('/\[\[(.+?)/', '$1', $pieces[0]);
					$link_text = preg_replace('/(.+?)\]\]/', '$1', $pieces[1]);
					$post_id = $this->interreplace($new_val);
					if($post_id == 0){ $content2 = '<span class="unwiki">'.$new_val.'</span>'; }
					$permalink = get_permalink($post_id);
					if($post_id){ $content2 = "<a href='$permalink'>$link_text</a>"; }
					$content = str_replace($val, $content2, $content);
				}else{
					$new_val = preg_replace('/\[\[(.+?)\]\]/', '$1', $val[0]);
					$post_id = $this->interreplace($new_val);
					if($post_id == 0){ $content2 = '<span class="unwiki" title="'.__('Wiki page not available','esimplewiki').'">'.$new_val.'</span>'; }
					$permalink = get_permalink($post_id);
					if($post_id){ $content2 = "<a href='$permalink'>$new_val</a>"; }
					$content = str_replace($val, $content2, $content);
				}
			}
		}
       
        
        return $content;
    }
    function interreplace($val){
    	//find the matching wiki pages
	      global $wpdb, $user_ID;
	      $table_name = $wpdb->prefix . "posts";
	      $val = $wpdb->escape($val);
	      $post_id = $wpdb->get_var("SELECT ID FROM $table_name WHERE post_title = '$val' AND post_status='publish' AND post_type='wiki'");
	      if(!$post_id){ return 0; }
	      else { return $post_id; }
	}
}
new ewiki_magic();

//adding these to the front end
if ( !is_admin() ) {
add_action('wp_print_scripts', 'ewiki_js');
add_action('wp_print_styles', 'ewiki_style');
}
function ewiki_js() {
	wp_register_script('WikiTog', WP_PLUGIN_URL.'/esimple-wiki/wikitog.js', array('jquery'));
	wp_enqueue_script('WikiTog');
}

function ewiki_style() {
	wp_register_style('WikiStyle', WP_PLUGIN_URL.'/esimple-wiki/wiki.css');
	wp_enqueue_style( 'WikiStyle');
}

//randomise
function ewiki_random() {
 	global $wpdb;
 	$query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'wiki' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
 	$random_id = $wpdb->get_var( $query );
  	wp_redirect( get_permalink( $random_id ) );
 	exit;
}
if ( isset( $_GET['random'] ) )
 add_action( 'template_redirect', 'ewiki_random' );
 


//widgetise
function eswikiwidgets_init(){
	register_widget('eswiki_widget');
}
add_action("widgets_init", "eswikiwidgets_init");

class eswiki_widget extends WP_Widget {

	function eswiki_widget() {
		$widget_ops = array('classname' => 'eswiki_widget', 'description' => __('Displays a wiki search, optional link to an index page, and a random wiki page link','esimplewiki'));
		$this->WP_Widget('eswiki_widget', __('eSimple Wiki','esimplewiki'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$output='';
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$index = $instance['index'];
		$random = $instance['random'];
		$search = $instance['search'];
		if($search=='1'){
			$output.='
			    <form id="wikisearchform" method="get" action="'.get_bloginfo('url').'">
				<div>
					<input type="text" name="s" id="ws" size="20" />
					<input type="submit" value="'.__('Find','esimplewiki').'" />
					<input type="hidden" name="post_type" value="wiki" />

				</div>
				</form>';
		}
		if($index !='' || $random!='')
			$output.='<ul>';
		if($index!=''){
			$output.='<li><a href="'.get_permalink($index).'">'.get_the_title($index).'</a></li>';
		}
		if($random!=''){
			$eswr=add_query_arg('random','wiki',get_bloginfo('url').'/wiki/');
			$output.='<li><a href="'.$eswr.'">'.__('Random page','esimplewiki').'</a></li>';
		}
		if($index !='' || $random!='')
			$output.='</ul>';
		echo $before_widget;
		echo $before_title.$title.$after_title;
		echo $output;
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['index'] = strip_tags( $new_instance['index'] );
		$instance['random'] = strip_tags( $new_instance['random'] );
		$instance['search'] = strip_tags( $new_instance['search'] );
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','index'=>'','random'=>'' , 'search'=>'') );
		$title = strip_tags($instance['title']);
		$index = $instance['index'];
		$random = $instance['random'];
		$search=$instance['search'];
		?>
		 <p>
		    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title);?>" />
		 </p>
		 <p>
		  	<label for="<?php echo $this->get_field_id('search'); ?>"><?php _e('Show search form','esimplewiki'); ?></label>
		  	<select id="<?php echo $this->get_field_id('search'); ?>" name="<?php echo $this->get_field_name('search'); ?>">
		  	<option value="1"<?php selected( $search, '1' ); ?>><?php _e('Yes','esimplewiki'); ?></option>
		  	<option value=""<?php selected( $search, '' ); ?>><?php _e('No','esimplewiki'); ?></option>
			</select><br />
			<label for="<?php echo $this->get_field_id('index'); ?>"><?php _e('Index page link','esimplewiki'); ?></label>
			<input size="3" id="<?php echo $this->get_field_id('index'); ?>" name="<?php echo $this->get_field_name('index'); ?>" type="text" value="<?php echo esc_attr($index); ?>" />
			<br />
			<label for="<?php echo $this->get_field_id('random'); ?>"><?php _e('Show random page link','esimplewiki'); ?></label>
			<select id="<?php echo $this->get_field_id('random'); ?>" name="<?php echo $this->get_field_name('random'); ?>">
			<option value="1"<?php selected( $random, '1' ); ?>><?php _e('Yes','esimplewiki'); ?></option>
			<option value=""<?php selected( $random, '' ); ?>><?php _e('No','esimplewiki'); ?></option>
			</select>			
		</p>
	<?php
	}
}
add_filter( 'map_meta_cap', 'ewiki_map_meta_cap', 10, 4 );

function ewiki_map_meta_cap( $caps, $cap, $user_id, $args ) {

	/* If editing, deleting, or reading a wiki, get the post and post type object. */
	if ( 'edit_wiki' == $cap || 'delete_wiki' == $cap || 'read_wiki' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );

		/* Set an empty array for the caps. */
		$caps = array();
	}

	/* If editing a wiki, assign the required capability. */
	if ( 'edit_wiki' == $cap ) {
		if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->edit_posts;
		else
			$caps[] = $post_type->cap->edit_others_posts;
	}

	/* If deleting a wiki, assign the required capability. */
	elseif ( 'delete_wiki' == $cap ) {
		if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->delete_posts;
		else
			$caps[] = $post_type->cap->delete_others_posts;
	}

	/* If reading a private wiki, assign the required capability. */
	elseif ( 'read_wiki' == $cap ) {

		if ( 'private' != $post->post_status )
			$caps[] = 'read';
		elseif ( $user_id == $post->post_author )
			$caps[] = 'read';
		else
			$caps[] = $post_type->cap->read_private_posts;
	}

	/* Return the capabilities required by the user. */
	return $caps;
}
?>