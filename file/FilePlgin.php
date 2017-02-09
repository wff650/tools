<?php
// 生成文件 1G 左右
// $fp = fopen('qq.txt','w+');
// for( $i=0; $i<100000000; $i++ ){
//     $str = mt_rand(10000,9999999999)."\n";
//     fwrite($fp,$str);
// }
// fclose($fp);
// exit;
$fileName = "qq.txt";

class FilePlgin{


	private static function getDefaultOutPath(){
		return "/tmp/FilePlgin" . date("Ymd") . '/';
	}

	public static function getDefaultSize(){
		return 1024*1024*5;
		// return 100;
	}
	/**
	 * 分割文件
	 * @param  string $fileName 文件路径
	 * @param  string $outPath 被分割到的路径
	 * @param  int $size     分割大小
	 * @param  string $fun 函数
	 * @return void           
	 */
	public static function fileSplit($fileName, $outPath = false, $size = false, $fun = ''){
		$outPath = $outPath === false ? self::getDefaultOutPath() : $outPath;
		$size = $size === false ?self::getDefaultSize() : $size;
		if(!file_exists($outPath)){
			mkdir($outPath);
		}
		$fp = fopen($fileName, 'r');
		$fs = filesize($fileName);
		$start = 0;
		$i = 0;
		while ($start < $fs) {
			$line = fread($fp, $size);

			if($line == "\n"){
				break;
			}
			$lastPt = strrpos($line, "\n");
			$start += $lastPt;
			$writeLine = substr($line, 0, $lastPt);
			unset($line);
			if($fun != ''){
				$writeArr = explode("\n", $writeLine);
				$fun($writeArr);
				$writeLine = implode($writeArr, "\n");
				unset($writeArr);
			}
			$outFileName = $outPath.$fileName.'.'.$i;
			$outFileNames[]  = $outFileName;
			file_put_contents($outFileName, $writeLine);
			unset($writeLine);
			$i++;
			$start++;
			fseek($fp, $start);
		}
		fclose($fp);
		return $outFileNames;
	}

	/**
	 * 对文件内容进行排序
	 * @param  string  $fileName 文件路径
	 * @param  integer $type     1：正序，2：倒序
	 * @return [type]            [description]
	 */
	public static function fileSort($fileName, $outPath, $type = 1){
		$splitFileNames = self::fileSplit($fileName,false,false,'sort');
		// $splitFileNames = scandir(self::getDefaultOutPath());
		// array_shift($splitFileNames);
		// array_shift($splitFileNames);
		$fps = [];
		$line = [];
		$readPt = [];

		$outSize = 1024*1024*2;

		$outFp = fopen($outPath, "w+");

		foreach($splitFileNames as $name){
			// $name = self::getDefaultOutPath().$name;

			$fps[$name] = fopen($name, "r");

			$line[$name] = trim(fgets($fps[$name]),"\n");
		}
		$outLine  = '';
		$i = 0;
		while (!empty($line)) {
			$i++;

			$min_num  =  min($line);//取得数组最小值
			$outLine .= $min_num . "\n";
            $min_key  =  array_search($min_num,$line);//得到数组最小值的key值

            if(feof($fps[$min_key])){
				//关闭文件
				fclose($fps[$min_key]);
				unset($fps[$min_key]);
				unset($line[$min_key]);
				//删除临时文件
				unlink($min_key);
				continue;
			}

            $line[$min_key] = trim(fgets($fps[$min_key]),"\n");

			if(strlen($outLine) > $outSize){
				fwrite($outFp, $outLine);
				$outLine = '';
			}
		}
		fwrite($outFp, $outLine);
		fclose($outFp);
		//删除临时文件夹
		rmdir(self::getDefaultOutPath());
	}

}


FilePlgin::fileSort("qq.txt",'sortqq.txt');
