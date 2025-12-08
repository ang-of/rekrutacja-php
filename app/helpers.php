<?php

use App\Models\Post;

if (!function_exists('user')) {
    function user()
    {
        return request()->user();
    }
}

if (!function_exists('post')) {
    function post() {
        return new class {
            public function __call($method, $parameters)
            {
                if ($method === 'create') {
                    $data = $parameters[0] ?? [];
                    $data['user_id'] = request()->user()->id;
                    return Post::create($data);
                }
                
                return Post::forUser()->$method(...$parameters);
            }
        };
    }
}