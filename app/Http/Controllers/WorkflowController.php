<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index()
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $workflows = Workflow::where('created_by', \Auth::user()->creatorId())->get();

            return view('workflow.index', compact('workflows'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function create()
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $triggers = Workflow::getAvailableTriggers();
            $actions = Workflow::getAvailableActions();

            return view('workflow.create', compact('triggers', 'actions'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function store(Request $request)
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'trigger_model' => 'required|string',
                'trigger_conditions' => 'nullable|array',
                'actions' => 'required|array',
            ]);

            $workflow = new Workflow;
            $workflow->created_by = \Auth::user()->creatorId();
            $workflow->name = $validated['name'];
            $workflow->description = $request->description;
            $workflow->trigger_model = $validated['trigger_model'];
            $workflow->trigger_conditions = $validated['trigger_conditions'] ?? [];
            $workflow->actions = $validated['actions'];
            $workflow->is_active = $request->is_active ?? true;
            $workflow->save();

            return redirect()->route('workflows.index')->with('success', __('Workflow created successfully'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function show(Workflow $workflow)
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $executions = $workflow->executions()->latest()->take(50)->get();

            return view('workflow.show', compact('workflow', 'executions'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function edit(Workflow $workflow)
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $triggers = Workflow::getAvailableTriggers();
            $actions = Workflow::getAvailableActions();

            return view('workflow.edit', compact('workflow', 'triggers', 'actions'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function update(Request $request, Workflow $workflow)
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'trigger_model' => 'required|string',
                'trigger_conditions' => 'nullable|array',
                'actions' => 'required|array',
            ]);

            $workflow->name = $validated['name'];
            $workflow->description = $request->description;
            $workflow->trigger_model = $validated['trigger_model'];
            $workflow->trigger_conditions = $validated['trigger_conditions'] ?? [];
            $workflow->actions = $validated['actions'];
            $workflow->is_active = $request->is_active ?? true;
            $workflow->save();

            return redirect()->route('workflows.index')->with('success', __('Workflow updated successfully'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function destroy(Workflow $workflow)
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $workflow->delete();

            return redirect()->route('workflows.index')->with('success', __('Workflow deleted successfully'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function toggle(Workflow $workflow)
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $workflow->is_active = ! $workflow->is_active;
            $workflow->save();

            return redirect()->back()->with('success', __('Workflow status updated'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function executions(Workflow $workflow)
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $executions = $workflow->executions()->latest()->paginate(20);

            return view('workflow.executions', compact('workflow', 'executions'));
        }

        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function test(Request $request, Workflow $workflow)
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $modelId = $request->model_id;
            $modelType = $request->model_type;

            $model = $modelType::find($modelId);
            if (! $model) {
                return response()->json(['error' => 'Model not found'], 404);
            }

            $execution = $workflow->execute($model);

            return response()->json([
                'success' => true,
                'execution_id' => $execution->id,
                'status' => $execution->status,
            ]);
        }

        return response()->json(['error' => 'Permission denied'], 403);
    }

    public function getAvailableTriggers()
    {
        return response()->json(Workflow::getAvailableTriggers());
    }

    public function getAvailableActions()
    {
        return response()->json(Workflow::getAvailableActions());
    }
}
