<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index(): JsonResponse
    {
        $news = NewsPost::orderBy('published_at', 'desc')->get();
        return response()->json($news);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only(['title', 'content', 'published_at']);
        $data['published_at'] = $data['published_at'] ?? now();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news', 'public');
            $data['image_path'] = $path;
        }

        $post = NewsPost::create($data);

        return response()->json($post, 201);
    }

    public function update(Request $request, NewsPost $news): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only(['title', 'content', 'published_at']);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $path = $request->file('image')->store('news', 'public');
            $data['image_path'] = $path;
        }

        $news->update($data);

        return response()->json($news->fresh());
    }

    public function destroy(NewsPost $news): JsonResponse
    {
        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
        }
        $news->delete();

        return response()->json(['message' => 'Jaunums dzēsts.']);
    }
}
