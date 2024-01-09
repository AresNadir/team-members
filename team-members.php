<?php
/*
Plugin Name: Team members
Description: Add team members plugin use the shortcode [team_members category="category-slug"] to display members by category
Version: 1.0
Author: Ares Ioakimidis
*/


add_action('init', 'register_team_member_type');
function register_team_member_type() {
  $args = array(
      'public' => true,
      'label'  => 'Team Members',
      'supports' => array('title', 'editor', 'thumbnail'), 
      'menu_icon' => 'dashicons-admin-users'
  );
  register_post_type('team_member', $args);
}



function create_my_custom_taxonomy() {
  $labels = array(
    'name' => _x('Categories', 'taxonomy general name'),
  );
  register_taxonomy('my_custom_category', array('team_member'), array(
    'hierarchical' => true,
    'labels'       => $labels,
  ));
}

add_action('init', 'create_my_custom_taxonomy', 0);

function my_custom_meta_boxes() {
  add_meta_box('position_meta_box', 'Position', 'position_meta_box_callback', 'team_member', 'normal', 'high');
}

add_action('add_meta_boxes', 'my_custom_meta_boxes');

function position_meta_box_callback($post) {
    echo '<input type="text" name="position_field" id="position_field" value="' . get_post_meta($post->ID, 'position_field', true) . '" />';
}

add_action('save_post', 'save_my_custom_meta_box_data');

function save_my_custom_meta_box_data($post_id) {
    update_post_meta($post_id, 'position_field', sanitize_text_field($_POST['position_field']));
}

function display_team_members($atts) {
  $atts = shortcode_atts(array(
      'category' => '',
  ), $atts);

  $output = '<div class="multiwave-team-members_container">';

  // WP_Query arguments
  $args = array(
    'post_type' => 'team_member',
    'posts_per_page' => -1,
    'orderby' => 'date', 
    'order'   => 'ASC',
    'tax_query' => array(
        array(
            'taxonomy' => 'my_custom_category',
            'field'    => 'slug',
            'terms'    => $atts['category'],
        ),
    ),
);

  $query = new WP_Query($args);

  // The Loop
  if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $position = get_post_meta(get_the_ID(), 'position_field', true);
        $featured_image = get_the_post_thumbnail(get_the_ID(), 'full');
        $post_content = apply_filters('the_content', get_the_content());
        $random_id = uniqid('view_more_', false);
        $short_description = get_field('mw_team_short_description');

        $output .= '<div class="multiwave-team-member">';
        if ($featured_image) {
            $output .= '<div class="member-image">' . $featured_image . '</div>';
        }
        $output .= '<h3>' . get_the_title() . '</h3>';
        $output .= '<p class="member-position">' . $position . '</p>';
        $output .= '<p class="member-description">' . $short_description . '</p>';
        if( '' !== get_post()->post_content ) {
          $output .='<a class="read-more-link" data-modal-id="myModal'.$random_id.'">Read More</a>
                      <div id="myModal'.$random_id.'" class="modal">
                        <div class="modal-content">
                          <div class="modal-texts">
                            <div class="modal-close_btn" id="modal_close_btn_container">
                              <img class="close" src="ADD IMAGE URL">
                            </div>
                            <div>
                              <h4>' . get_the_title() . '</h4>
                            </div>
                            <div>
                              <h5>'.$position.'</h5>
                            </div>
                            <div>
                              <p>'.$post_content.'</p>
                            </div>
                          </div>
                        </div>
                      </div>';
        }
        $output .= '</div>';
    }
} else {
    $output .= '<p>No team members found.</p>';
}

  wp_reset_postdata();

  $output .= '</div>
  ';

  return $output;
}

function my_custom_scripts() {
  wp_enqueue_script('modal-script', plugins_url('js/script.js', __FILE__), array(), '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'my_custom_scripts');
wp_enqueue_style('team-members-style', plugins_url('css/style.css', __FILE__));

add_shortcode('team_members', 'display_team_members');
