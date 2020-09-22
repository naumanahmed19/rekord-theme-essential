<?php


class RekordAlbumsController{


    public function data($posts){
        $data = [];
        $i = 0;
        if(!empty($posts)){
            foreach($posts as $post) {
                $data[$i]['id'] = $post->ID;
                $data[$i]['title'] = $post->post_title;
                $data[$i]['content'] = $post->post_content;
                $data[$i]['slug'] = $post->post_name;
                $data[$i]['media'] = rekord_get_post_media($post->ID);
                $data[$i]['tracks'] = $this->tracks( $post->ID);
                
                $i++;
            }
        }
        return $data;
    }

    function tracks($id){
        $tracks = new RekordTracksController();
        $args = rekord_relation_args('track' , 'track_albums',-1, $id);
        $posts = get_posts($args); //builtin method
        return $tracks->data($posts);
    }


    public function get($id){
        $args = rekord_relation_args('track' , 'track_artists',-1, $id);
        $posts = get_posts($args); //builtin method
        return $this->data($posts);
    }
    
        
}
