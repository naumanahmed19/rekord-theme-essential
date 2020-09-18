<?php
/**
 * Plugin Name: Rekord API
 * Plugin URI: 
 * Description: API for Rekord WordPress Theme
 * Version: 1.3
 * Author: xvelopers
 * Author URI: http://xvelopers.com
 */


function rekord_api_get_albums() {

	$posts = rekord_api_get_posts('album');
	$data =  rekord_api_attach_album_fields($posts);

	
	return [
		'data' => $data,
		'status' => 200
	];
}


function rekord_api_attach_album_fields($posts){
	$data = [];
	$i = 0;
	if(!empty($posts)){
		foreach($posts as $post) {
			$data[$i]['id'] = $post->ID;
			$data[$i]['title'] = $post->post_title;
			$data[$i]['content'] = $post->post_content;
			$data[$i]['slug'] = $post->post_name;
			$data[$i]['media']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
			$data[$i]['media']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
			$data[$i]['media']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
			$data[$i]['media']['cover'] = rekord_get_field('cover',$post->ID)['url']  ;
			$data[$i]['tracks'] = rekord_album_tracks( $post->ID);
			
			$i++;
		}
	}
	return $data;
}




function rekord_album_tracks($id){
	$args = rekord_relation_args('track' , 'track_albums',-1, $id);
	$posts = get_posts($args); //builtin method
	return rekord_api_attach_track_fields($posts);
}


function rekord_api_get_tracks() {


	$posts = rekord_api_get_posts('track');
	$data =  rekord_api_attach_track_fields($posts);
	return [
		'data' => $data,
		'status' => 200
	];
}





function rekord_api_attach_track_fields($posts){

	$data = [];
	$i = 0;

	if(!empty($posts)){
		foreach($posts as $post) {


			$url =  rekord_get_field('track_upload',$post->ID)['url'];
			if(rekord_get_field('track',$post->ID) != 'upload'):
				$url = rekord_get_field('track_url',$post->ID)  ;
			  endif;   
		
			$data[$i]['id'] = $post->ID;
			$data[$i]['title'] = $post->post_title;
			$data[$i]['content'] = $post->post_content;
			$data[$i]['slug'] = $post->post_name;
			$data[$i]['type'] =  rekord_get_field('track',$post->ID);
			$data[$i]['stream_type'] =  rekord_get_field('stream_type',$post->ID);
			$data[$i]['url'] = $url ;

			if(get_the_post_thumbnail_url($post->ID, 'thumbnail')){
				$data[$i]['media']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
				$data[$i]['media']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
				$data[$i]['media']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
			}
		
			$data[$i]['time'] = rekord_get_field('track_time', $post->ID);
			$data[$i]['artist'] =  rekord_get_field('track_artists',$post->ID);
	

//$data[$i]['tracks'] = rekord_relation_args('track' , 'track_albums',-1);
			$i++;
		}
	}
	return $data;
}


function rekord_api_get_taxonomy(){
	$type = !empty($_GET['type']) ? $_GET['type'] : '';
	$terms = get_terms($type); 

	return $terms;
}


function rekord_api_get_posts($post_type){
	$paged = $_GET['paged'] ?$_GET['paged']: 1;
	$postsPerPage = !empty($_GET['numberposts'] )? $_GET['numberposts'] : 2;
	$postOffset = $paged * $postsPerPage;
	$args = array(
		'posts_per_page'  => $postsPerPage,
		//'category_name'   => $btmetanm,
		'offset'          => $postOffset,
		'post_type'       => $post_type
	);


	if(!empty($_GET['q'])){
		$args['s'] =  esc_attr( $_GET['q']);
		$args['posts_per_page'] = -1;
	}

	return  get_posts($args);
}


function rekord_api_get_explore($post_type){

	$data = [];
	$i = 0;

	$sections =  rekord_get_field('explore_screen', 'option');

	foreach($sections as $section){
		$data[$i]['title'] = $section['title'];
		$data[$i]['type'] = $section['select_post_type'];
		$data[$i]['number_of_post'] = $section['number_of_post'];


		$args = array(
			'posts_per_page'  => $section['number_of_post'],
			//'category_name'   => $btmetanm,
			'offset'          => $postOffset,
			'post_type'       =>  $section['select_post_type']
		);

		
		if($section['select_post_type'] == 'album'){
			$posts = get_posts($args);
			$data[$i]['posts'] = rekord_api_attach_album_fields($posts );
		}
		
		if($section['select_post_type'] == 'track'){

			$posts = rekord_api_get_posts('track');
		
			$data[$i]['posts'] =rekord_api_attach_track_fields($posts);
		}

		//$data['sections'][$i] = $section['select_post_type'];	

		$i++;
	}

	
	return [
		'data' => $data,
		'status' => 200
	];
}






function wl_post( $slug ) {
	$args = [
		'name' => $slug['slug'],
		'post_type' => 'post'
	];

	$post = get_posts($args);


	$data['id'] = $post[0]->ID;
	$data['title'] = $post[0]->post_title;
	$data['content'] = $post[0]->post_content;
	$data['slug'] = $post[0]->post_name;
	$data['media']['thumbnail'] = get_the_post_thumbnail_url($post[0]->ID, 'thumbnail');
	$data['media']['medium'] = get_the_post_thumbnail_url($post[0]->ID, 'medium');
	$data['media']['large'] = get_the_post_thumbnail_url($post[0]->ID, 'large');

	return $data;
}

add_action('rest_api_init', function() {

	
	$routes = ['explore','posts','albums','tracks','taxonomy'];
	foreach($routes as $route){
		register_rest_route('wl/v1', $route, [
			'methods' => 'GET',
			'callback' => 'rekord_api_get_'.$route,
		]);
	}


	register_rest_route( 'wl/v1', 'posts/(?P<slug>[a-zA-Z0-9-]+)', array(
		'methods' => 'GET',
		'callback' => 'wl_post',
	) );
});




if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_5f64bff58c3d2',
		'title' => 'Rekord Mobile API',
		'fields' => array(
			array(
				'key' => 'field_5f64c23da4525',
				'label' => 'Explore Screen',
				'name' => 'explore_screen',
				'type' => 'repeater',
				'instructions' => 'Add explore screen sections',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => '',
				'min' => 0,
				'max' => 0,
				'layout' => 'block',
				'button_label' => '',
				'sub_fields' => array(
					array(
						'key' => 'field_5f64c388a4526',
						'label' => 'Title',
						'name' => 'title',
						'type' => 'text',
						'instructions' => 'Add section title',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5f64c5cad50c7',
						'label' => 'Select Post Type',
						'name' => 'select_post_type',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'album' => 'album',
							'track' => 'track',
							'event' => 'event',
							'podcast' => 'podcast',
						),
						'default_value' => array(
						),
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					),
					array(
						'key' => 'field_5f64c657d50c8',
						'label' => 'Number of Post',
						'name' => 'number_of_post',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 5,
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5f64c70198043',
						'label' => 'Select A category',
						'name' => 'select_a_category',
						'type' => 'taxonomy',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'taxonomy' => 'category',
						'field_type' => 'select',
						'allow_null' => 0,
						'add_term' => 0,
						'save_terms' => 0,
						'load_terms' => 0,
						'return_format' => 'id',
						'multiple' => 0,
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'podcast-rss-settings',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));
	
	endif;