<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use File;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {

        if(\Auth::user()->can('manage plan'))
        {
            if(\Auth::user()->type == 'super admin')
            {
                $plans = Plan::get();
            }
            else
            {
                $plans = Plan::where('is_disable', 1)->get();
            }
            $admin_payment_setting = Utility::getAdminPaymentSetting();

            return view('plan.index', compact('plans', 'admin_payment_setting'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create plan'))
        {
            $arrDuration = [
                'lifetime' => __('Lifetime'),
                'month' => __('Per Month'),
                'year' => __('Per Year'),
            ];

            return view('plan.create', compact('arrDuration'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {



        if(\Auth::user()->can('create plan'))
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();


                $validation                  = [];
                $validation['name']          = 'required|unique:plans';
                $validation['price']         = 'required|numeric|min:0';
                $validation['duration']      = 'required';
                $validation['max_users']     = 'required|numeric';
                $validation['max_customers'] = 'required|numeric';
                $validation['max_venders']   = 'required|numeric';
                $validation['storage_limit']   = 'required|numeric';

                if($request->image)
                {
                    $validation['image'] = 'required|max:20480';
                }
                $request->validate($validation);
                $post = $request->all();
                if(isset($request->enable_project))
                {
                    $post['project'] = 1;
                }
                if(isset($request->enable_crm))
                {
                    $post['crm'] = 1;
                }
                if(isset($request->enable_hrm))
                {
                    $post['hrm'] = 1;
                }
                if(isset($request->enable_account))
                {
                    $post['account'] = 1;
                }
                if(isset($request->enable_pos))
                {
                    $post['pos'] = 1;
                }
                if(isset($request->enable_production))
                {
                    $post['production'] = 1;
                }
                if(isset($request->enable_chatgpt))
                {
                    $post['chatgpt'] = 1;
                }
                if(isset($request->trial))
                {
                    $post['trial'] = 1;
                }
                if($request->hasFile('image'))
                {
                    $filenameWithExt = $request->file('image')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('image')->getClientOriginalExtension();
                    $fileNameToStore = 'plan_' . time() . '.' . $extension;

                    $dir = storage_path('uploads/plan/');
                    if(!file_exists($dir))
                    {
                        mkdir($dir, 0777, true);
                    }
                    $path          = $request->file('image')->storeAs('uploads/plan/', $fileNameToStore);
                    $post['image'] = $fileNameToStore;
                }



                if(Plan::create($post))
                {
                    return redirect()->back()->with('success', __('Plan Successfully created.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Something is wrong.'));
                }

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }


    public function edit($plan_id)
    {
        if(\Auth::user()->can('edit plan'))
        {
            $arrDuration = Plan::$arrDuration;
            $plan        = Plan::find($plan_id);

            return view('plan.edit', compact('plan', 'arrDuration'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $plan_id)
    {


        if(\Auth::user()->can('edit plan'))
        {
            $plan = Plan::find($plan_id);
            if(empty($plan))
            {
                return redirect()->back()->with('error', __('Plan not found.'));
            }

            $rules = [
                'name'          => 'required|unique:plans,name,' . $plan_id,
                'max_users'     => 'required|numeric',
                'max_customers' => 'required|numeric',
                'max_venders'   => 'required|numeric',
                'max_clients'   => 'required|numeric',
                'storage_limit' => 'required|numeric',
            ];

            if((int) $plan_id !== 1)
            {
                $rules['price'] = 'required|numeric|min:0';
                $rules['duration'] = 'required';
            }

            if($request->hasFile('image'))
            {
                $rules['image'] = 'max:20480';
            }

            if((int) $plan_id !== 1 && $request->has('trial'))
            {
                $rules['trial_days'] = 'required|integer|min:1';
            }

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $post = $request->all();

            $post['project'] = array_key_exists('enable_project', $post) ? 1 : 0;
            $post['crm'] = array_key_exists('enable_crm', $post) ? 1 : 0;
            $post['hrm'] = array_key_exists('enable_hrm', $post) ? 1 : 0;
            $post['account'] = array_key_exists('enable_account', $post) ? 1 : 0;
            $post['pos'] = array_key_exists('enable_pos', $post) ? 1 : 0;
            $post['production'] = array_key_exists('enable_production', $post) ? 1 : 0;
            $post['chatgpt'] = array_key_exists('enable_chatgpt', $post) ? 1 : 0;

            if((int) $plan_id !== 1 && $request->has('trial'))
            {
                $post['trial'] = 1;
                $post['trial_days'] = $request->trial_days;
            }
            else
            {
                $post['trial'] = 0;
                $post['trial_days'] = null;
            }

            if($request->hasFile('image'))
            {
                $filenameWithExt = $request->file('image')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('image')->getClientOriginalExtension();
                $fileNameToStore = 'plan_' . time() . '.' . $extension;

                $dir = storage_path('uploads/plan/');
                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }
                $image_path = $dir . '/' . $plan->image;
                if(File::exists($image_path))
                {
                    chmod($image_path, 0755);
                    File::delete($image_path);
                }
                $path = $request->file('image')->storeAs('uploads/plan/', $fileNameToStore);

                $post['image'] = $fileNameToStore;
            }

            if($plan->update($post))
            {
                return redirect()->back()->with('success', __('Plan successfully updated.'));
            }

            return redirect()->back()->with('error', __('Something is wrong.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function destroy(Request $request, $id)
    {
        $userPlan = User::where('plan' , $id)->first();
        if($userPlan != null)
        {
            return redirect()->back()->with('error',__('The company has subscribed to this plan, so it cannot be deleted.'));
        }
        $plan = Plan::find($id);
        if($plan->id == $id)
        {
            $plan->delete();

            return redirect()->back()->with('success' , __('Plan deleted successfully'));
        }
        else
        {
            return redirect()->back()->with('error',__('Something went wrong'));
        }
    }

    public function userPlan(Request $request)
    {
        $objUser = \Auth::user();
        try{
            $planID  = \Illuminate\Support\Facades\Crypt::decrypt($request->code);
        } catch (\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
        $plan    = Plan::find($planID);
        if($plan)
        {
            if($plan->price <= 0)
            {
                $objUser->assignPlan($plan->id);

                return redirect()->route('plans.index')->with('success', __('Plan successfully activated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan not found.'));
        }
    }

    public function planTrial(Request $request , $plan)
    {

        $objUser = \Auth::user();
        try{
            $planID  = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        } catch (\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
        $plan    = Plan::find($planID);

        if($plan)
        {
            if($plan->price > 0)
            {
                $user = User::find($objUser->id);
                $user->trial_plan = $planID;
                $currentDate = date('Y-m-d');
                $numberOfDaysToAdd = $plan->trial_days;

                $newDate = date('Y-m-d', strtotime($currentDate . ' + ' . $numberOfDaysToAdd . ' days'));
                $user->trial_expire_date = $newDate;
                $user->save();

                $objUser->assignPlan($planID);

                return redirect()->route('plans.index')->with('success', __('Plan successfully activated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan not found.'));
        }
    }

    public function planDisable(Request $request)
    {
        $userPlan = User::where('plan' , $request->id)->first();
        if($userPlan != null)
        {
            return response()->json(['error' =>__('The company has subscribed to this plan, so it cannot be disabled.')]);
        }

        Plan::where('id', $request->id)->update(['is_disable' => $request->is_disable]);

        if ($request->is_disable == 1) {
            return response()->json(['success' => __('Plan successfully enable.')]);

        } else {
            return response()->json(['success' => __('Plan successfully disable.')]);
        }
    }
}
