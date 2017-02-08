<?php
// 生成文件 1G 左右
// $fp = fopen('qq.txt','w+');
// for( $i=0; $i<100000000; $i++ ){
//     $str = mt_rand(10000,9999999999)."\n";
//     fwrite($fp,$str);
// }
// fclose($fp);

$fileName = "qq.txt";


//分割文件
//分割大小
$size = 1024*1024*5;

$fp = fopen($fileName, 'r');
$fs = filesize($fileName);
$start = 0;
$i = 0;
while ($start < $fs) {
	$line = fread($fp, $size);

	if($line == "\n"){
		break;
	}
  //行尾
  $lastPt = strrpos($line, "\n");
	$start += $lastPt;
	$writeLine = substr($line, 0, $lastPt);

	$writeArr = explode("\n", $writeLine);

	$writeLine = implode($writeArr, "\n");

	file_put_contents("qq/".$fileName.'.'.$i, $writeLine);
	$i++;
	fseek($fp, $start+1);
	echo $start .PHP_EOL;
}
