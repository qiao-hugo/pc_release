<?php
include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');
class Appraisal extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_vacate";
    var $table_index= 'vacateid';
    var $tab_name = Array(vtiger_vacate);
    var $tab_name_index = Array('vtiger_vacate'=>'vacateid');
    // Mandatory table for supporting custom fields.

    /*这两个参数是用来跟主表关联的（有效）
     * var $tab_name = Array('vtiger_crmentity','vtiger_salesorder');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_salesorder'=>'salesorderid');*/

    var $customFieldTable = Array('vtiger_salesordercf', 'salesorderid');
	var $entity_table = "vtiger_crmentity";
	var $billadr_table = "vtiger_sobillads";
	var $object_name = "";
	var $new_schema = true;
	var $update_product_array = Array();
	var $column_fields = Array();
	var $sortby_fields = Array('subject','smownerid','accountname','lastname');
	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );
	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(

				);

	var $list_fields_name = Array(
				        'Order No'=>'salesorder_no',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
				        'Quote Name'=>'quote_id',
					    'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id',
						'Status'=>'sostatus'
				      );
	var $list_link_field= 'subject';
	//弹出页面的搜索下拉字段 wangbin 
	var $search_fields = Array(
	);
	//弹出页面列表字段的显示控制 wangbin
	var $search_fields_name = Array(
	);
	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);
	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'subject';
	var $default_sort_order = 'ASC';
	//var $groupTable = Array('vtiger_sogrouprelation','salesorderid');
	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime', 'assigned_user_id');
	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;

    //右侧关联
    var $relatedmodule_list=array();
    var $relatedmodule_fields=array();

	/** Constructor Function for SalesOrder class
	 *  This function creates an instance of LoggerManager class using getLogger method
	 *  creates an instance for PearDatabase class and get values for column_fields array of SalesOrder class.
	 */
	function Appraisal() {
        echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>系统维护中</h2><p class="text">系统维护中!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
        exit;
		$this->log =LoggerManager::getLogger('Vacate');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Vacate');
	}

    function datedif($vacate_start,$vacate_end,$vacateid,$uid,$vacate_type){
        global $adb,$current_user;
        $result_beg = array();
        $result_end = array();
        $insert_detil = "INSERT INTO vtiger_vacate_detail (userid,start,end,vacatid,vacatetype,hours) VALUE (?,?,?,?,?,?)";
        $del_detail = "DELETE FROM  vtiger_vacate_detail WHERE vacatid = ?";
        $adb->pquery($del_detail,array($vacateid));
        $departmentid = $current_user->departmentid;
        $mowork_time = "";
        if($departmentid=='H9'||$departmentid=='H52'){
            $mowork_time = '8';
        }else{
            $mowork_time = '9';
        }
        $count = count($vacate_start);
       global $more1day ;
         $more1day = true;
        //echo (56%30);die;
        function count_hours($a,$b,$c,$mowork_time){
            $c_12 = strtotime('+12 hours',$c);
            $c_13 = strtotime('+1 hours 30 minutes',$c_12);
            $x = 0;
            if($b<=$c_12||$a>=$c_13){
                    if((($b-$a)%3600) == '0'){
                        $x = 0;
                    }elseif((($b-$a)%3600)<=1800){
                        $x = 0.5;
                    }else{
                       $x = 1;
                }
                return floor(($b-$a)/3600)+$x;
            }elseif($a<$c_12 && $b>$c_13){
                if((($b-$a)%3600) == '0'){
                    $x = 0;
                }elseif((($b-$a)%3600)<=1800){
                    $x = 0.5;
                }else{
                    $x = 1;
                }
                return (floor(($b-$a)/3600)-1.5)+$x;
            }elseif($a>=$c_12 && $b>=$c_13){
                return 0;
            }elseif($a>$c_12 && $a<$c_13){
                if((($b-$c_13)%3600) == '0'){
                    $x = 0;
                }elseif((($b-$c_13)%3600)<=1800){
                    $x = 0.5;
                }else{
                    $x = 1;
                }
                return floor(($b-$c_13)/3600)+$x;
            }elseif($b>$c_12 && $b<$c_13){
                if((($c_12-$a)%3600) == '0'){
                    $x = 0;
                }elseif((($c_12-$a)%3600)<=1800){
                    $x = 0.5;
                }else{
                    $x = 1;
                }
                return floor(($c_12-$a)/3600)+$x;
            }
        }
        for($i=0;$i<$count;$i++){
            $type=$vacate_type[$i];
            $beg  = strtotime($vacate_start[$i]);//请假开始时间
            $end  = strtotime($vacate_end[$i]);//请假结束时间

            $beg_d = strtotime(date("Y-m-d",$beg));
            $end_d = strtotime(date("Y-m-d",$end));
            $cel_d = ($end_d-$beg_d)/86400;

            if($mowork_time=='9'){
                $beg_morning = strtotime('+9 hours',$beg_d);//上班时间
                $end_d_morning = strtotime('+9 hours',$end_d);//上班时间
            }else{
                $beg_morning = strtotime('+8 hours 30 minutes',$beg_d);
                $end_d_morning = strtotime('+8 hours 30 minutes',$end_d);//上班时间
            }
            $beg_evening = strtotime('+9 hours 30 minutes',$beg_morning);//晚上下班时间
            $end_d_evening = strtotime('+9 hours 30 minutes',$end_d_morning);//晚上下班时间

            if($beg<$beg_morning)$beg=$beg_morning;
            if($beg>$beg_evening)$beg=$beg_evening;

            if($end<$end_d_morning)$end = $end_d_morning;
            if($end>$end_d_evening)$end = $end_d_evening;
            //如果是负的需要做一些判断
           if($cel_d=='0'){
               $more1day = false;
               $true_hours =  count_hours($beg,$end,$beg_d,$mowork_time);
               //echo $true_hours;
               if(Workday_Record_Model::get_daytype($beg)=='work') {
                   $adb->pquery($insert_detil, array($uid, date('Y-m-d H:i', $beg), date('Y-m-d H:i', $end), $vacateid, $type, $true_hours));
               }
           }elseif($cel_d>='1'){
                for($j=0;$j<=$cel_d;$j++){
                    if($j=='0'){
                        $true_hours =  count_hours($beg,$beg_evening,$beg_d,$mowork_time);
                        if(Workday_Record_Model::get_daytype($beg)=='work') {
                            $adb->pquery($insert_detil, array($uid, date('Y-m-d H:i', $beg), date('Y-m-d H:i', $beg_evening), $vacateid, $type, $true_hours));//开始~下班
                        }
                    }elseif($cel_d==$j){
                        $true_hours =  count_hours($end_d_morning,$end,$end_d,$mowork_time);
                        if(Workday_Record_Model::get_daytype($end_d_morning)=='work') {
                            $adb->pquery($insert_detil, array($uid, date('Y-m-d H:i', $end_d_morning), date('Y-m-d H:i', $end), $vacateid, $type, $true_hours));//上班~结束
                        }
                    }else{
                        $true_hours = '8';
                        if(Workday_Record_Model::get_daytype(strtotime('+' . $j . 'days', $beg_morning))=='work') {
                            $adb->pquery($insert_detil, array($uid, date('Y-m-d H:i', strtotime('+' . $j . 'days', $beg_morning)), date('Y-m-d H:i', strtotime('+' . $j . 'days', $beg_evening)), $vacateid, $type, $true_hours));//上班~下班
                        }
                    }
                }
           }



            //if(Workday_Record_Model::get_daytype($beg)=='work')
           // $m = floor((($cel_d%(3600*24))%3600)/60);
        }

    }
	function save_module($module){
       global $adb ,$current_user;
        $uid = $current_user->id;
        //var_dump($this->mode) ;die;
       $vacate_start =  $_REQUEST['vacate_start'];
       $vacate_end = $_REQUEST['vacate_end'];
       $vacate_type = $_REQUEST['vacate_type'];
       $vacateid = $this->id;
       $insert_orign = "INSERT INTO vtiger_vacate_original (vacatid,start,end,vacatetype) VALUES (?,?,?,?)";
       $delete_orign = "DELETE  FROM vtiger_vacate_original WHERE vacatid = ?";
       if(!empty($vacate_type)){
           $adb->pquery($delete_orign,array($vacateid));
          for($i=0;$i<count($vacate_type);$i++){
              $adb->pquery($insert_orign,array($vacateid,$vacate_start[$i],$vacate_end[$i],$vacate_type[$i]));
          }
       }
        $this->datedif($vacate_start,$vacate_end,$vacateid,$uid,$vacate_type);
       // echo $vacate_start[0].'<br>';
        //echo strtotime($vacate_start[0]).'<hr>';
        //var_dump(strtotime("+3 days ",'2015-12-18')) ;
        //echo date("Y-m-d",strtotime("+19 days ",'1450423020'));die;
    }

}

?>
