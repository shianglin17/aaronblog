<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function list() {
        $articles = Article::all();
        return response()->json($articles);
    }
}