<?php

$workflows=array (
  0 => '2126542',
  'workflowsid' => '2126542',
  1 => '充值申请单退款审核流程',
  'workflowsname' => '充值申请单退款审核流程',
  2 => '0',
  'iscontract' => '0',
  3 => '1',
  'iscontent' => '1',
  4 => '0',
  'isattachment' => '0',
  5 => '0',
  'ischargeback' => '0',
  6 => 'RefillApplication',
  'mountmodule' => 'RefillApplication',
  7 => NULL,
  'dataupdate' => NULL,
  'stage' => 
  array (
    1 => 
    array (
      0 => '2131445',
      'workflowstagesid' => '2131445',
      1 => '部门负责人退款审核',
      'workflowstagesname' => '部门负责人退款审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H115 |##| H78 |##| H79 |##| H94',
      'isrole' => 'H115 |##| H78 |##| H79 |##| H94',
      4 => '0',
      'iscost' => '0',
      5 => '1',
      'sequence' => '1',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2126542',
      'workflowsid' => '2126542',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_check',
      'modulestatus' => 'b_check',
      14 => '0',
      'isnextnode' => '0',
      15 => 'DO_REFUND',
      'workflowstagesflag' => 'DO_REFUND',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    2 => 
    array (
      0 => '2131446',
      'workflowstagesid' => '2131446',
      1 => '财务充值退款审核',
      'workflowstagesname' => '财务充值退款审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H102',
      'isrole' => 'H102',
      4 => '0',
      'iscost' => '0',
      5 => '2',
      'sequence' => '2',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2126542',
      'workflowsid' => '2126542',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_check',
      'modulestatus' => 'b_check',
      14 => '0',
      'isnextnode' => '0',
      15 => 'DO_REFUND',
      'workflowstagesflag' => 'DO_REFUND',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    3 => 
    array (
      0 => '2131447',
      'workflowstagesid' => '2131447',
      1 => '申请人确认退款',
      'workflowstagesname' => '申请人确认退款',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '3',
      'sequence' => '3',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2126542',
      'workflowsid' => '2126542',
      9 => '0',
      'timelimit' => '0',
      10 => 'MyCheck',
      'handleaction' => 'MyCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'c_complete',
      'modulestatus' => 'c_complete',
      14 => '0',
      'isnextnode' => '0',
      15 => 'DO_REFUND',
      'workflowstagesflag' => 'DO_REFUND',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
  ),
);
?>