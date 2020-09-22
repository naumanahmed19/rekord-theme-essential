<?php


class RekordArtistsController{


    public function data($posts){

        $data = [];
        $i = 0;
        if(!empty($posts)){
            foreach($posts as $post) {
                $data[$i]['id'] = $post->ID;
                $data[$i]['name'] = $post->post_title;
                $data[$i]['content'] = $post->post_content;
                $data[$i]['designation'] =rekord_get_field('designation',$post->ID); 
        
                if(get_the_post_thumbnail_url($post->ID, 'thumbnail')){
                    $data[$i]['media'] = rekord_get_post_media($post->ID);
                }

                $data[$i]['tracks'] = $this->tracks( $post->ID);
                $data[$i]['albums'] = $this->albums( $post->ID);
                $i++;
            }
        }
        return $data;
    }


    
    public function tracks($id){
        $tracks = new RekordTracksController();
        $args = rekord_relation_args('track' , 'track_artists',-1, $id);
        $posts = get_posts($args); //builtin method
        return $tracks->data($posts);
    }

    public function albums($id){
        $tracks = new RekordTracksController();
        $args = rekord_relation_args('album' , 'album_artists',-1, $id);
        $posts = get_posts($args); //builtin method
        return $tracks->data($posts);
    }



    public function get($id){
        $args = rekord_relation_args('track' , 'track_artists',-1, $id);
        $posts = get_posts($args); //builtin method
        return $this->data($posts);
    }
        
}
