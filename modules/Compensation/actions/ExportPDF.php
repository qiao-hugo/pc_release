<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

//vimport('~~/modules/Compensation/CompensationPDFController.php');
class Compensation_ExportPDF_Action extends Inventory_ExportPDF_Action {
    function checkPermission(Vtiger_Request $request) {
        set_time_limit(0);
        $record = $request->get('record');
        $fliter = $request->get('fliter');
        global $adb;
        $query='SELECT releasedate FROM `vtiger_compensation` WHERE compensationid=?';
        $result=$adb->pquery($query,array($record));
        $data=$adb->query_result_rowdata($result,'releasedate',0);
        if($fliter=='all'){
            $query="SELECT
				compensationid,
				CONCAT(releasedate, '合计') as releasedate,sum(fixedsalary) as fixedsalary,
				sum(meritpay) as meritpay,
				sum(allowancebonus) as allowancebonus,
				sum(percentage) as percentage,
				sum(replenishment) as replenishment,
				sum(debit) as debit,
				sum(wagejointventure) as wagejointventure,
				sum(individualsocialinsuran) as individualsocialinsuran,
				sum(accumulationfund) as accumulationfund,
				sum(individualincometax) as individualincometax,
				sum(taxdeductions) as taxdeductions,
				sum(realwage) as realwage

				FROM vtiger_compensation
				WHERE releasedate=?";
            $dataresult=$adb->pquery($query,array($data['releasedate']));
            $datas=$adb->query_result_rowdata($dataresult,0);
            $str='<table class="table listViewEntriesTable" border="1">
                <thead>
                    <tr>
                        <th nowrap="" data-field="releasedate" style="text-align:center;">月份</th>
                        <th nowrap="" data-field="fixedsalary" style="text-align:center;">固定工资</th>
                        <th nowrap="" data-field="meritpay" style="text-align:center;">绩效工资</th>
                        <th nowrap="" data-field="allowancebonus" style="text-align:center;">津贴奖金</th>
                        <th nowrap="" data-field="percentage" style="text-align:center;">提成</th>
                        <th nowrap="" data-field="replenishment" style="text-align:center;">补款</th>
                        <th nowrap="" data-field="debit" style="text-align:center;">扣款</th>
                        <th nowrap="" data-field="wagejointventure" style="text-align:center;">应发工资合计</th>
                        <th nowrap="" data-field="individualsocialinsuran" style="text-align:center;">社保个人扣款</th>
                        <th nowrap="" data-field="accumulationfund" style="text-align:center;">公积金个人扣款</th>
                        <th nowrap="" data-field="individualincometax" style="text-align:center;">个人所得税</th>
                        <th nowrap="" data-field="taxdeductions" style="text-align:center;">税后扣款</th>
                        <th nowrap="" data-field="realwage" style="text-align:center;">实发工资</th>
                    </tr>
                </thead>
                <tbody><tr>
                <td nowrap="" style="text-align:center;">'.$datas['releasedate'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['fixedsalary'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['meritpay'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['allowancebonus'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['percentage'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['replenishment'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['debit'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['wagejointventure'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['individualsocialinsuran'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['accumulationfund'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['individualincometax'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['taxdeductions'].'</td>
                <td nowrap="" style="text-align:center;">'.$datas['realwage'].'</td>

                </tr></tbody></table>';
            $title="月份合计";

        }else{
            $query='SELECT * FROM `vtiger_compensation` WHERE releasedate=?';
            $dataresult=$adb->pquery($query,array($data['releasedate']));
            $str='<table class="table listViewEntriesTable" border="1" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th nowrap=""style="text-align:center;">序号</th>
                            <th nowrap="" data-field="releasedate" style="text-align:center;">发放年月</th>
                            <th nowrap="" data-field="name" style="text-align:center;">姓名</th>
                            <th nowrap="" data-field="insuredtype" style="text-align:center;">参保类型</th>
                            <th nowrap="" data-field="fixedsalary" style="text-align:center;">固定工资</th>
                            <th nowrap="" data-field="meritpay" style="text-align:center;">绩效工资</th>
                            <th nowrap="" data-field="allowancebonus" style="text-align:center;">津贴奖金</th>
                            <th nowrap="" data-field="percentage" style="text-align:center;">提成</th>
                            <th nowrap="" data-field="replenishment" style="text-align:center;">补款</th>
                            <th nowrap="" data-field="debit" style="text-align:center;">扣款</th>
                            <th nowrap="" data-field="wagejointventure" style="text-align:center;">应发工资合计</th>
                            <th nowrap="" data-field="individualsocialinsuran" style="text-align:center;">社保个人扣款</th>
                            <th nowrap="" data-field="accumulationfund" style="text-align:center;">公积金个人扣款</th>
                            <th nowrap="" data-field="individualincometax" style="text-align:center;">个人所得税</th>
                            <th nowrap="" data-field="taxdeductions" style="text-align:center;">税后扣款</th>
                            <th nowrap="" data-field="realwage" style="text-align:center;">实发工资</th>
                        </tr>
                    </thead>
                    <tbody>';

            $fixedsalarysum=0;
            $meritpaysum=0;
            $allowancebonussum=0;
            $percentagesum=0;
            $replenishmentsum=0;
            $debitsum=0;
            $wagejointventuresum=0;
            $individualsocialinsuransum=0;
            $accumulationfundsum=0;
            $individualincometaxsum=0;
            $taxdeductionssum=0;
            $realwagesum=0;
            $num=$adb->num_rows($dataresult);
            for($i=0;$i<$num;$i++){
                $releasedate=$adb->query_result($dataresult,$i,'releasedate');
                $name=$adb->query_result($dataresult,$i,'name');
                $insuredtype=$adb->query_result($dataresult,$i,'insuredtype');
                $fixedsalary=$adb->query_result($dataresult,$i,'fixedsalary');
                $meritpay=$adb->query_result($dataresult,$i,'meritpay');
                $allowancebonus=$adb->query_result($dataresult,$i,'allowancebonus');
                $percentage=$adb->query_result($dataresult,$i,'percentage');
                $replenishment=$adb->query_result($dataresult,$i,'replenishment');
                $debit=$adb->query_result($dataresult,$i,'debit');
                $wagejointventure=$adb->query_result($dataresult,$i,'wagejointventure');
                $individualsocialinsuran=$adb->query_result($dataresult,$i,'individualsocialinsuran');
                $accumulationfund=$adb->query_result($dataresult,$i,'accumulationfund');
                $individualincometax=$adb->query_result($dataresult,$i,'individualincometax');
                $taxdeductions=$adb->query_result($dataresult,$i,'taxdeductions');
                $realwage=$adb->query_result($dataresult,$i,'realwage');


                $str .= '<tr style="height:8pt;line-height:8pt;">
                    <td style="text-align:center;">&nbsp;<br>'. ($i+1) . '</td>
                    <td style="text-align:center;">&nbsp;<br>' . $releasedate . '</td>
                    <td style="text-align:center;">&nbsp;<br>' . $name . '</td>
                    <td style="text-align:center;">&nbsp;<br>' . $insuredtype . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($fixedsalary==0?'':$fixedsalary) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($meritpay==0?'':$meritpay) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($allowancebonus==0?'':$allowancebonus) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($percentage==0?'':$percentage) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($replenishment==0?'':$replenishment) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($debit==0?'':$debit) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($wagejointventure==0?-'':$wagejointventure) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($individualsocialinsuran==0?'':$individualsocialinsuran) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($accumulationfund==0?'':$accumulationfund) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($individualincometax==0?'':$individualincometax) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($taxdeductions==0?'':$taxdeductions) . '</td>
                    <td nowrap style="text-align:center;">&nbsp;<br>' . ($realwage==0?'':$realwage) . '</td></tr>';
                $fixedsalarysum+=$fixedsalary;
                $meritpaysum+=$meritpay;
                $allowancebonussum+=$allowancebonus;
                $percentagesum+=$percentage;
                $replenishmentsum+=$replenishment;
                $debitsum+=$debit;
                $wagejointventuresum+=$wagejointventure;
                $individualsocialinsuransum+=$individualsocialinsuran;
                $accumulationfundsum+=$accumulationfund;
                $individualincometaxsum+=$individualincometax;
                $taxdeductionssum+=$taxdeductions;
                $realwagesum+=$realwage;
                if(false && ($i+1)==$num){
                    $str .= '<tr>
                        <td nowrap style="text-align:center;" colspan="4">&nbsp;<br>' . $data['releasedate'] . '合计</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$fixedsalarysum . '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$meritpaysum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$allowancebonussum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$percentagesum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$replenishmentsum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$debitsum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$wagejointventuresum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$individualsocialinsuransum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$accumulationfundsum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$individualincometaxsum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$taxdeductionssum. '</td>
                        <td nowrap style="text-align:center;">&nbsp;<br>'.$realwagesum. '</td>
                        </tr>';
                }
            }

            $str.='</tbody></table>';

            $title="月份明细";
            //echo $str;
            //exit;
        }
        $this->printpdf($str,$title);
        exit;

    }
    public function printpdf($html,$title){
        set_time_limit(0);
        global $root_directory;
        $font_family='stsongstdlight';//设置字体
        require_once($root_directory.'modules/Compensation/actions/tcpdf/tcpdf.php');
        //require_once($root_directory.'modules/Compensation/actions/tcpdf/examples/tcpdf_include.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'pt', PDF_PAGE_FORMAT, true, 'UTF-8', true);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('胡阿有');
        $pdf->SetTitle($title);
        $pdf->SetSubject('who are you');
        $pdf->SetKeywords('胡阿有');
        $pdf->SetMargins(20, 30, 30);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);

        $pdf->setFooterData(array(0,64,0), array(0,64,128));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //$pdf->setFontSubsetting(true);
        $pdf->setPrintHeader(false);
        //$pdf->setPrintFooter(false);

        $pdf->SetFont($font_family, '', 7,'',true);
        //$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //$pdf->AddPage();
        $pdf->AddPage('Landscape','A4');
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        $pdf->Output('whoareyou_001'.time().'.pdf', 'I');
    }


}
