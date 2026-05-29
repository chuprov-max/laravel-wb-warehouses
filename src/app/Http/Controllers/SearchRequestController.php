<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSearchRequestRequest;
use App\Http\Requests\UpdateSearchRequestRequest;
use App\Models\SearchRequest;
use App\Models\Warehouse;

class SearchRequestController extends Controller
{
    public function index()
    {
        $requests = SearchRequest::with('user')->orderByDesc('id')->paginate(10);
        return view('search_requests.index', compact('requests'));
    }

    public function create()
    {
        return view('search_requests.form', [
            'requestModel' => new SearchRequest(),
            'warehouses'   => Warehouse::getActiveWarehouses(),
        ]);
    }

    public function store(StoreSearchRequestRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($data['status']) {
            $data['started_at'] = now();
        }

        SearchRequest::create($data);

        return redirect()->route('search-requests.index')->with('success', 'Запрос добавлен');
    }

    public function edit(SearchRequest $searchRequest)
    {
        return view('search_requests.form', [
            'requestModel' => $searchRequest,
            'warehouses'   => Warehouse::getActiveWarehouses(),
        ]);
    }

    public function update(UpdateSearchRequestRequest $request, SearchRequest $searchRequest)
    {
        $data = $request->validated();

        if ($searchRequest->status === SearchRequest::STATUS_INACTIVE && $data['status'] === SearchRequest::STATUS_ACTIVE) {
            $data['started_at'] = now();
            $data['stopped_at'] = null;
        } elseif ($searchRequest->status === SearchRequest::STATUS_ACTIVE && $data['status'] === SearchRequest::STATUS_INACTIVE) {
            $data['stopped_at'] = now();
        }

        $searchRequest->update($data);

        return redirect()->route('search-requests.index')->with('success', 'Обновлено');
    }
}
