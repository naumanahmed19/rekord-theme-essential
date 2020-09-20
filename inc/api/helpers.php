<?php

function rekord_get_post_media($id){
    $media = [];
    $media['thumbnail'] = get_the_post_thumbnail_url($id, 'thumbnail');
    $media['medium'] = get_the_post_thumbnail_url($id, 'medium');
    $media['large'] = get_the_post_thumbnail_url($id, 'large');
    $media['cover'] = rekord_get_field('cover',$id)['url']  ;
    return $media;
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
