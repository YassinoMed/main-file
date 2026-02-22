<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'data',
        'is_read',
    ];

    public function toHtml()
    {
        $data       = json_decode($this->data);
        $link       = '#';
        $icon       = 'ti ti-bell';
        $icon_color = 'notif-system';
        $text       = '';
        $usr        = null;

        if(isset($data->updated_by) && !empty($data->updated_by))
        {
            $usr = User::find($data->updated_by);
        }

        if(!empty($usr))
        {
            // For Deals Notification
            if($this->type == 'assign_deal')
            {
                $link       = route('deals.show', [$data->deal_id,]);
                $text       = $usr->name . " " . __('Added you') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-user-plus";
                $icon_color = 'notif-crm';
            }

            if($this->type == 'create_deal_call')
            {
                $link       = route('deals.show', [$data->deal_id,]);
                $text       = $usr->name . " " . __('Create new Deal Call') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-phone";
                $icon_color = 'notif-crm';
            }

            if($this->type == 'update_deal_source')
            {
                $link       = route('deals.show', [$data->deal_id,]);
                $text       = $usr->name . " " . __('Update Sources') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-filter";
                $icon_color = 'notif-crm';
            }

            if($this->type == 'create_task')
            {
                $link       = route('deals.show', [$data->deal_id,]);
                $text       = $usr->name . " " . __('Create new Task') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-checkbox";
                $icon_color = 'notif-project';
            }

            if($this->type == 'add_product')
            {
                $link       = route('deals.show', [$data->deal_id,]);
                $text       = $usr->name . " " . __('Add new Products') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-package";
                $icon_color = 'notif-finance';
            }

            if($this->type == 'add_discussion')
            {
                $link       = route('deals.show', [$data->deal_id,]);
                $text       = $usr->name . " " . __('Add new Discussion') . " " . __('in deal') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-message-circle";
                $icon_color = 'notif-crm';
            }

            if($this->type == 'move_deal')
            {
                $link       = route('deals.show', [$data->deal_id,]);
                $text       = $usr->name . " " . __('Moved the deal') . " <b class='font-weight-bold'>" . $data->name . "</b> " . __('from') . " " . __(ucwords($data->old_status)) . " " . __('to') . " " . __(ucwords($data->new_status));
                $icon       = "ti ti-arrows-move";
                $icon_color = 'notif-crm';
            }
            // end deals

            // for estimations
            if($this->type == 'assign_estimation')
            {
                $link       = route('estimations.show', [$data->estimation_id,]);
                $text       = $usr->name . " " . __('Added you') . " " . __('in estimation') . " <b class='font-weight-bold'>" . $data->estimation_name . "</b> ";
                $icon       = "ti ti-file-invoice";
                $icon_color = 'notif-finance';
            }
            // end estimations

            // For Leads Notification
            if($this->type == 'assign_lead')
            {
                $link       = route('leads.show', [$data->lead_id,]);
                $text       = $usr->name . " " . __('Added you') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-user-plus";
                $icon_color = 'notif-crm';
            }

            if($this->type == 'create_lead_call')
            {
                $link       = route('leads.show', [$data->lead_id,]);
                $text       = $usr->name . " " . __('Create new Lead Call') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-phone";
                $icon_color = 'notif-crm';
            }

            if($this->type == 'update_lead_source')
            {
                $link       = route('leads.show', [$data->lead_id,]);
                $text       = $usr->name . " " . __('Update Sources') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-filter";
                $icon_color = 'notif-crm';
            }

            if($this->type == 'add_lead_product')
            {
                $link       = route('leads.show', [$data->lead_id,]);
                $text       = $usr->name . " " . __('Add new Products') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-package";
                $icon_color = 'notif-finance';
            }

            if($this->type == 'add_lead_discussion')
            {
                $link       = route('leads.show', [$data->lead_id,]);
                $text       = $usr->name . " " . __('Add new Discussion') . " " . __('in lead') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
                $icon       = "ti ti-message-circle";
                $icon_color = 'notif-crm';
            }

            if($this->type == 'move_lead')
            {
                $link       = route('leads.show', [$data->lead_id,]);
                $text       = $usr->name . " " . __('Moved the lead') . " <b class='font-weight-bold'>" . $data->name . "</b> " . __('from') . " " . __(ucwords($data->old_status)) . " " . __('to') . " " . __(ucwords($data->new_status));
                $icon       = "ti ti-arrows-move";
                $icon_color = 'notif-crm';
            }
            // end Leads

            $date = $this->created_at->diffForHumans();
            $html = '<a href="' . $link . '" class="notification-item ' . ($this->is_read ? '' : 'unread') . '" data-notification-id="' . $this->id . '">
                        <div class="notif-icon ' . $icon_color . '"><i class="' . $icon . '"></i></div>
                        <div class="notif-content">
                            <div class="notif-title">' . $text . '</div>
                            <div class="notif-time">' . $date . '</div>
                        </div>
                    </a>';
        }
        else
        {
            $html = '';
        }

        return $html;
    }
}
