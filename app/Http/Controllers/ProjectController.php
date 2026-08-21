<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    private const SORTABLE_COLUMNS = ['client_name', 'project_name', 'status', 'priority', 'start_date', 'due_date'];

    public function index(): Response
    {
        $query = Project::query();

        // Search
        if (request()->filled('search')) {
            $search = request()->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if (request()->filled('status')) {
            $query->where('status', request()->input('status'));
        }

        // Filter by priority
        if (request()->filled('priority')) {
            $query->where('priority', request()->input('priority'));
        }

        // Sort
        $sort = request()->input('sort', 'due_date');
        $direction = request()->input('direction', 'asc');

        if (in_array($sort, self::SORTABLE_COLUMNS)) {
            $query->orderBy($sort, in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc');
        } else {
            $query->orderBy('due_date', 'asc');
        }

        $projects = $query->paginate(15)->withQueryString();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => request()->only(['search', 'status', 'priority', 'sort', 'direction']),
            'statuses' => \App\Enums\ProjectStatus::cases(),
            'priorities' => \App\Enums\ProjectPriority::cases(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Projects/Create', [
            'statuses' => \App\Enums\ProjectStatus::cases(),
            'priorities' => \App\Enums\ProjectPriority::cases(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::create($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): Response
    {
        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'statuses' => \App\Enums\ProjectStatus::cases(),
            'priorities' => \App\Enums\ProjectPriority::cases(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}