<?php

$workflows=array (
  0 => '370391',
  'workflowsid' => '370391',
  1 => '退单流程(含客服)',
  'workflowsname' => '退单流程(含客服)',
  2 => '0',
  'iscontract' => '0',
  3 => '1',
  'iscontent' => '1',
  4 => '0',
  'isattachment' => '0',
  5 => '0',
  'ischargeback' => '0',
  6 => 'SalesOrder',
  'mountmodule' => 'SalesOrder',
  'stage' => 
  array (
    1 => 
    array (
      0 => '429151',
      'workflowstagesid' => '429151',
      1 => '指定审核人',
      'workflowstagesname' => '指定审核人',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H83 |##| H84',
      'isrole' => 'H83 |##| H84',
      4 => '0',
      'iscost' => '0',
      5 => '1',
      'sequence' => '1',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '370391',
      'workflowsid' => '370391',
      9 => '8',
      'timelimit' => '8',
      10 => 'MyCheck',
      'handleaction' => 'MyCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_actioning',
      'modulestatus' => 'b_actioning',
      14 => '1',
      'isnextnode' => '1',
      15 => '',
      'workflowstagesflag' => '',
      'nostd' => 
      array (
      ),
    ),
    3 => 
    array (
      0 => '370394',
      'workflowstagesid' => '370394',
      1 => '执行部门退单成本核算',
      'workflowstagesname' => '执行部门退单成本核算',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H78 |##| H87 |##| H88',
      'isrole' => 'H78 |##| H87 |##| H88',
      4 => '0',
      'iscost' => '0',
      5 => '3',
      'sequence' => '3',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '370391',
      'workflowsid' => '370391',
      9 => '8',
      'timelimit' => '8',
      10 => 'ProductCheck',
      'handleaction' => 'ProductCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '1',
      'iseditdata' => '1',
      13 => 'b_actioning',
      'modulestatus' => 'b_actioning',
      14 => '0',
      'isnextnode' => '0',
      15 => NULL,
      'workflowstagesflag' => NULL,
      'nostd' => 
      array (
      ),
    ),
    4 => 
    array (
      0 => '370407',
      'workflowstagesid' => '370407',
      1 => '财务审核',
      'workflowstagesname' => '财务审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H127 |##| H90',
      'isrole' => 'H127 |##| H90',
      4 => '0',
      'iscost' => '0',
      5 => '4',
      'sequence' => '4',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '370391',
      'workflowsid' => '370391',
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
      'nostd' => 
      array (
      ),
    ),
    5 => 
    array (
      0 => '370420',
      'workflowstagesid' => '370420',
      1 => '客服经理',
      'workflowstagesname' => '客服经理',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H83 |##| H84',
      'isrole' => 'H83 |##| H84',
      4 => '0',
      'iscost' => '0',
      5 => '5',
      'sequence' => '5',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '370391',
      'workflowsid' => '370391',
      9 => '8',
      'timelimit' => '8',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => NULL,
      'iseditdata' => NULL,
      13 => NULL,
      'modulestatus' => NULL,
      14 => NULL,
      'isnextnode' => NULL,
      15 => NULL,
      'workflowstagesflag' => NULL,
      'nostd' => 
      array (
      ),
    ),
    6 => 
    array (
      0 => '370421',
      'workflowstagesid' => '370421',
      1 => '客服总监审核',
      'workflowstagesname' => '客服总监审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H83 |##| H84',
      'isrole' => 'H83 |##| H84',
      4 => '0',
      'iscost' => '0',
      5 => '6',
      'sequence' => '6',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '370391',
      'workflowsid' => '370391',
      9 => '8',
      'timelimit' => '8',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => NULL,
      'iseditdata' => NULL,
      13 => NULL,
      'modulestatus' => NULL,
      14 => NULL,
      'isnextnode' => NULL,
      15 => NULL,
      'workflowstagesflag' => NULL,
      'nostd' => 
      array (
      ),
    ),
  ),
);
?>