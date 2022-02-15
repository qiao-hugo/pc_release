<?php
error_reporting(0);
header("Content-type:text/html;charset=utf-8");
require('include/utils/UserInfoUtil.php');
$fileid=$_GET['fileid'];
$type=$_GET['type'];

if(empty($fileid)){
    die('文件不存在1!');
}
$fileid=(int)base64_decode($fileid);
if(!is_numeric($fileid) || $fileid<1){
    die('文件不存在2!');
}
//$fileid=10501;
global $adb;
$query="SELECT * FROM `vtiger_files` WHERE description in ('ServiceContracts','SupplierContracts') /*AND style IN ('files_style3','files_style4','files_style5','files_style6')*/ AND attachmentsid=?";
$typeId='attachmentsid';
if($type=='vmate'){
    $query="SELECT * FROM `vtiger_vmatefiles` WHERE  style IN ('files_style9') AND vmateattachmentsid=?";
    $typeId='vmateattachmentsid';
}
$result = $adb->pquery($query, array($fileid));
if($adb->num_rows($result)){
    $fileDetails = $adb->query_result_rowdata($result);
    $filePath = $fileDetails['path'];
    $newfilename=$fileDetails['newfilename'];
    if($newfilename>0){
        $savedFile = $fileDetails[$typeId]."_".$newfilename;
    }else{
        $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, 'UTF-8');
        $t_fileName = base64_encode($fileName);
        $t_fileName = str_replace('/', '', $t_fileName);
        $savedFile = $fileDetails[$typeId]."_".$t_fileName;
        if(!file_exists($filePath.$savedFile)){
            $savedFile = $fileDetails[$typeId]."_".$fileName;
        }
    }
    $fileNewPath=$filePath.$savedFile;
    if(!file_exists($fileNewPath)){
        die('文件不存在3!');
    }
    $name = $fileDetails['name'];
    $arr_name = explode(".",$name);
    $ex_name = array("jpg","jpeg");
    if(in_array(end($arr_name),$ex_name)){

    }else if (strtolower(end($arr_name)) == 'pdf'){
        $pdftext=file_get_contents($fileNewPath);
        $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
    }else{
        die('文件格式错误!');
    }

}else{
    die('文件不存在4!');
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <style type="text/css">
        #bodycontainer{
            position:relative;
        }
        .lightbox{
            position: fixed;
            top: 0px;
            left: 0px;
            height: 100%;
            width: 100%;
            z-index: 7;
            opacity: 0.3;
            display: block;
            background-color: rgb(0, 0, 0);
        }
        .pop{
            position: absolute;
            left: 50%;
            width: 894px;
            margin-left: -447px;
            z-index: 9;
            top: 30px;
        }
        #leftbutton,#rightbutton{
            width:64px;
            height:64px;
            position:absolute;
            top:50%;
            z-index:10;
            cursor:pointer;
        }
        #leftbutton{
            left:0;
            background-image:url('./libraries/images/previous_normal.png');
        }
        #leftbutton:hover{
            left:0;
            background-image:url('./libraries/images/previous_normal_down.png');
        }
        #rightbutton{
            right:0;
            background-image:url('./libraries/images/next_normal.png');
        }
        #rightbutton:hover{
            right:0;
            background-image:url('./libraries/images/next_normal_down.png');
        }
        #page{
            height:30px;
            width: 100%;
            line-height: 30px;
            text-align: center;
            bottom:0;
            position: fixed;
            z-index:10;
            cursor:pointer;
        }
        #topbutton{
            width:100%;
            height:30px;
            top:0;
            position: fixed;
            background-color: #b3b3b3;
            z-index:10;
            cursor:pointer;

        }
        #roate{
            width:30px;
            height:30px;
            top:0;
            margin: 0 auto;
            z-index:10;
            cursor:pointer;
            background-image:url('./libraries/images/roate.png');
            background-size: 30px 30px;
        }
    </style>
    <script src="libraries/jquery/jquery.min.js"></script>
    <script src="libraries/pdfjs/pdf.js"></script>
    <script type="text/javascript">
        $(function(){
            var container = document.getElementById("container");
            container.style.display = "block";
            var url = '<?=$fileNewPath?>';
            PDFJS.workerSrc = 'libraries/pdfjs/pdf.worker.js';
            var firstpage=1;
            var roate=0;
            var currnetpage=firstpage;
            var maxPage=<?=$num?>;
            getPDFPage(url,currnetpage);
            $('#leftbutton').hide();
            if(maxPage==1){
                $('#rightbutton').hide();
            }
            function getPDFPage(url,currnetpage,roate=0){
                PDFJS.getDocument(url).then(function (pdf) {
                    pdf.getPage(currnetpage).then(function (page) {
                        var scale = 1.5;
                        var viewport = page.getViewport(scale,roate);
                        var canvas = document.getElementById('the-canvas');
                        var context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        var renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
                        page.render(renderContext);
                    });
                    maxPage = pdf.numPages;
                    $('#page').html(currnetpage+'/'+maxPage);
                });
            }
            $('#leftbutton').on('click',function(){
                var oldcurrentpage=currnetpage;
                currnetpage=((currnetpage-1)<1)?1:(currnetpage-1);
                if(currnetpage==1){
                    $(this).hide();
                }
                if(currnetpage==maxPage-1){
                    $('#rightbutton').show();
                }
                getPDFPage(url,currnetpage);
            });
            $('#rightbutton').on('click',function(){
                currnetpage=((currnetpage+1)>=maxPage)?maxPage:(currnetpage+1);
                if(currnetpage==maxPage){
                    $(this).hide();
                }
                if(currnetpage==2){
                    $('#leftbutton').show();
                }
                getPDFPage(url,currnetpage);
            });
            $('#roate').on('click',function(){
                roate = (roate + 90) % 360;
                getPDFPage(url,currnetpage,roate);
            });
            $(document).contextmenu(function (e) {
                e.preventDefault();
            });

        });
    </script>
</head>
<body>

<?php if(in_array(strtolower(end($arr_name)),$ex_name)){ ?>
    <div id="container">
        <div style="text-align: center;">
            <img src="<?=$fileNewPath?>" />
        </div>
    </div>
<?php }?>

<?php if(strtolower(end($arr_name)) == 'pdf'){ ?>
    <div id="container" style="display: none;">
        <div class="lightbox"></div>
        <div id="leftbutton"></div>
        <div id="rightbutton"></div>
        <div id="topbutton">
            <div id="roate"></div>
        </div>
        <div id="page"></div>
        <div id="pop" class="pop">
            <canvas id="the-canvas"></canvas>
        </div>
    </div>
<?php }?>

</body>
</html>
