<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Platform;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('platform')->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $platforms = Platform::all();
        return view('projects.create', compact('platforms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'platform_id' => 'required|exists:platforms,id',
        ]);

        Project::create($request->all());

        return redirect()->route('projects.index')->with('success', 'Dự án đã được tạo.');
    }

    public function edit(Project $project)
    {
        $platforms = Platform::all();
        return view('projects.edit', compact('project', 'platforms'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'platform_id' => 'required|exists:platforms,id',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')->with('success', 'Dự án đã được cập nhật.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Dự án đã được xóa.');
    }
}
