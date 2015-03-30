<?php
/*
Plugin Name: WP Multisite Most Commented Posts
Plugin URI: http://www.termel.fr
Description: Show most commented posts across multisite install : Widget & [SHORTCODE]
Version: 1.1
Author: Termel
Author URI: http://www.termel.fr
License: GPL2
*/

class wp_multisite_popular_posts extends WP_Widget {
  
  // constructor
  function wp_multisite_popular_posts() {
	$widget_ops = array('classname' => 'wp_multisite_popular_posts', 'description' => __('Add this widget to display most popular posts across the network', 'wp_mpp'));
    $control_ops = array('width' => 400, 'height' => 300);
	
	parent::WP_Widget(false, $name = __('WP Multisite Popular Posts', 'wp_mpp'),$widget_ops/*,$control_ops*/ );
  
  	$this->register_plugin_styles();
	add_shortcode( 'wp_mpp', array($this, 'wp_multisite_popular_posts_shortcode_fn' ));
  }
  
  public function register_plugin_styles() {
	  
	  wp_register_style( 'wp_multisite_popular_posts', plugins_url( 'wp-multisite-popular-posts/css/wp-multisite-popular-posts.css' ) );
	  wp_enqueue_style( 'wp_multisite_popular_posts' );
	}
	

	
  // widget form creation
  function form($instance) {
	
	// Check values
	if( $instance) {
	  $title = esc_attr($instance['title']);
	  $max_comments = esc_attr($instance['max_comments']);
	  $time_slot = esc_attr($instance['time_slot']);
	  $show_nb_of_comments = esc_attr($instance['show_nb_of_comments']);
	  $show_total_nb_of_posts = esc_attr($instance['show_total_nb_of_posts']);
	}
	else {
	  $title = '';
	  $max_comments = '10';
	  $time_slot = 'ever';
	  $show_nb_of_comments ='';
	  $show_total_nb_of_posts ='';
	}
?>

<p>
  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_multisite_popular_posts');?></label>
  <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id('max_comments'); ?>"><?php _e('Select max comments', 'wp_widget_multisite_popular_posts');?></label>
  <select name="<?php echo $this->get_field_name('max_comments'); ?>" id="<?php echo $this->get_field_id('max_comments'); ?>" class="widefat">
	<?php
	$options = array('1', '2', '3','4','5','6','7','8','9','10');
	foreach ($options as $option) {
	  echo '<option value="' . $option . '" id="' . $option . '"', $max_comments == $option ? ' selected="selected"' : '', '>', $option, '</option>';
	}
	?>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id('time_slot'); ?>"><?php _e('Select time frame', 'wp_widget_multisite_popular_posts');
	?></label>
  <select name="<?php echo $this->get_field_name('time_slot'); ?>" id="<?php echo $this->get_field_id('time_slot'); ?>" class="widefat">
	<?php
	$options = array('ever', 'lastmonth', 'currentmonth','lastweek','currentweek');
	foreach ($options as $option) {
	  echo '<option value="' . $option . '" id="' . $option . '"', $time_slot == $option ? ' selected="selected"' : '', '>', $option, '</option>';
	}
	?>
  </select>
</p>
<p>
  <input id="<?php echo $this->get_field_id('show_nb_of_comments'); ?>" name="<?php echo $this->get_field_name('show_nb_of_comments'); ?>" type="checkbox" value="1" <?php checked( '1', $show_nb_of_comments ); ?> />
<label for="<?php echo $this->get_field_id('show_nb_of_comments'); ?>"><?php _e('Show number of comments', 'wp_widget_multisite_popular_posts'); ?></label>
</p>
<p>
  <input id="<?php echo $this->get_field_id('show_total_nb_of_posts'); ?>" name="<?php echo $this->get_field_name('show_total_nb_of_posts'); ?>" type="checkbox" value="1" <?php checked( '1', $show_total_nb_of_posts ); ?> />
<label for="<?php echo $this->get_field_id('show_total_nb_of_posts'); ?>"><?php _e('Show total posts', 'wp_widget_multisite_popular_posts'); ?></label>
</p>
<?php
  }


// widget update
function update($new_instance, $old_instance) {
  $instance = $old_instance;
  // Fields
  $instance['title'] = strip_tags($new_instance['title']);
  $instance['max_comments'] = strip_tags($new_instance['max_comments']);
  $instance['time_slot'] = strip_tags($new_instance['time_slot']);
   $instance['show_nb_of_comments'] = strip_tags($new_instance['show_nb_of_comments']);
   $instance['show_total_nb_of_posts'] = strip_tags($new_instance['show_total_nb_of_posts']);
  
  return $instance;
}

// widget display
function widget($args, $instance) {
  extract( $args );
  // these are the widget options
  $title = apply_filters('widget_title', $instance['title']);
  $max_comments = $instance['max_comments'];
  $time_slot = $instance['time_slot'];
  $show_comments = $instance['show_nb_of_comments'];
  $show_posts = $instance['show_total_nb_of_posts'];
  
  echo $before_widget;
  // Display the widget
  echo '<div class="widget-text wp_widget_plugin_box">';
  
  // Check if title is set
  if ( $title ) {
	echo $before_title . $title . $after_title;
  }
   
  $attrs = array(
        'max' => $max_comments,
        'type' => $time_slot,
  		'show_comments' => $show_comments,
  		'show_posts' => $show_posts,
  );
  
  
  echo $this->wp_multisite_popular_posts_shortcode_fn($attrs);
  
  echo '</div>';
  echo $after_widget;

}
  
  function wp_multisite_popular_posts_shortcode_fn($attributes) {
		
	  //echo "chiefeditor_shortcode_function";
	  // get optional attributes and assign default values if not present
    extract( shortcode_atts( array(
        'max' => '10',
        'type' => 'lastmonth',
	  	'show_comments' => 'true',
  		'show_posts' => 'false',
    ), $attributes ) );
	  
	  
	  //echo "$max   $type";
	  
	  if ($type == 'lastmonth') {
		//$last_month_most_commented = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));
		//$current_month = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$startDate = date('Y-m-d', strtotime('today - 30 days'));//date('Y-m-01 H:i:s', $last_month_most_commented );
		$endDate = date('Y-m-d', strtotime('today'));//date('Y-m-01 H:i:s', $current_month);
		$mostCommentedPosts = $this->getMostCommentedPosts($max,$startDate,$endDate,$show_comments,$show_posts);
	  } else if ($type == 'currentmonth') {
		//$current_month = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$current_month = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$startDate = date('Y-m-01 H:i:s', $current_month );
		$endDate = date('Y-m-t H:i:s', $current_month);
		$mostCommentedPosts = $this->getMostCommentedPosts($max,$startDate,$endDate,$show_comments,$show_posts);
	  } else if ($type == 'lastweek') {
		//$weekNumber = date("W");
		//$week = max($weekNumber - 1,1);
		//$currentYear = date("Y");	
		//$weekArray = $this->getStartAndEndDate($week,$currentYear);
		//$startDate = $weekArray['week_start'];
		//$endDate = $weekArray['week_end'];		
		$startDate = date('Y-m-d', strtotime('today - 7 days'));//date('Y-m-01 H:i:s', $last_month_most_commented );
		$endDate = date('Y-m-d', strtotime('today'));
		$mostCommentedPosts = $this->getMostCommentedPosts($max,$startDate,$endDate,$show_comments,$show_posts);
	  } else if ($type == 'currentweek') {
		$weekNumber = date("W");
		
	  	$currentYear = date("Y");	
		$weekArray = $this->getStartAndEndDate($weekNumber,$currentYear);
		$startDate = $weekArray['week_start'];
		$endDate = $weekArray['week_end'];	
	  } else if ($type == 'ever') {
	  	$mostCommentedPosts = $this->getMostCommentedPosts($max,NULL,NULL,$show_comments,$show_posts);
	  } else {
		$mostCommentedPosts = $this->getMostCommentedPosts($max);
	  }
	  
	  return $mostCommentedPosts;
	}
  
  function getStartAndEndDate($week, $year) {
	  $dto = new DateTime();
	  $dto->setISODate($year, $week);
	  $ret['week_start'] = $dto->format('Y-m-d');
	  $dto->modify('+6 days');
	  $ret['week_end'] = $dto->format('Y-m-d');
	  return $ret;
	}
  
  function postBetweenDates($post,$startDate,$endDate){
	
	  //$post_date = $post->post_date;
	  	$format = 'Y-m-d';
		$postDate = get_the_time($format,$post->ID);
		$post_date = new DateTime($postDate);
		$start_date = new DateTime($startDate);
		$end_date = new DateTime($endDate);
			
		if ($post_date >= $start_date && $post_date <= $end_date) {
			  
			  return true;
		} else {
			return false;
		}
	}
  
  function getAllPostsOfAllBlogs($startDate = NULL, $endDate = NULL) {
	  
	  $network_sites = wp_get_sites();
	  
	  $result = array();
	  foreach ( $network_sites as $network_site ) {
		
		$blog_id = $network_site['blog_id'];
		
		switch_to_blog($blog_id);
		
		$allPostsOfCurrentBlog = get_posts(array(
		  'numberposts' => -1, 
		  'post_type' => 'post',
		  'post_status' => array('publish','future')
		));
		
		if ($startDate != NULL && $endDate != NULL) {
		
		foreach ($allPostsOfCurrentBlog as $post) {
		  if ($this->postBetweenDates($post,$startDate,$endDate)) {
		  	$result[$blog_id][] = $post;
		  } 
		}
		} else {
			$result[$blog_id] = $allPostsOfCurrentBlog;
		}
		
		// Switch back to the main blog
		restore_current_blog();
	  }
	  
	  
	  return $result;
	}
  
  public function get_comments_number_for_blog($blogid, $postid ){
	  
	  //echo "get_comments_number_for_blog : $blogid, $postid ";
	  switch_to_blog($blogid);
	  $result = get_comments_number( $postid );
	  restore_current_blog();
	  return $result;
	}
  
  public function getMostCommentedPosts($maxResults, $startDate = NULL, $endDate = NULL, $show_comments = NULL, $show_posts = NULL) {
	  
	  
	  $blog_posts_array = $this->getAllPostsOfAllBlogs($startDate,$endDate);
	  $postCommentsArray = array();
	  $postCommentsTitles = array();
	  $postCommentsPermalinks = array();
	  //echo 'count($blog_posts_array) '.count($blog_posts_array) ;
	  foreach ($blog_posts_array as $blogid => $postsOfBlog) {
		
		foreach ($postsOfBlog as $post) {
		  //echo "<br/>$blogid, $post->ID";
		  $nbOfComments = $this->get_comments_number_for_blog($blogid, $post->ID );
		  $postCommentsArray[$blogid .'_'.$post->ID] = $nbOfComments;
		  $postCommentsTitles[$blogid .'_'.$post->ID] = $post->post_title;
		  $postCommentsPermalinks[$blogid .'_'.$post->ID] = get_blog_permalink( $blogid, $post->ID );
		}
	  }
	
	if ($show_posts){
	  $result = '<h4 class="wmpp_title">'.__('Total number of posts accross network: ','wp_mpp').count($postCommentsArray).'</h4>';
	} else {
	  $result = '';
	}
	  $sortResult = arsort($postCommentsArray);
	  //echo '$sorted : '.count($postCommentsArray);
	  if ($sortResult) {
		
		if (!count($postCommentsArray)) {
			return __("Not enough data",'wp_mpp');
		}
		  
		$postComments = '<ol class="wmpp_list">';
		$idx = 1;
		foreach ($postCommentsArray as $key => $value) {
		  
		  if ($value) {
			$commentMsg = $value == 1 ? __("comment",'wp_mpp') : __("comments",'wp_mpp');
			$postComments .= '<li class="wmpp_list_item"><a target="_blank" href="'.$postCommentsPermalinks[$key].'">'.$postCommentsTitles[$key]. '</a>';
			if ($show_comments){
			  $postComments .= '<span class="wmpp_comment"> | '.$value.' '.$commentMsg.'</span></li>';
			}
			if ($idx == $maxResults) {
			  break;
			}
			$idx += 1;
		  }
		  
		}
		$postComments .= '</ol>';
		$result .= $postComments;
	  }
	  else {
		$result .= 'problem sorting...';
	  }
	  
	  return $result;
	}
  
  
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_multisite_popular_posts");'));

function wp_mpp_load_lang() {
  	$plugin_name =  'wp-multisite-popular-posts';
  $relative_path = dirname( plugin_basename( __FILE__ ) ) . '/languages' ;
  //echo $relative_path . '<br/>';
  if (load_plugin_textdomain( 'wp_mpp', false, $relative_path)) {
	//echo 'SUCCESS::loading lang file in :'.$relative_path;
  }
}
add_action( 'init', 'wp_mpp_load_lang');

?>