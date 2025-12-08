<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function post($id = null) {
        $data = request()->validate([
            'content' => 'required',
            'status' => ''
        ]);
        if (is_null($id)) {
            post()->create($data);
        } else {
            post()->find($id)->update($data);
        }

        return $this->get();
    }

    public function delete($id) {
        $post = post()->find($id);
        if ($post->user_id != user()->id) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $post->delete();

        return $this->get();

    }

    public function get() {
        return response()->json(post()->get(), 200);
    }
}
