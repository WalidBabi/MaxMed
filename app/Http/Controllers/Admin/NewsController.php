<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:news.view')->only(['index', 'show']);
        $this->middleware('permission:news.create')->only(['create', 'store']);
        $this->middleware('permission:news.edit')->only(['edit', 'update']);
        $this->middleware('permission:news.delete')->only(['destroy']);
        $this->middleware('permission:news.publish')->only(['publish', 'unpublish']);
    }

    public function index()
    {
        $news = News::orderBy('created_at', 'desc')->get();
        return view('admin.news.index', compact('news'));
    }
    
    public function create()
    {
        return view('admin.news.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'published' => 'boolean',
        ]);
        
        $news = new News();
        $news->title = $validated['title'];
        $news->content = $validated['content'];
        $news->published = $request->has('published');
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news', 'public'); // Store in public disk
            $news->image_url = 'storage/' . $path;
        }
        
        $news->save();
        
        return redirect()->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }
    
    public function destroy(News $news)
    {
        // Delete image if exists
        if ($news->image_url && Storage::disk('public')->exists(str_replace('storage/', '', $news->image_url))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $news->image_url));
        }
        
        $news->delete();
        
        return redirect()->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }
} 