class A
{   
    /**
     * 根据channel 中信息算出不同的组合类型
     * @param array $arr [1,2,3,4]
     * @return array [
     *                  [0] => 0_0_0_4
                        [1] => 0_0_3_0
                        [2] => 0_0_3_4
                        [3] => 0_2_0_0
                        [4] => 0_2_0_4
                        [5] => 0_2_3_0
                        [6] => 0_2_3_4
                        [7] => 1_0_0_0
                        [8] => 1_0_0_4
                        [9] => 1_0_3_0
                        [10] => 1_0_3_4
                        [11] => 1_2_0_0
                        [12] => 1_2_0_4
                        [13] => 1_2_3_0
                        [14] => 1_2_3_4
     *              ]
     */
    public function combination($arr,&$res = array(), $len = 0, $r = array(0, 0, 0, 0), $flat = true)
    {
        $arr_len = count($arr);
        for ($i = $len; $i < $arr_len; $i++) {
            $this->combination($arr,$res, $i + 1, $r, false);
            $r[$i] = $arr[$i];
            $res[] = implode("_", $r);
        }

        return $res;
    }


    public function action()
    {
        $list = [1, 2, 3, 4];
        $info = $this->combination($list);

    }
}
