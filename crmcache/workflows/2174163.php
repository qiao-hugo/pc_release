<?php

$workflows=array (
  0 => '2174163',
  'workflowsid' => '2174163',
  1 => '赠款流程出款',
  'workflowsname' => '赠款流程出款',
  2 => '0',
  'iscontract' => '0',
  3 => '0',
  'iscontent' => '0',
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
      0 => '2174164',
      'workflowstagesid' => '2174164',
      1 => '部门一级赠款出款审核',
      'workflowstagesname' => '部门一级赠款出款审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '1',
      'sequence' => '1',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2174163',
      'workflowsid' => '2174163',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_actioning',
      'modulestatus' => 'b_actioning',
      14 => '0',
      'isnextnode' => '0',
      15 => 'AUDIT_VERIFICATION',
      'workflowstagesflag' => 'AUDIT_VERIFICATION',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    2 => 
    array (
      0 => '2174165',
      'workflowstagesid' => '2174165',
      1 => '部门二级赠款出款审核',
      'workflowstagesname' => '部门二级赠款出款审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '2',
      'sequence' => '2',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2174163',
      'workflowsid' => '2174163',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_actioning',
      'modulestatus' => 'b_actioning',
      14 => '0',
      'isnextnode' => '0',
      15 => 'TWO_VERIFICATION',
      'workflowstagesflag' => 'TWO_VERIFICATION',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    3 => 
    array (
      0 => '2174166',
      'workflowstagesid' => '2174166',
      1 => '部门三级赠款出款审核',
      'workflowstagesname' => '部门三级赠款出款审核',
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
      8 => '2174163',
      'workflowsid' => '2174163',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_actioning',
      'modulestatus' => 'b_actioning',
      14 => '0',
      'isnextnode' => '0',
      15 => 'THREE_VERIFICATION',
      'workflowstagesflag' => 'THREE_VERIFICATION',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    4 => 
    array (
      0 => '2174169',
      'workflowstagesid' => '2174169',
      1 => '财务主管审核',
      'workflowstagesname' => '财务主管审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H89',
      'isrole' => 'H89',
      4 => '0',
      'iscost' => '0',
      5 => '4',
      'sequence' => '4',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2174163',
      'workflowsid' => '2174163',
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
      15 => '',
      'workflowstagesflag' => '',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    5 => 
    array (
      0 => '2174170',
      'workflowstagesid' => '2174170',
      1 => '出纳出款',
      'workflowstagesname' => '出纳出款',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H107',
      'isrole' => 'H107',
      4 => '0',
      'iscost' => '0',
      5 => '5',
      'sequence' => '5',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2174163',
      'workflowsid' => '2174163',
      9 => '0',
      'timelimit' => '0',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_actioning',
      'modulestatus' => 'b_actioning',
      14 => '0',
      'isnextnode' => '0',
      15 => 'UPDATEPAYMENTDATE',
      'workflowstagesflag' => 'UPDATEPAYMENTDATE',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    6 => 
    array (
      0 => '2174171',
      'workflowstagesid' => '2174171',
      1 => '提单人确认',
      'workflowstagesname' => '提单人确认',
      2 => '0',
      'isproductmanger' => '0',
      3 => '',
      'isrole' => '',
      4 => '0',
      'iscost' => '0',
      5 => '6',
      'sequence' => '6',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '2174163',
      'workflowsid' => '2174163',
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
      15 => 'BILL_CONFIRM',
      'workflowstagesflag' => 'BILL_CONFIRM',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
  ),
);
?>