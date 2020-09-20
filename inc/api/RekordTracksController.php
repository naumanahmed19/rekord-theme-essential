<?php

include ( __DIR__ . '/TestController.php');


class RekordTracksController{

    public function data($posts){
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
                    $data[$i]['media'] = rekord_get_post_media($post->ID);
                }
    
                $data[$i]['time'] = rekord_get_field('track_time', $post->ID);
                $data[$i]['artist'] =  $this->artists($post->ID);
                
        
                $i++;
            }
        }
        return $data;
    }

    public function artists($id){
       $artist= new TestController();   
        $posts = rekord_get_field('track_artists', $id);
    
        return $artist->data($posts);
    }
}



