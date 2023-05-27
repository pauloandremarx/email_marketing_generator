<?php

namespace app\classes;

class Recover
{
    private $post_recover = [];

    public function existPost(array $fields)
    {
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $this->post_recover['Post_'.$field] = $_POST[$field];
            }
        } 

        return $this->post_recover;
    }

    
}
