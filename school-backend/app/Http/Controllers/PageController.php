<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        return response()->json($page);
    }

    public function update(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $page = Page::firstOrCreate(
            ['slug' => $slug],
            ['title' => $request->title, 'content' => $request->content]
        );

        $page->update($request->only(['title', 'content']));

        return response()->json($page);
    }
}
