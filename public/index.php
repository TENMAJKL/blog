<?php

declare(strict_types=1);

use Lemon\Cache;
use Lemon\Config;
use Lemon\Http\Request;
use Lemon\Kernel\Lifecycle;
use Lemon\Route;
use Lemon\Support\Filesystem;
use Lemon\Support\Types\Arr;

include __DIR__ . '/../vendor/autoload.php';

Lifecycle::init(Filesystem::parent(__DIR__));

session_start();

Config::part('kernel')->set('debug', true);

Route::get('/', function() {
    $posts = Cache::get('posts');
    if (! $posts) {
        return 'no posts';
    }
    $posts = Arr::reverse($posts)->content;
    foreach ($posts as $post) {
        echo $post['title'], '<br>', $post['content'], '<br><br>';
    }
});

Route::post('/upload', function(Request $request) {
    if (Arr::hasKey($_SESSION, 'password')) {
        if ($_SESSION['password'] === Cache::get('password')) {
            $posts = Cache::get('posts');
            $posts[] = [
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'date' => time(),
            ];
            Cache::set('posts', $posts);
        }
    }
    
    return redirect('/');
});

Route::get('/upload', function() {
    if (Arr::hasKey($_SESSION, 'password')) {
        if ($_SESSION['password'] === Cache::get('password')) {
            return <<<'HTML'
                <form method="post">
                    <input type="text" name="title">
                    <input type="text" name="content">
                    <button>dekel</button>
                </form>
            HTML;
        }
    }
    return redirect('/');
});


Route::get('/login', function() {
    if (Arr::has($_SESSION, 'password')) {
        return redirect('/');
    }

    return <<<'HTML'
        <form method="post">
            <input type="text" name="name">
            <input type="password" name="password">
            <button>send</button>
        </form>       
    HTML;
});


Route::post('/login', function(Request $request) {
    if (isset($request->input['name']) && isset($request->input['password'])) {
        if ($request->input('name') === Cache::get('name') && password_verify($request->input('password'), Cache::get('password'))) {
            $_SESSION['password'] = Cache::get('password');
            return redirect('/');
        } 
    } // TODO add $request->has


    return redirect('/login');
});
