<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
	include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewer.php';

	class OrderChargebackPDFHeaderViewer extends Vtiger_PDF_InventoryHeaderViewer {

		function display($parent) {
			$pdf = $parent->getPDF();
            $font_family='stsongstdlight';//设置字体
			$headerFrame = $parent->getHeaderFrame();
			if($this->model) {
				$headerColumnWidth = $headerFrame->w/3.0;
               	$modelColumns = $this->model->get('columns');
                //exit;
				// Column 1
				$offsetX = 5;

				$modelColumnLeft = $modelColumns[0];

				/*list($imageWidth, $imageHeight, $imageType, $imageAttr) = getimagesize($modelColumnLeft['logo']);
				//division because of mm to px conversion
				$w = $imageWidth/3;
				if($w > 60) {
					$w=60;
				}
				$h = $imageHeight/3;
				if($h > 30) {
					$h = 30;
				}
				$pdf->Image($modelColumnLeft['logo'], $headerFrame->x, $headerFrame->y, $w, $h);*/
				//$imageHeightInMM = 20;

				/*$pdf->SetFont($font_family, 'B');
				$contentHeight = $pdf->GetStringHeight( $modelColumnLeft['summary'], $headerColumnWidth);
				$pdf->MultiCell($headerColumnWidth, $contentHeight, $modelColumnLeft['summary'], 1, 'L', 0, 1,
					$headerFrame->x, $headerFrame->y+$imageHeightInMM+2);*/
                /*
				$pdf->SetFont($font_family, '');
				$contentHeight = $pdf->GetStringHeight( $modelColumnLeft['content'], $headerColumnWidth);
				$pdf->MultiCell($headerColumnWidth, $contentHeight, $modelColumnLeft['content'], 1, 'L', 0, 1,
					$headerFrame->x, $pdf->GetY());
                */

				if(!empty($modelColumnLeft['fieldvalue'])) {
					/*$pdf->SetFont($font_family, 'B');
					$pdf->SetFillColor(205,201,201);
					$height = $pdf->GetStringHeight($modelColumnLeft['fieldlabel'], $headerColumnWidth);
					$pdf->MultiCell($headerColumnWidth, 7, $modelColumnLeft['fieldlabel']."eeeww", 1, 'C', 1, 1, $headerFrame->x, $pdf->GetY()+2);

					$pdf->SetFont($font_family, '');
					$height = $pdf->GetStringHeight($modelColumnLeft['fieldvalue'], $headerColumnWidth);
					$pdf->MultiCell($headerColumnWidth, 7, $modelColumnLeft['fieldvalue'], 1, 'C', 0, 1, $headerFrame->x, $pdf->GetY());*/
				}

				/*// Column 2
				$offsetX = 5;
				$pdf->SetY($headerFrame->y);

				$modelColumnCenter = $modelColumns[1];
				$offsetY = 8;
				foreach($modelColumnCenter as $label => $value) {

					if(!empty($value)) {
						$pdf->SetFont($font_family, 'B');
						$pdf->SetFillColor(205,201,201);
						$pdf->MultiCell($headerColumnWidth-$offsetX, 7, $label, 1, 'C', 1, 1, $headerFrame->x+$headerColumnWidth+$offsetX, $pdf->GetY()+$offsetY);

						$pdf->SetFont($font_family, '');
						$pdf->MultiCell($headerColumnWidth-$offsetX, 7, $value, 1, 'C', 0, 1, $headerFrame->x+$headerColumnWidth+$offsetX, $pdf->GetY());
						$offsetY = 2;
					}
				}*/

				// Column 3
				$offsetX = 10;

				$modelColumnRight = $modelColumns[2];

				$contentWidth = $pdf->GetStringWidth($this->model->get('title'));
				$contentHeight = $pdf->GetStringHeight($this->model->get('title'), $contentWidth);
                //print_r($headerFrame);
				$roundedRectX = $headerFrame->w+$headerFrame->x-$contentWidth*2.0;
				$roundedRectW = $contentWidth*2.0;

				//$pdf->RoundedRect($roundedRectX, 10, $roundedRectW, 10, 3, '1111', 'DF', array(), array(205,201,201));

				$contentX = $roundedRectX + (($roundedRectW - $contentWidth)/2.0);
				$pdf->SetFont($font_family, 'B',20);
				$pdf->MultiCell(0, 0,'退款/作废申请单', 0, 'C', 0, 1, 0,$headerFrame->y+2);
                $pdf->SetFont($font_family, 'B',10);
				$offsetY = 6;
				/*foreach($modelColumnRight as $label => $value) {
					if(is_array($value)) {
						//$pdf->SetFont('freeserif', '');
						$pdf->SetFont($font_family, '');
						foreach($value as $l => $v) {
							$pdf->MultiCell($headerColumnWidth-$offsetX, 7, sprintf('%s: %s', $l, $v), 1, 'C', 0, 1,
								$headerFrame->x+$headerColumnWidth*2.0+$offsetX, $pdf->GetY()+$offsetY);
							$offsetY = 0;
						}
					} else {
						$offsetY = 1;

					//$pdf->SetFont('freeserif', 'B');
					$pdf->SetFont($font_family, 'B');
					$pdf->SetFillColor(205,201,201);
					$pdf->MultiCell($headerColumnWidth-$offsetX, 7, $label, 1, 'L', 1, 1, $headerFrame->x+$headerColumnWidth*2.0+$offsetX,
						$pdf->GetY()+$offsetY);

					//$pdf->SetFont('freeserif', '');
					$pdf->SetFont($font_family, '');
					$pdf->MultiCell($headerColumnWidth-$offsetX, 7, $value, 1, 'L', 0, 1, $headerFrame->x+$headerColumnWidth*2.0+$offsetX,
						$pdf->GetY());
					}
				}*/
				//$pdf->setFont('freeserif', '');
				$pdf->setFont($font_family, '');

				// Add the border cell at the end
				// This is required to reset Y position for next write
				$pdf->MultiCell(0, 0, "", 0, 'L', 0, 1, 0, $headerFrame->y+15);
			}
		}
}
?>