<?php

namespace App\Actions\GuaranteeLetter;

use App\Models\Operation\GLTemplate;
use Illuminate\Http\Request;

class ListGLTemplates
{
    public function execute(Request $request)
    {
        $query = GLTemplate::query()->with('data');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('gl_tmp_title', 'like', "%{$search}%")->orWhere('gl_content', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('gl_tmp_id', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('gl_tmp_title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('gl_tmp_title', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('gl_tmp_id', 'desc');
                break;
        }

        $perPage = $request->get('per_page', 5);

        return ($perPage === 'all') ? $query->get() : $query->paginate((int) $perPage);
    }
}
