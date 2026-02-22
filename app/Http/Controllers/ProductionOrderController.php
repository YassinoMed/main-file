<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ProductService;
use App\Models\ProductionBom;
use App\Models\ProductionBomLine;
use App\Models\ProductionBomVersion;
use App\Models\ProductionMaterialMove;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderOperation;
use App\Models\ProductionWorkCenter;
use App\Models\StockReport;
use App\Models\User;
use App\Models\Utility;
use App\Models\warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionOrderController extends Controller
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

    public function index(Request $request)
    {
        if (!\Auth::user()->can('manage production')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $query = ProductionOrder::where('created_by', \Auth::user()->creatorId())
            ->with(['product', 'workCenter', 'employee'])
            ->orderBy('id', 'desc');

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }
        if (!empty($request->priority)) {
            $query->where('priority', $request->priority);
        }
        if (!empty($request->work_center_id)) {
            $query->where('work_center_id', $request->work_center_id);
        }
        if (!empty($request->employee_id)) {
            $query->where('employee_id', $request->employee_id);
        }
        if (!empty($request->start_date)) {
            $query->whereDate('planned_start_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $query->whereDate('planned_end_date', '<=', $request->end_date);
        }

        $orders = $query->get();

        $workCenters = ProductionWorkCenter::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $employees = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.orders.index', compact('orders', 'workCenters', 'employees'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create production order')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'product')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $warehouses = warehouse::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $workCenters = ProductionWorkCenter::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $employees = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.orders.create', compact('products', 'warehouses', 'workCenters', 'employees'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create production order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'warehouse_id' => 'nullable|integer',
            'work_center_id' => 'nullable|integer',
            'employee_id' => 'nullable|integer',
            'quantity_planned' => 'required|numeric|min:0.0001',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date',
            'priority' => 'required|in:low,normal,high,urgent',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $createdBy = \Auth::user()->creatorId();

        $bom = ProductionBom::where('created_by', $createdBy)->where('product_id', $request->product_id)->first();
        $bomVersionId = $bom?->active_bom_version_id;
        if (!$bomVersionId) {
            return redirect()->back()->with('error', __('No active BOM found for selected product.'));
        }

        $order = DB::transaction(function () use ($request, $createdBy, $bomVersionId) {
            $nextNumber = (int) ProductionOrder::where('created_by', $createdBy)->max('order_number');
            $nextNumber = $nextNumber > 0 ? $nextNumber + 1 : 1;

            $order = ProductionOrder::create([
                'order_number' => $nextNumber,
                'product_id' => $request->product_id,
                'production_bom_version_id' => $bomVersionId,
                'warehouse_id' => $request->warehouse_id,
                'work_center_id' => $request->work_center_id,
                'employee_id' => $request->employee_id,
                'quantity_planned' => $request->quantity_planned,
                'quantity_produced' => 0,
                'planned_start_date' => $request->planned_start_date,
                'planned_end_date' => $request->planned_end_date,
                'priority' => $request->priority,
                'status' => 'draft',
                'notes' => $request->notes,
                'created_by' => $createdBy,
            ]);

            $lines = ProductionBomLine::where('production_bom_version_id', $bomVersionId)->get();
            $materialRows = [];
            foreach ($lines as $line) {
                $required = (float) $request->quantity_planned * (float) $line->quantity;
                $required = $required * (1 + ((float) $line->scrap_percent / 100));
                $materialRows[] = [
                    'production_order_id' => $order->id,
                    'component_product_id' => $line->component_product_id,
                    'warehouse_id' => $request->warehouse_id,
                    'required_qty' => $required,
                    'reserved_qty' => 0,
                    'consumed_qty' => 0,
                    'created_by' => $createdBy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($materialRows)) {
                ProductionMaterialMove::insert($materialRows);
            }

            ProductionOrderOperation::create([
                'production_order_id' => $order->id,
                'name' => __('Production'),
                'sequence' => 1,
                'work_center_id' => $request->work_center_id,
                'planned_minutes' => 0,
                'actual_minutes' => 0,
                'status' => 'pending',
                'created_by' => $createdBy,
            ]);

            return $order;
        });

        return redirect()->route('production.orders.show', $order->id)->with('success', __('Production order successfully created.'));
    }

    public function show(ProductionOrder $order)
    {
        if (!\Auth::user()->can('show production order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($order->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $order->load(['product', 'warehouse', 'workCenter', 'employee', 'bomVersion.lines.component', 'materials.component', 'operations.workCenter', 'operations.timeLogs', 'qualityChecks']);

        $warehouses = warehouse::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $workCenters = ProductionWorkCenter::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $employees = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.orders.show', compact('order', 'warehouses', 'workCenters', 'employees'));
    }

    public function edit(ProductionOrder $order)
    {
        if (!\Auth::user()->can('edit production order')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($order->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'product')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $warehouses = warehouse::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $workCenters = ProductionWorkCenter::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $employees = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.orders.edit', compact('order', 'products', 'warehouses', 'workCenters', 'employees'));
    }

    public function update(Request $request, ProductionOrder $order)
    {
        if (!\Auth::user()->can('edit production order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($order->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'warehouse_id' => 'nullable|integer',
            'work_center_id' => 'nullable|integer',
            'employee_id' => 'nullable|integer',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date',
            'priority' => 'required|in:low,normal,high,urgent',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $order->warehouse_id = $request->warehouse_id;
        $order->work_center_id = $request->work_center_id;
        $order->employee_id = $request->employee_id;
        $order->planned_start_date = $request->planned_start_date;
        $order->planned_end_date = $request->planned_end_date;
        $order->priority = $request->priority;
        $order->notes = $request->notes;
        $order->save();

        ProductionMaterialMove::where('production_order_id', $order->id)->update(['warehouse_id' => $request->warehouse_id]);

        return redirect()->route('production.orders.show', $order->id)->with('success', __('Production order successfully updated.'));
    }

    public function destroy(ProductionOrder $order)
    {
        if (!\Auth::user()->can('delete production order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($order->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        DB::transaction(function () use ($order) {
            ProductionMaterialMove::where('production_order_id', $order->id)->delete();
            ProductionOrderOperation::where('production_order_id', $order->id)->delete();
            $order->delete();
        });

        return redirect()->route('production.orders.index')->with('success', __('Production order successfully deleted.'));
    }

    public function calendar()
    {
        if (!\Auth::user()->can('manage production')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $transdate = date('Y-m-d', time());

        return view('production.calendar', compact('transdate'));
    }

    public function calendarData(Request $request)
    {
        if (!\Auth::user()->can('manage production')) {
            return response()->json([], 403);
        }

        $orders = ProductionOrder::where('created_by', \Auth::user()->creatorId())
            ->with(['product'])
            ->whereNotNull('planned_start_date')
            ->get();

        $events = [];
        foreach ($orders as $order) {
            $events[] = [
                'id' => $order->id,
                'title' => ($order->product?->name ?? __('Production Order')) . ' #' . $order->order_number,
                'start' => $order->planned_start_date,
                'end' => $order->planned_end_date ?: $order->planned_start_date,
                'className' => $order->status === 'done' ? 'event-success' : ($order->status === 'in_progress' ? 'event-primary' : 'event-warning'),
                'url' => route('production.orders.show', $order->id),
            ];
        }

        return response()->json($events);
    }
}
