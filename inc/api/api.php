<?php
/**
 * Plugin Name: Rekord API
 * Plugin URI: 
 * Description: API for Rekord WordPress Theme
 * Version: 1.1
 * Author: xvelopers
 * Author URI: http://xvelopers.com
 */


function rekord_api_get_albums() {

	$posts = rekord_api_get_posts('album');
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
	return [
		'data' => $data,
		'status' => 200
	];
}

function rekord_album_tracks($id){
	$args = rekord_relation_args('track' , 'track_albums',-1, $id);
	$posts = get_posts($args); //builtin method
	return rekord_data_tracks($posts);
}


function rekord_api_get_tracks() {


	$posts = rekord_api_get_posts('track');
	$data = rekord_data_tracks($posts);
	return [
		'data' => $data,
		'status' => 200
	];
}





function rekord_data_tracks($posts){

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

	$routes = ['posts','albums','tracks','taxonomy'];
	foreach($routes as $route){
		register_rest_route('wl/v1',str_replace("_","-",$route) , [
			'methods' => 'GET',
			'callback' => 'rekord_api_get_'.$route,
		]);
	}


	register_rest_route( 'wl/v1', 'posts/(?P<slug>[a-zA-Z0-9-]+)', array(
		'methods' => 'GET',
		'callback' => 'wl_post',
	) );
});
