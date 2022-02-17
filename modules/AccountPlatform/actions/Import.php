<?php
/*+************
 * 独立文件上传
 *20141222
 **************/
class AccountPlatform_Import_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        $files=$_FILES['importFile'];
        $filetmp_name = $files['tmp_name'];
        $upload_file_path = decideFilePath();
        $file_name=$upload_file_path .time().'.xlsx';
        $upload_status = move_uploaded_file($filetmp_name, $file_name);
        if(!$upload_status){
            echo json_encode(array('success'=>false,'result'=>array('name'=>$files['name'])));
            exit;
        }
        $this->import($file_name);
    }

    /**
     * 导入
     * @param $fileName
     * @throws Exception
     */
    public function import($fileName){
        global $root_directory;
        include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($fileName)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($fileName)) {
                echo json_encode(array('success'=>false,'result'=>array('name'=>$fileName)));
            }
        }
        $PHPExcel = $PHPReader->load($fileName);
        $currentSheet = $PHPExcel->getSheet(0);
        /**取得一共有多少列*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();
        $all = array();
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
            $flag = 0;
            $col = array();
            for ($currentColumn = 'A'; $this->getascii($currentColumn) <= $this->getascii($allColumn); $currentColumn++) {
                $address = $currentColumn . $currentRow;
                $string = $currentSheet->getCell($address)->getValue();
                $col[$flag] = $string;
                $flag++;
            }
            $all[] = $col;
        }
        echo json_encode(array('success'=>true,'result'=>$all));
        exit;
    }

    /**
     * 读取字符串的ASCII码
     * @param $ch
     * @return int
     */
    function getascii( $ch) {
        if(strlen($ch) == 1){
            return ord($ch)-65;
        }
        return ord($ch[1])-38;
    }


}
