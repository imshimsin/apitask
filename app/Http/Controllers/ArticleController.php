<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        return response()->json($article, 201);
    }

    public function index()
    {
        $articles = Article::where('user_id', auth()->id())->get();
        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);

        if ($article->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($article);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        if ($article->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $article->update($request->only(['title', 'content']));
        return response()->json($article);
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if ($article->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $article->delete();
        return response()->json(['message' => 'Article deleted']);
    }
}
