<?php

$workflows=array (
  0 => '380317',
  'workflowsid' => '380317',
  1 => '效果营销/无锡SEM充值流程',
  'workflowsname' => '效果营销/无锡SEM充值流程',
  2 => '1',
  'iscontract' => '1',
  3 => '0',
  'iscontent' => '0',
  4 => '0',
  'isattachment' => '0',
  5 => '0',
  'ischargeback' => '0',
  6 => 'SalesOrder',
  'mountmodule' => 'SalesOrder',
  7 => NULL,
  'dataupdate' => NULL,
  'stage' => 
  array (
    1 => 
    array (
      0 => '380318',
      'workflowstagesid' => '380318',
      1 => '项目/产品经理',
      'workflowstagesname' => '项目/产品经理',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H105 |##| H86',
      'isrole' => 'H105 |##| H86',
      4 => '0',
      'iscost' => '0',
      5 => '1',
      'sequence' => '1',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '380317',
      'workflowsid' => '380317',
      9 => '8',
      'timelimit' => '8',
      10 => 'ProductCheck',
      'handleaction' => 'ProductCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '1',
      'iseditdata' => '1',
      13 => 'b_check',
      'modulestatus' => 'b_check',
      14 => '0',
      'isnextnode' => '0',
      15 => NULL,
      'workflowstagesflag' => NULL,
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    2 => 
    array (
      0 => '380319',
      'workflowstagesid' => '380319',
      1 => '财务成本审核',
      'workflowstagesname' => '财务成本审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H127 |##| H90 |##| H171',
      'isrole' => 'H127 |##| H90 |##| H171',
      4 => '0',
      'iscost' => '0',
      5 => '2',
      'sequence' => '2',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '380317',
      'workflowsid' => '380317',
      9 => '8',
      'timelimit' => '8',
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
      15 => '',
      'workflowstagesflag' => '',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    3 => 
    array (
      0 => '2137283',
      'workflowstagesid' => '2137283',
      1 => '提单人关联回款',
      'workflowstagesname' => '提单人关联回款',
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
      8 => '380317',
      'workflowsid' => '380317',
      9 => '0',
      'timelimit' => '0',
      10 => 'MyCheck',
      'handleaction' => 'MyCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'b_check',
      'modulestatus' => 'b_check',
      14 => '0',
      'isnextnode' => '0',
      15 => 'RAYMENT_MATCH',
      'workflowstagesflag' => 'RAYMENT_MATCH',
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    4 => 
    array (
      0 => '380320',
      'workflowstagesid' => '380320',
      1 => 'SEM充值',
      'workflowstagesname' => 'SEM充值',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H102 |##| H105',
      'isrole' => 'H102 |##| H105',
      4 => '0',
      'iscost' => '0',
      5 => '4',
      'sequence' => '4',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '380317',
      'workflowsid' => '380317',
      9 => '8',
      'timelimit' => '8',
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
      0 => '380324',
      'workflowstagesid' => '380324',
      1 => '产品经理审核',
      'workflowstagesname' => '产品经理审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H105 |##| H86',
      'isrole' => 'H105 |##| H86',
      4 => '0',
      'iscost' => '0',
      5 => '5',
      'sequence' => '5',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '380317',
      'workflowsid' => '380317',
      9 => '0',
      'timelimit' => '0',
      10 => 'ProductCheck',
      'handleaction' => 'ProductCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'c_complete',
      'modulestatus' => 'c_complete',
      14 => '0',
      'isnextnode' => '0',
      15 => NULL,
      'workflowstagesflag' => NULL,
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    6 => 
    array (
      0 => '380325',
      'workflowstagesid' => '380325',
      1 => '财务尾款',
      'workflowstagesname' => '财务尾款',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H127 |##| H90 |##| H171',
      'isrole' => 'H127 |##| H90 |##| H171',
      4 => '0',
      'iscost' => '0',
      5 => '6',
      'sequence' => '6',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '380317',
      'workflowsid' => '380317',
      9 => '8',
      'timelimit' => '8',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'c_complete',
      'modulestatus' => 'c_complete',
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
  ),
);
?>