<?php

namespace App\Http\Controllers\Core;

use App\Actions\Archive\DeleteArchivedItem;
use App\Actions\Archive\GetArchives;
use App\Actions\Archive\UnarchiveItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function __construct(
        private GetArchives $getArchives,
        private UnarchiveItem $unarchiveItem,
        private DeleteArchivedItem $deleteArchivedItem
    ) {}
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', 'all');
        $sortBy = $request->input('sort_by', 'latest');
        $perPage = $request->input('per_page', 5);

        $archives = $this->getArchives->execute($type, $search);

        if ($sortBy === 'oldest') {
            $archives = $archives->sortBy('archived_at');
        } else {
            $archives = $archives->sortByDesc('archived_at');
        }

        if ($perPage === 'all') {
            $archives = $archives->values();
        } else {
            $currentPage = $request->input('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $total = $archives->count();
            $items = $archives->slice($offset, $perPage)->values();
            $archives = new \Illuminate\Pagination\LengthAwarePaginator($items, $total, $perPage, $currentPage, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return view('pages.dashboard.system.archives', compact('archives'));
    }

    public function unarchive($id)
    {
        $success = $this->unarchiveItem->execute($id);

        if ($success) {
            return redirect()->route('archives.list')->with('success', 'Item unarchived successfully.');
        }

        return redirect()->route('archives.list')->with('error', 'Item not found.');
    }

    public function destroy($id)
    {
        $success = $this->deleteArchivedItem->execute($id);

        if ($success) {
            return redirect()->route('archives.list')->with('success', 'Item permanently deleted.');
        }

        return redirect()->route('archives.list')->with('error', 'Failed to delete item.');
    }
}
