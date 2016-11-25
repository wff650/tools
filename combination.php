<?php
/**
 *
 * 根据 给出的数据 进行排列 获取所有组合 C(n,m)=A(n,m)/m!=n!/((n-m)!*m!)
 *
 * @param $arr                      基础数据数组 n
 * @param int $len                  排列个数    m
 * @param string $str
 * @return array                    返回 包含所有排列的 数组
 */
这个是排列的
还有个组合的
/**
 *
 * 根据 给出的数据 进行组合 获取所有可能 A(n,m)=n(n-1)(n-2)……(n-m+1)= n!/(n-m)!
 *
 * @param $arr                      基础数据数组 n
 * @param int $len                  排列个数    m
 * @param string $str
 * @return array                    返回 包含所有排列的 数组
 */
function arrangement($arr, $len=0, $str="") {
    global $res_gc;
    $arr_len = count($arr);
    if($len == 0){
        $res_gc[] = $str;
    }else{
        for($i=0; $i<$arr_len; $i++){
            $tmp = array_shift($arr);
            arrangement($arr, $len-1, $str == '' ? $tmp : $str.",".$tmp);
            //注视下面这行可以生成不重复的排列
            array_push($arr, $tmp);
        }
    }
    return $res_gc;
}


