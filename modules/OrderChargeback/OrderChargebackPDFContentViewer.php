<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
	include_once 'vtlib/Vtiger/PDF/inventory/ContentViewer.php';

	class OrderChargebackPDFContentViewer extends Vtiger_PDF_InventoryContentViewer {

        public function __construct(){
            //总宽度为190
            $this->cells = array( // Name => Width
                'Code'    => 30,
                'Name'    => 65,
                'Quantity'=> 30,
                'Price'   => 65,
            );
        }

        function initDisplay($parent) {

            $pdf = $parent->getPDF();

            $contentFrame = $parent->getContentFrame();
            $pdf->MultiCell($contentFrame->w, $contentFrame->h, "",0, 'L', 0, 1, $contentFrame->x, $contentFrame->y+7.4);

            // Defer drawing the cell border later.
            if(!$parent->onLastPage()) {
                //$this->displayWatermark($parent);
            }

            // Header
            $offsetX = 0;
            $pdf->SetFont('','B');
            foreach($this->cells as $cellName => $cellWidth) {
                $cellLabel = ($this->labelModel)? $this->labelModel->get($cellName, $cellName) : $cellName;
                //$pdf->MultiCell($cellWidth, $this->headerRowHeight, $cellLabel, 0, 'L', 0, 1, $contentFrame->x+$offsetX, $contentFrame->y);
                $offsetX += $cellWidth;
            }
            $pdf->SetFont('','');
            // Reset the y to use
            $contentFrame->y += $this->headerRowHeight;
        }

        function drawCellBorder($parent, $cellHeights=False) {
            $pdf = $parent->getPDF();
            $contentFrame = $parent->getContentFrame();

            if(empty($cellHeights)) $cellHeights = array();

            $offsetX = 0;
            foreach($this->cells as $cellName => $cellWidth) {
                $cellHeight = isset($cellHeights[$cellName])? $cellHeights[$cellName] : $contentFrame->h;

                $offsetY = $contentFrame->y-$this->headerRowHeight;

                $pdf->MultiCell($cellWidth, $cellHeight, "", 0, 'L', 0, 1, $contentFrame->x+$offsetX, $offsetY);
                $offsetX += $cellWidth;
            }
        }

        function displayPreLastPage($parent) {
            $models = $this->contentModels;
            //echo "<pre>";
            //print_r($models);
            $modelall=$models[0];
            $modelall=$modelall->get('all');

            $totalModels = count($models);
            $pdf = $parent->getPDF();

            $parent->createPage();
            $contentFrame = $parent->getContentFrame();

            $contentLineX = $contentFrame->x; $contentLineY = $contentFrame->y;
            $widthone=10;
            $widthtwo=$widthone+$this->cells['Code'];
            $widththree=$widthtwo+$this->cells['Name'];
            $widthfoure=$widththree+$this->cells['Quantity'];
            $heightone=31;
            $overflowOffsetHN = 8;
            /*$pdf->MultiCell($this->cells['Code'], 0, '退款申请单号:', 0, 'R', 0, 1, $widthone, $heightone);
            $pdf->SetFont('freeserif', '');
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['orderchargeback_no'], 0, 'L', 0, 1, $widthtwo, $heightone);*/
            $borderT="T";
            $borderL="L";
            $borderB="B";
            $borderR="R";
            $borderA=1;
            $borderN=0;
            $pdf->SetFont('stsongstdlight', '');
            $pdf->SetFont('cid0cs', '');
            $pdf->MultiCell($this->cells['Code'], 0, '退款申请单号', 0, 'R', 0, 1, $widththree, $heightone);
            //$pdf->SetFont('freeserif', '');
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['orderchargeback_no'], $borderN, 'L', 0, 1, $widthfoure, $heightone);
            //$pdf->SetFont('stsongstdlight', '');
            $pdf->MultiCell($this->cells['Code'], 0, '客户名称', $borderR.$borderB, 'C', 0, 1, $widthone, $heightone+$overflowOffsetHN);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['accountname'], $borderR.$borderB, 'C', 0, 1, $widthtwo, $heightone+$overflowOffsetHN);
            //$pdf->SetFont('stsongstdlight', '');
            $pdf->MultiCell($this->cells['Code'], 0, '业务类型', $borderR.$borderB, 'C', 0, 1, $widththree, $heightone+$overflowOffsetHN);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['servicecontractsclass'], $borderR.$borderB, 'C', 0, 1, $widthfoure, $heightone+$overflowOffsetHN);
            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Code'], 0, '银行账户', $borderR.$borderB, 'C', 0, 1, $widthone, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['banknumber'], $borderR.$borderB, 'C', 0, 1, $widthtwo, $contentheight);
            //$pdf->SetFont('stsongstdlight', '');
            $pdf->MultiCell($this->cells['Code'], 0, '开户行', $borderR.$borderB, 'C', 0, 1, $widththree, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['bankaccount'], $borderR.$borderB, 'C', 0, 1, $widthfoure, $contentheight);
            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Code'], 0, '开户名', $borderR.$borderB, 'C', 0, 1, $widthone, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['bankname'], $borderR.$borderB, 'C', 0, 1, $widthtwo, $contentheight);
            //$pdf->SetFont('stsongstdlight', '');
            $pdf->MultiCell($this->cells['Code'], 0, '银行代码', $borderR.$borderB, 'C', 0, 1, $widththree, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['bankcode'], $borderR.$borderB, 'C', 0, 1, $widthfoure, $contentheight);
            //$pdf->SetFont('stsongstdlight', '');

            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Code'], 0, '服务合同编号', $borderR.$borderB, 'C', 0, 1, $widthone, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['servicecontractsno'], $borderR.$borderB, 'C', 0, 1, $widthtwo, $contentheight);
            $pdf->MultiCell($this->cells['Code'], 0, '合同总金额', $borderR.$borderB, 'C', 0, 1, $widththree, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['contractamount'], $borderR.$borderB, 'C', 0, 1, $widthfoure, $contentheight);

            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Code'], 0, '已收回款总金额', $borderR.$borderB, 'C', 0, 1, $widthone, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['receivingmoney'],$borderR.$borderB, 'C', 0, 1, $widthtwo, $contentheight);
            $pdf->MultiCell($this->cells['Code'], 0, '已执行成本', $borderR.$borderB, 'C', 0, 1, $widththree, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['executedcost'], $borderR.$borderB, 'C', 0, 1, $widthfoure, $contentheight);


            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Code'], 0, '退款金额',$borderR.$borderB, 'C', 0, 1, $widthone, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['refundamount'], $borderR.$borderB, 'C', 0, 1, $widthtwo, $contentheight);
            $pdf->MultiCell($this->cells['Code'], 0, '退款原因', $borderR.$borderB, 'C', 0, 1, $widththree, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['refundreason'],"B", 'C', 0, 1, $widthfoure, $contentheight);

            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Code'], 0, '原合同处理结果', $borderR.$borderB, 'C', 0, 1, $widthone, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['originalcontractprocessing'], $borderR."B", 'C', 0, 1, $widthtwo, $contentheight);
            $pdf->MultiCell($this->cells['Code'], 0, '退款处理结果', $borderR.$borderB, 'C', 0, 1, $widththree, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, $modelall['processingresult'],$borderB, 'C', 0, 1, $widthfoure, $contentheight);



            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Code'], 0, '退款/作废原因', $borderN,'C', 0, 1, $widthone, $contentheight);
            $pdf->MultiCell($this->cells['Name']+$this->cells['Code']+$this->cells['Name'], 0, $modelall['changebackdescribe'], "L", 'L', 0, 1, $widthtwo, $contentheight);
            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Name']+$this->cells['Code']+$this->cells['Name']+$this->cells['Code'], $contentheight-32.5, '', $borderA, 'C', 0, 1, $widthone, $heightone+$overflowOffsetHN);
            $pdf->MultiCell($this->cells['Name']-20, 0, '商务代表', $borderN.$borderL, 'R', 0, 1, $widthtwo, $contentheight);
            $pdf->MultiCell($this->cells['Code']+40, 0, $modelall['userName'], $borderN, 'L', 0, 1, $widththree-20, $contentheight);
            $pdf->MultiCell($this->cells['Name'], 0, '日期  '.$modelall['applytime'],$borderN, 'C', 0, 1, $widthfoure, $contentheight);
            //$pdf->Ln();cid0cs
            //$pdf->SetFont('cid0cs', '');
            foreach($modelall['resultall'] as $value){
                $contentheight=$pdf->GetY();
                $pdf->MultiCell($this->cells['Name']+$this->cells['Code']+$this->cells['Name']+$this->cells['Code'], 0, $value['workflowstagesname'].'　'.$value['auditorid'].'　'.$value['auditortime'], $borderL.$borderR, 'L', 0, 1, $widthone,$contentheight);
                if(!empty($modelall['resultremarklist'][$value['salesorderworkflowstagesid']])){
                    foreach($modelall['resultremarklist'][$value['salesorderworkflowstagesid']] as $val){
                        $contentheight=$pdf->GetY();
                        //$rejectstring=mb_substr($val['reject'],0,300);
                        $pdf->SetTextColor(60,60,60);
                        $pdf->MultiCell($this->cells['Name']+$this->cells['Code']+$this->cells['Name']+$this->cells['Code'], 0, '     '.$val['reject'].'　'.$val['rejectid'], $borderL.$borderR, 'L', 0, 1, $widthone,$contentheight);
                        $pdf->SetTextColor(0,0,0);
                    }
                }
            }
            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Name']+$this->cells['Code']+$this->cells['Name']+$this->cells['Code'], 0,'', $borderT.$borderB, 'L', 0, 1, $widthone,$contentheight);
            $pdf->MultiCell($this->cells['Code'], 0,'备注', $borderL, 'C', 0, 1, $widthone,$contentheight);
            $pdf->MultiCell($this->cells['Name']+$this->cells['Code']+$this->cells['Name'], 0,'', $borderL.$borderR, 'L', 0, 1, $widthtwo,$contentheight);
            $pdf->Ln();
            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Name']+$this->cells['Code']+$this->cells['Name']+$this->cells['Code'], 0,'说明:', $borderN, 'L', 0, 1, $widthone,$contentheight);
            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Name']+$this->cells['Code']+$this->cells['Name']+$this->cells['Code'], 0,'1、如果部门已经发放提成，责任认定部门需将已发放的所有提成，奖金退回财务部。', $borderN, 'L', 0, 1, $widthone,$contentheight);
            $contentheight=$pdf->GetY();
            $pdf->MultiCell($this->cells['Name']+$this->cells['Code']+$this->cells['Name']+$this->cells['Code'], 0,'2、每月25日前流程审核节点到财务部门的，财务部将于月末最后一日办理退款；以入款来源退还原款。', $borderN, 'L', 0, 1, $widthone,$contentheight);
            $overflowOffsetH = 8; // This is offset used to detect overflow to next page
            /*for ($index = 0; $index < $totalModels; ++$index) {
                $model = $models[$index];

                $contentHeight = 1;

                // Determine the content height to use
                foreach($this->cells as $cellName => $cellWidth) {
                    $contentString = $model->get($cellName);
                    if(empty($contentString)) continue;
                    $contentStringHeight = $pdf->GetStringHeight($contentString, $cellWidth);
                    if ($contentStringHeight > $contentHeight) $contentHeight = $contentStringHeight;
                }

                // Are we overshooting the height?
                if(ceil($contentLineY + $contentHeight) > ceil($contentFrame->h+$contentFrame->y)) {

                    $this->drawCellBorder($parent);
                    $parent->createPage();

                    $contentFrame = $parent->getContentFrame();
                    $contentLineX = $contentFrame->x; $contentLineY = $contentFrame->y;
                }
                $offsetX = 0;
                $pdf->MultiCell(30, 0, 'aaadd', 1, 'L', 0, 1, 0, 0);
                foreach($this->cells as $cellName => $cellWidth) {
                    $pdf->MultiCell($cellWidth, $contentHeight, $model->get($cellName).'aaadd', 1, 'L', 0, 1, $contentLineX+$offsetX, $contentLineY);
                    $offsetX += $cellWidth;
                }

                $contentLineY = $pdf->GetY();
                $commentContent = $model->get('Comment');

                if (!empty($commentContent)) {
                    $commentCellWidth = $this->cells['Name'];
                    $offsetX = $this->cells['Code'];

                    $contentHeight = $pdf->GetStringHeight($commentContent, $commentCellWidth);
                    if(ceil($contentLineY + $contentHeight + $overflowOffsetH) > ceil($contentFrame->h+$contentFrame->y)) {

                        $this->drawCellBorder($parent);
                        $parent->createPage();

                        $contentFrame = $parent->getContentFrame();
                        $contentLineX = $contentFrame->x; $contentLineY = $contentFrame->y;
                    }
                    $pdf->MultiCell($commentCellWidth, $contentHeight, $model->get('Comment'), 0, 'L', 0, 1, $contentLineX+$offsetX,
                        $contentLineY);

                    $contentLineY = $pdf->GetY();
                }
            }*/

            // Summary

            $cellHeights = array();
            if ($this->contentSummaryModel) {
                $summaryCellKeys = $this->contentSummaryModel->keys(); $summaryCellCount = count($summaryCellKeys);

                $summaryCellLabelWidth = $this->cells['Quantity'] + $this->cells['Price'] + $this->cells['Discount'] + $this->cells['Tax'];
                $summaryCellHeight = $pdf->GetStringHeight("TEST", $summaryCellLabelWidth); // Pre-calculate cell height

                $summaryTotalHeight = ceil(($summaryCellHeight * $summaryCellCount));

                if (($contentFrame->h+$contentFrame->y) - ($contentLineY+$overflowOffsetH)  < $summaryTotalHeight) { //$overflowOffsetH is added so that last Line Item is not overlapping
                    $this->drawCellBorder($parent);
                    $parent->createPage();

                    $contentFrame = $parent->getContentFrame();
                    $contentLineX = $contentFrame->x; $contentLineY = $contentFrame->y;
                }

                $summaryLineX = $contentLineX + $this->cells['Code'] + $this->cells['Name'];
                $summaryLineY = ($contentFrame->h+$contentFrame->y-$this->headerRowHeight)-$summaryTotalHeight;

                foreach($summaryCellKeys as $key) {
                    $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, $key, 1, 'L', 0, 1, $summaryLineX, $summaryLineY);
                    $pdf->MultiCell($contentFrame->w-$summaryLineX+10-$summaryCellLabelWidth, $summaryCellHeight,
                        $this->contentSummaryModel->get($key), 1, 'R', 0, 1, $summaryLineX+$summaryCellLabelWidth, $summaryLineY);
                    $summaryLineY = $pdf->GetY();
                }

                $cellIndex = 0;
                foreach($this->cells as $cellName=>$cellWidth) {
                    if ($cellIndex < 2) $cellHeights[$cellName] = $contentFrame->h;
                    else $cellHeights[$cellName] = $contentFrame->h - $summaryTotalHeight;
                    ++$cellIndex;
                }
            }
            $this->onSummaryPage = true;
            $this->drawCellBorder($parent, $cellHeights);
        }

}
?>