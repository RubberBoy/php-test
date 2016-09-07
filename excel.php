<?php

require_once dirname(__FILE__) . '/pexcel/PHPExcel.php';
require_once dirname(__FILE__) . '/pexcel/PHPExcel/IOFactory.php';
require_once dirname(__FILE__) . '/pexcel/PHPExcel/Reader/Excel5.php';

echo date("h:i:sa") . "\r\n";

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("/Users/gaosheng/图库标题和标签.xlsx");

$sheet = $objPHPExcel->getActiveSheet();
$highestRow = $sheet->getHighestRow(); // 取得总行数
$highestColumn = $sheet->getHighestColumn(); // 取得总列数
$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

echo "totalRows:" . $highestRow . " totalColumns:" . $highestColumn . "\r\n";
echo date("h:i:sa") . "\r\n";

//var_dump($objPHPExcel->getActiveSheet()->toArray(null,true,true,true));
//echo date("h:i:sa") . "\r\n";

$data = array();
for ($row = 2; $row <= $highestRow; $row++) {
    $index = $row - 2;
    $data[] = array();
    $data[$index]['id'] = $sheet->getCellByColumnAndRow(0, $row)->getCalculatedValue();
    $data[$index]['title'] = $sheet->getCellByColumnAndRow(1, $row)->getCalculatedValue();
    $data[$index]['fileName'] = $sheet->getCellByColumnAndRow(2, $row)->getCalculatedValue();
    $data[$index]['tags'] = $sheet->getCellByColumnAndRow(3, $row)->getCalculatedValue();
//                $data[$index]['pageUrl'] = $sheet->getCellByColumnAndRow(4, $row)->getCalculatedValue();

    echo $data[$index]['id'] . " ";
    if ($row % 20 == 0){
        $temp = import($data);
        break;
    }
}



function import($params) {
    $pictureSqlTemp = "INSERT INTO TM_Deco_GalleryPicture (Deco_GalleryPicture_Title, Deco_GalleryPicture_Path, Deco_GalleryPicture_Uuid) VALUES ";
    $tagSqlTemp = "INSERT INTO TM_Deco_DesignResTag (Deco_DesignResTag_ResId, Deco_DesignResTag_ResType, Deco_DesignResTag_TagId, Deco_DesignResTag_Active) VALUES ";

    $pictureNum = 1;
    $pictureSql = $pictureSqlTemp;

    $tagNum = 1;
    $tagSql = $tagSqlTemp;

    $errorTag = "";
    foreach ($params as $param){
        $uuid = md5(uniqid(mt_rand(), true));
        $tags = empty($param['tags']) ? null : explode("|", $param['tags']);

        $pictureSql .= "('" . $param['title'] . "','" . $param['fileName'] . "','" . $uuid . "') ";
        if ($pictureNum % 200 == 0) {
            echo $pictureSql . "\r\n";
            $pictureSql = $pictureSqlTemp;
        } else {
            $pictureSql .= ",";
        }
        $pictureNum++;

        if ( ! empty($tags)) {
            foreach ($tags as $tag) {
                $tagId = $tag;
                if ( ! empty($tagId)) {
                    $tagSql .= "(-1, '" . $uuid . "','" . $tagId . "', 1) ";
                    if ($tagNum % 200 == 0) {
                        echo $tagSql . "\r\n";
                        $tagSql = $tagSqlTemp;
                    } else {
                        $tagSql .= ",";
                    }
                    $tagNum++;
                } else {
                    $errorTag .= $tag . "、";
                }
            }
        }
    }

    if ($pictureSql != $pictureSqlTemp) {
        echo trim($pictureSql, ",") . "\r\n";;
    }
    if ($tagSql != $tagSqlTemp) {
        echo trim($tagSql, ",") . "\r\n";;
    }

}