<?php
class RekordExploreController{

    // public function data($posts){
    //     $data = [];
    //     $i = 0;
    //     if(!empty($posts)){
    //         foreach($posts as $post) {
    //             $data[$i]['id'] = $post->ID;
    //             $data[$i]['title'] = $post->post_title;
    //             $data[$i]['content'] = $post->post_content;
    //             $data[$i]['slug'] = $post->post_name;
    //             $data[$i]['media'] = rekord_get_post_media($post->ID);
    //             $data[$i]['tracks'] = $this->rekord_album_tracks( $post->ID);
                
    //             $i++;
    //         }
    //     }
    //     return $data;
    // }

    // public function rekord_album_tracks($id){
    //     $args = rekord_relation_args('track' , 'track_albums',-1, $id);
    //     $posts = get_posts($args); //builtin method
    //     return rekord_api_attach_track_fields($posts);
    // }


    public function get(){
       
            $data = [];
            $i = 0;


            $albums = new RekordAlbumsController();


            $tracks = new RekordTracksController();


            $sections =  rekord_get_field('r_explore_screen', 'option');

            foreach($sections as $section){

                $data[$i]['title'] = $section['r_title'];
                $data[$i]['type'] = $section['r_post_type'];
                $data[$i]['number_of_post'] = $section['r_number_of_post'];
                $data[$i]['style'] = $section['style'];


                $args = array(
                    'posts_per_page'  => $section['r_number_of_post'],
                    //'category_name'   => $btmetanm,
                    //'offset'          => $postOffset,
                    'post_type'       =>  $section['r_post_type']
                );

                
                if($section['r_post_type'] == 'album'){
                    $posts = get_posts($args);
                    $data[$i]['albums'] =  $albums->data($posts);
                }
                
                if($section['r_post_type'] == 'track'){
                    $posts = rekord_api_get_posts('track');
                    $data[$i]['tracks'] =$tracks->data($posts);
                }

                //$data['sections'][$i] = $section['r_post_type'];	

                $i++;
            }


            return [
                'data' => $data,
                'status' => 200
            ];
    }
    
        
}


