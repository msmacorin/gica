<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PostType;

class PostTypeController extends Controller {
    
    function getTypes() {
        $type = PostType::all();
        return json_encode($type);
    }
    
}
