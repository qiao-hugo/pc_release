<?php

$workflows=array (
  0 => '371024',
  'workflowsid' => '371024',
  1 => '退单流程(不含客服)',
  'workflowsname' => '退单流程(不含客服)',
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
  7 => NULL,
  'dataupdate' => NULL,
  'stage' => 
  array (
    1 => 
    array (
      0 => '371025',
      'workflowstagesid' => '371025',
      1 => '商务总监审核',
      'workflowstagesname' => '商务总监审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H78 |##| H79',
      'isrole' => 'H78 |##| H79',
      4 => '0',
      'iscost' => '0',
      5 => '1',
      'sequence' => '1',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '371024',
      'workflowsid' => '371024',
      9 => '8',
      'timelimit' => '8',
      10 => 'ChiefCheck',
      'handleaction' => 'ChiefCheck',
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
      16 => '0',
      'reviewer' => '0',
      'nostd' => 
      array (
      ),
    ),
    2 => 
    array (
      0 => '371026',
      'workflowstagesid' => '371026',
      1 => '执行部门退单成本核算',
      'workflowstagesname' => '执行部门退单成本核算',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H88',
      'isrole' => 'H88',
      4 => '0',
      'iscost' => '0',
      5 => '2',
      'sequence' => '2',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '371024',
      'workflowsid' => '371024',
      9 => '8',
      'timelimit' => '8',
      10 => 'ProductCheck',
      'handleaction' => 'ProductCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '1',
      'iseditdata' => '1',
      13 => 'c_refund',
      'modulestatus' => 'c_refund',
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
    4 => 
    array (
      0 => '371027',
      'workflowstagesid' => '371027',
      1 => '财务审核',
      'workflowstagesname' => '财务审核',
      2 => '0',
      'isproductmanger' => '0',
      3 => 'H127 |##| H90 |##| H171',
      'isrole' => 'H127 |##| H90 |##| H171',
      4 => '0',
      'iscost' => '0',
      5 => '4',
      'sequence' => '4',
      6 => NULL,
      'manalert' => NULL,
      7 => NULL,
      'auditalert' => NULL,
      8 => '371024',
      'workflowsid' => '371024',
      9 => '8',
      'timelimit' => '8',
      10 => 'CommonCheck',
      'handleaction' => 'CommonCheck',
      11 => NULL,
      'subworkflowsid' => NULL,
      12 => '0',
      'iseditdata' => '0',
      13 => 'c_lackpayment',
      'modulestatus' => 'c_lackpayment',
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