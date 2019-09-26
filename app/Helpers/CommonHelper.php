<?php

namespace App\Helpers;
use Config;
use DB;
use Illuminate\Support\Facades\Auth;

class CommonHelper{
	
	public static function emailTemplateNames(){
		return array('admin_registration'=>'Admin Registration', 'user_registration' => 'User registration', 'survey_invitation' => 'Survey Invitation');
    }

    public static function getEmailTemplateName($key){
        $array = (new CommonHelper)->emailTemplateNames();
        return $array[$key];
    }

    public static function industryTypes(){
    	return array('Agriculture', 'Automotive', 'Aviation', 'Chemicals', 'Commercial Services', 'Conglomerates/ Mixed', 'Construction', 'Construction Materials', 'Consumer Durables', 'Energy', 'Energy Utilities', 'Equipment', 'Financial Services', 'Food and Beverage Products', 'Forest and Paper Products', 'Healthcare Products', 'Healthcare Services', 'Household and Personal Products', 'Logistics', 'Media', 'Metals Products', 'Mining', 'Non-Profit/Services', 'Other', 'Public Agency', 'Railroad', 'Real Estate', 'Retailers', 'Technology Hardware/Electronics', 'IT Products and Solutions', 'Telecommunications', 'Textiles and Apparel', 'Tourism/Leisure/Entertainment', 'Toys', 'Education/Universities', 'Waste Management', 'Water Utilities');
    }
}
