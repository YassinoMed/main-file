<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProjectUser;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AssignProject;
use App\Models\Project;
use App\Models\Utility;
use App\Models\Tag;
use App\Models\ProjectTask;
use App\Models\TimeTracker;
use App\Models\TrackPhoto;
use App\Models\Customer;
use App\Models\ProductService;
use App\Models\Invoice;
use App\Models\Employee;

use Illuminate\Support\Facades\Validator;


class ApiController extends Controller
{
    //
    use ApiResponser;

    private function tokenAllows(Request $request, string $ability): bool
    {
        $token = $request->user()?->currentAccessToken();
        if (! $token) {
            return false;
        }

        return $request->user()->tokenCan('*') || $request->user()->tokenCan($ability);
    }

    public function login(Request $request)
    {

        $attr = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($attr)) {
            return $this->error('Credentials not match', 401);
        }

        $settings              = Utility::settings(auth()->user()->id);

        $settings = [
            'shot_time'=> isset($settings['interval_time'])?$settings['interval_time']:0.5,
        ];

        return $this->success([
            'token' => auth()->user()->createToken('API Token')->plainTextToken,
            'user'=> auth()->user()->id,
            'settings' =>$settings,
        ],'Login successfully.');
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->success([],'Tokens Revoked');
    }


    public function getProjects(Request $request)
    {

        $user = auth()->user();

        if($user->type!='company')
        {
            $assign_pro_ids = ProjectUser::where('user_id',$user->id)->pluck('project_id');


            $project_s      = Project::with('tasks')->whereIn('id', $assign_pro_ids)->get()->toArray();

        }
        else
        {


            $project_s = Project::with('tasks')->where('created_by', $user->id)->get()->toArray();


        }

        return $this->success([
            'projects' => $project_s,
        ],'Get Project List successfully.');
    }


    public function addTracker(Request $request){

        $user = auth()->user();
        if($request->has('action') && $request->action == 'start'){

            $validatorArray = [
                'task_id' => 'required|integer',
            ];
            $validator      = \Validator::make(
                $request->all(), $validatorArray
            );
            if($validator->fails())
            {
                return $this->error($validator->errors()->first(), 401);
            }
            $task= ProjectTask::find($request->task_id);

            if(empty($task)){
                return $this->error('Invalid task', 401);
            }

            $project_id = isset($task->project_id)?$task->project_id:'';
            TimeTracker::where('created_by', '=', $user->id)->where('is_active', '=', 1)->update(['end_time' => date("Y-m-d H:i:s")]);

            $track['name']        = $request->has('workin_on') ? $request->input('workin_on') : '';
            $track['project_id']  = $project_id;
            $track['is_billable'] =  $request->has('is_billable')? $request->is_billable:0;
            $track['tag_id']      = $request->has('workin_on') ? $request->input('workin_on') : '';
            $track['start_time']  = $request->has('time') ?  date("Y-m-d H:i:s",strtotime($request->input('time'))) : date("Y-m-d H:i:s");
            $track['task_id']     = $request->has('task_id') ? $request->input('task_id') : '';
            $track['user_id']     = $user->id;
            $track['created_by']  = $user->creatorId();
            $track                = TimeTracker::create($track);
            $track->action        ='start';

            return $this->success( $track,'Track successfully create.');
        }else{
            $validatorArray = [
                'task_id' => 'required|integer',
                'traker_id' =>'required|integer',
            ];
            $validator      = Validator::make(
                $request->all(), $validatorArray
            );
            if($validator->fails())
            {
                return Utility::error_res($validator->errors()->first());
            }
            $tracker = TimeTracker::where('id',$request->traker_id)->first();
            if($tracker)
            {
                $tracker->end_time   = $request->has('time') ?  date("Y-m-d H:i:s",strtotime($request->input('time'))) : date("Y-m-d H:i:s");
                $tracker->is_active  = 0;
                $tracker->total_time = Utility::diffance_to_time($tracker->start_time, $tracker->end_time);
                $tracker->save();
                return $this->success( $tracker,'Stop time successfully.');
            }
        }

    }
    public function uploadImage(Request $request){
        $user = auth()->user();
        $image_base64 = base64_decode($request->img);
        $file =$request->imgName;
        if($request->has('tracker_id') && !empty($request->tracker_id)){
            $app_path = storage_path('uploads/traker_images/').$request->tracker_id.'/';
            if (!file_exists($app_path)) {
                mkdir($app_path, 0777, true);
            }

        }else{
            $app_path = storage_path('uploads/traker_images/');
            if (!is_dir($app_path)) {
                mkdir($app_path, 0777, true);
            }
        }
        $file_name =  $app_path.$file;
        file_put_contents( $file_name, $image_base64);
        $new = new TrackPhoto();
        $new->track_id = $request->tracker_id;
        $new->user_id  = $user->id;
        $new->img_path  = 'uploads/traker_images/'.$request->tracker_id.'/'.$file;
        $new->time  = $request->time;
        $new->status  = 1;
        $new->created_by  = $user->creatorId();
        $new->save();
        return $this->success( [],'Uploaded successfully.');
    }

    public function customers(Request $request)
    {
        if (! $this->tokenAllows($request, 'customers.read')) {
            return $this->error('Forbidden', 403);
        }

        $customers = Customer::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'customers' => $customers,
        ], 'Customers fetched successfully.');
    }

    public function customerShow(Request $request, Customer $customer)
    {
        if (! $this->tokenAllows($request, 'customers.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $customer->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'customer' => $customer,
        ], 'Customer fetched successfully.');
    }

    public function products(Request $request)
    {
        if (! $this->tokenAllows($request, 'products.read')) {
            return $this->error('Forbidden', 403);
        }

        $products = ProductService::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'products' => $products,
        ], 'Products fetched successfully.');
    }

    public function productShow(Request $request, ProductService $productService)
    {
        if (! $this->tokenAllows($request, 'products.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $productService->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'product' => $productService,
        ], 'Product fetched successfully.');
    }

    public function invoices(Request $request)
    {
        if (! $this->tokenAllows($request, 'invoices.read')) {
            return $this->error('Forbidden', 403);
        }

        $invoices = Invoice::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['customer'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'invoices' => $invoices,
        ], 'Invoices fetched successfully.');
    }

    public function invoiceShow(Request $request, Invoice $invoice)
    {
        if (! $this->tokenAllows($request, 'invoices.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $invoice->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        $invoice->load(['customer', 'items']);

        return $this->success([
            'invoice' => $invoice,
        ], 'Invoice fetched successfully.');
    }

    public function employees(Request $request)
    {
        if (! $this->tokenAllows($request, 'employees.read')) {
            return $this->error('Forbidden', 403);
        }

        $employees = Employee::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'employees' => $employees,
        ], 'Employees fetched successfully.');
    }

    public function employeeShow(Request $request, Employee $employee)
    {
        if (! $this->tokenAllows($request, 'employees.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $employee->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'employee' => $employee,
        ], 'Employee fetched successfully.');
    }

}
