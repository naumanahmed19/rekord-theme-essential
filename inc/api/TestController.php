<?php


class TestController{


 
    public function data($posts){

        $data = [];
        $i = 0;
        if(!empty($posts)){
            foreach($posts as $post) {
                $data[$i]['id'] = $post->ID;
                $data[$i]['name'] = $post->post_title;
                $data[$i]['content'] = $post->post_content;
        
                if(get_the_post_thumbnail_url($post->ID, 'thumbnail')){
                    $data[$i]['media'] = rekord_get_post_media($post->ID);
                }
                $i++;
            }
        }
        return $data;
    }


    // public function get($id){
    //     $args = rekord_relation_args('track' , 'track_artists',-1, $id);
    //     $posts = get_posts($args); //builtin method
    //     return $this->data($posts);
    //}
        
    
        
}
