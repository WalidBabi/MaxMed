<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::where('published', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('news.index', compact('news'));
    }
    
    public function show(News $news)
    {
        if (!$news->published) {
            abort(404);
        }
        
        return view('news.show', compact('news'));
    }
} 