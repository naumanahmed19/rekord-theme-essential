<?php
/**
 * Plugin Name: Rekord API
 * Plugin URI: 
 * Description: API for Rekord WordPress Theme
 * Version: 1.3
 * Author: xvelopers
 * Author URI: http://xvelopers.com
 */
include ( __DIR__ . '/acf.php');
include ( __DIR__ . '/helpers.php');

include ( __DIR__ . '/RekordArtistsController.php');
include ( __DIR__ . '/RekordTracksController.php');
include ( __DIR__ . '/RekordAlbumsController.php');

include ( __DIR__ . '/RekordExploreController.php');


function rekord_api_get_explore($post_type){
    $response = new RekordExploreController();
    return  $response->get();
}


function rekord_api_get_albums() {
    
    $albums = new RekordAlbumsController();
	$posts = rekord_api_get_posts('album');
	$data =  $albums->data($posts);
	return [
		'data' => $data,
		'status' => 200
	];
}

function rekord_api_get_tracks() {
    $tracks = new RekordTracksController();
	$posts = rekord_api_get_posts('track');
	$data =  $tracks->data($posts);
	return [
		'data' => $data,
		'status' => 200
	];
}

function rekord_api_get_artists() {
    $artists = new RekordArtistsController();
	$posts = rekord_api_get_posts('artist');
	$data =  $artists->data($posts);
	return [
		'data' => $data,
		'status' => 200
	];
}





function rekord_api_get_taxonomy(){
	$type = !empty($_GET['type']) ? $_GET['type'] : '';
	$terms = get_terms($type); 

	return $terms;
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

	
	$routes = ['explore','posts','albums','artists','tracks','taxonomy'];
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




