<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        // Remove permission middleware for public methods - index and show should be publicly accessible
        $this->middleware('permission:news.create')->only(['create', 'store']);
        $this->middleware('permission:news.edit')->only(['edit', 'update']);
        $this->middleware('permission:news.delete')->only(['destroy']);
    }

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