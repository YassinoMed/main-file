<?php

namespace App\Http\Controllers;

use App\Models\ProductionWorkCenter;
use App\Models\User;
use Illuminate\Http\Request;

class ProductionWorkCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (\Auth::check() && \Auth::user()->type !== 'super admin' && (int) User::show_production() !== 1) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => __('Permission denied.')], 403);
                }

                return redirect()->route('dashboard')->with('error', __('Permission denied.'));
            }

            return $next($request);
        });
    }

    public function index()
    {
        if (!\Auth::user()->can('manage production')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $workCenters = ProductionWorkCenter::where('created_by', \Auth::user()->creatorId())->orderBy('id', 'desc')->get();

        return view('production.work_centers.index', compact('workCenters'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create production work center')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('production.work_centers.create');
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create production work center')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'type' => 'required|in:machine,workshop,employee',
            'cost_per_hour' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        ProductionWorkCenter::create([
            'name' => $request->name,
            'type' => $request->type,
            'cost_per_hour' => $request->cost_per_hour ?? 0,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        return redirect()->route('production.work-centers.index')->with('success', __('Work center successfully created.'));
    }

    public function edit(ProductionWorkCenter $workCenter)
    {
        if (!\Auth::user()->can('edit production work center')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($workCenter->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('production.work_centers.edit', compact('workCenter'));
    }

    public function update(Request $request, ProductionWorkCenter $workCenter)
    {
        if (!\Auth::user()->can('edit production work center')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($workCenter->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'type' => 'required|in:machine,workshop,employee',
            'cost_per_hour' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $workCenter->name = $request->name;
        $workCenter->type = $request->type;
        $workCenter->cost_per_hour = $request->cost_per_hour ?? 0;
        $workCenter->save();

        return redirect()->route('production.work-centers.index')->with('success', __('Work center successfully updated.'));
    }

    public function destroy(ProductionWorkCenter $workCenter)
    {
        if (!\Auth::user()->can('delete production work center')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($workCenter->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $workCenter->delete();

        return redirect()->route('production.work-centers.index')->with('success', __('Work center successfully deleted.'));
    }
}
