<?php

namespace App\Http\Controllers;

use App\Models\ProductService;
use App\Models\ProductionBom;
use App\Models\ProductionBomLine;
use App\Models\ProductionBomVersion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionBomController extends Controller
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

        $boms = ProductionBom::where('created_by', \Auth::user()->creatorId())
            ->with(['product', 'activeVersion'])
            ->orderBy('id', 'desc')
            ->get();

        return view('production.boms.index', compact('boms'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create production bom')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'product')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $components = ProductService::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'product')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        return view('production.boms.create', compact('products', 'components'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create production bom')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'name' => 'required|max:255',
            'code' => 'nullable|max:255',
            'version' => 'nullable|max:50',
            'components' => 'required|array|min:1',
            'components.*' => 'required|integer',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:0.0001',
            'scrap_percents' => 'nullable|array',
            'scrap_percents.*' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $createdBy = \Auth::user()->creatorId();

        DB::transaction(function () use ($request, $createdBy) {
            $bom = ProductionBom::create([
                'product_id' => $request->product_id,
                'code' => $request->code,
                'name' => $request->name,
                'created_by' => $createdBy,
            ]);

            $version = ProductionBomVersion::create([
                'production_bom_id' => $bom->id,
                'version' => $request->version ?: '1',
                'is_active' => true,
                'created_by' => $createdBy,
            ]);

            $lines = [];
            foreach ($request->components as $idx => $componentId) {
                $lines[] = [
                    'production_bom_version_id' => $version->id,
                    'component_product_id' => $componentId,
                    'quantity' => $request->quantities[$idx] ?? 0,
                    'scrap_percent' => $request->scrap_percents[$idx] ?? 0,
                    'created_by' => $createdBy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            ProductionBomLine::insert($lines);

            $bom->active_bom_version_id = $version->id;
            $bom->save();
        });

        return redirect()->route('production.boms.index')->with('success', __('BOM successfully created.'));
    }

    public function show(ProductionBom $bom)
    {
        if (!\Auth::user()->can('show production bom')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($bom->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $bom->load(['product', 'versions.lines.component']);

        return view('production.boms.show', compact('bom'));
    }

    public function edit(ProductionBom $bom)
    {
        if (!\Auth::user()->can('edit production bom')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($bom->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'product')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $components = ProductService::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'product')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $bom->load(['activeVersion.lines.component']);

        return view('production.boms.edit', compact('bom', 'products', 'components'));
    }

    public function update(Request $request, ProductionBom $bom)
    {
        if (!\Auth::user()->can('edit production bom')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($bom->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'name' => 'required|max:255',
            'code' => 'nullable|max:255',
            'version' => 'nullable|max:50',
            'components' => 'required|array|min:1',
            'components.*' => 'required|integer',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:0.0001',
            'scrap_percents' => 'nullable|array',
            'scrap_percents.*' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $createdBy = \Auth::user()->creatorId();

        DB::transaction(function () use ($request, $bom, $createdBy) {
            $bom->product_id = $request->product_id;
            $bom->code = $request->code;
            $bom->name = $request->name;
            $bom->save();

            $version = ProductionBomVersion::where('id', $bom->active_bom_version_id)
                ->where('created_by', $createdBy)
                ->first();

            if (!$version) {
                $version = ProductionBomVersion::create([
                    'production_bom_id' => $bom->id,
                    'version' => $request->version ?: '1',
                    'is_active' => true,
                    'created_by' => $createdBy,
                ]);
                $bom->active_bom_version_id = $version->id;
                $bom->save();
            } else {
                if ($request->version) {
                    $version->version = $request->version;
                }
                $version->is_active = true;
                $version->save();
            }

            ProductionBomLine::where('production_bom_version_id', $version->id)->delete();

            $lines = [];
            foreach ($request->components as $idx => $componentId) {
                $lines[] = [
                    'production_bom_version_id' => $version->id,
                    'component_product_id' => $componentId,
                    'quantity' => $request->quantities[$idx] ?? 0,
                    'scrap_percent' => $request->scrap_percents[$idx] ?? 0,
                    'created_by' => $createdBy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            ProductionBomLine::insert($lines);
        });

        return redirect()->route('production.boms.index')->with('success', __('BOM successfully updated.'));
    }

    public function destroy(ProductionBom $bom)
    {
        if (!\Auth::user()->can('delete production bom')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($bom->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        DB::transaction(function () use ($bom) {
            $versionIds = ProductionBomVersion::where('production_bom_id', $bom->id)->pluck('id');
            if ($versionIds->isNotEmpty()) {
                ProductionBomLine::whereIn('production_bom_version_id', $versionIds)->delete();
            }
            ProductionBomVersion::where('production_bom_id', $bom->id)->delete();
            $bom->delete();
        });

        return redirect()->route('production.boms.index')->with('success', __('BOM successfully deleted.'));
    }

    public function storeVersion(Request $request, ProductionBom $bom)
    {
        if (!\Auth::user()->can('edit production bom')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($bom->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'version' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $createdBy = \Auth::user()->creatorId();

        DB::transaction(function () use ($request, $bom, $createdBy) {
            $sourceVersion = ProductionBomVersion::where('id', $bom->active_bom_version_id)
                ->where('production_bom_id', $bom->id)
                ->first();

            $newVersion = ProductionBomVersion::create([
                'production_bom_id' => $bom->id,
                'version' => $request->version,
                'is_active' => false,
                'created_by' => $createdBy,
            ]);

            if ($sourceVersion) {
                $sourceLines = ProductionBomLine::where('production_bom_version_id', $sourceVersion->id)->get();
                $lines = [];
                foreach ($sourceLines as $line) {
                    $lines[] = [
                        'production_bom_version_id' => $newVersion->id,
                        'component_product_id' => $line->component_product_id,
                        'quantity' => $line->quantity,
                        'scrap_percent' => $line->scrap_percent,
                        'created_by' => $createdBy,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($lines)) {
                    ProductionBomLine::insert($lines);
                }
            }
        });

        return redirect()->route('production.boms.show', $bom->id)->with('success', __('BOM version successfully created.'));
    }

    public function activateVersion(ProductionBom $bom, ProductionBomVersion $version)
    {
        if (!\Auth::user()->can('edit production bom')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($bom->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($version->production_bom_id != $bom->id) {
            return redirect()->back()->with('error', __('Invalid data.'));
        }

        DB::transaction(function () use ($bom, $version) {
            ProductionBomVersion::where('production_bom_id', $bom->id)->update(['is_active' => false]);
            $version->is_active = true;
            $version->save();
            $bom->active_bom_version_id = $version->id;
            $bom->save();
        });

        return redirect()->route('production.boms.show', $bom->id)->with('success', __('BOM version activated.'));
    }
}
