<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SearchRequest;

class SearchRequestController extends Controller
{
    public function index()
    {
        $requests = SearchRequest::with('user')->orderByDesc('id')->paginate(10);
        return view('search_requests.index', compact('requests'));
    }

    public function create()
    {
        return view('search_requests.form', ['requestModel' => new SearchRequest()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'box_type_id' => 'required|in:2,5,6',
            'max_coefficient' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ]);

        $validated['user_id'] = auth()->id();

        if ($validated['status']) {
            $validated['started_at'] = now();
        }

        $searchRequest = SearchRequest::create($validated);

        return redirect()->route('search-requests.index')->with('success', 'Запрос добавлен');
    }

    public function edit(SearchRequest $searchRequest)
    {
        return view('search_requests.form', ['requestModel' => $searchRequest]);
    }

    public function update(Request $request, SearchRequest $searchRequest)
    {
        $validated = $request->validate([
            'box_type_id' => 'required|in:2,5,6',
            'max_coefficient' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ]);

        if ($searchRequest->status == SearchRequest::STATUS_INACTIVE && $validated['status'] == SearchRequest::STATUS_ACTIVE) {
            $validated['started_at'] = now();
            $validated['stopped_at'] = null;
        } elseif ($searchRequest->status == SearchRequest::STATUS_ACTIVE && $validated['status'] == SearchRequest::STATUS_INACTIVE) {
            $validated['stopped_at'] = now();
        }

        $searchRequest->update($validated);
        return redirect()->route('search-requests.index')->with('success', 'Обновлено');
    }
}
