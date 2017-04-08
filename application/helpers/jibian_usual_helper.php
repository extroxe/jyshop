<?php
/**
 * =========================================================
 *
 *      Filename: Jibian_usual_helper.php
 *
 *   Description: 辅助函数
 *
 *       Created: 2016/7/20 14:30
 *
 *        Author: liuquanalways@163.com
 *
 * =========================================================
 */
 


/**
 * 数组转化为字符串
 * @param $arr array 数组
 * @param $delimiter string 分隔符
 * @return string
 */
function array_to_string($arr = [], $delimiter = ', ')
{
    $str = '';

    if (is_array($arr)) {
        foreach ($arr as $row) {
            if (is_array($row)) {
                $str .= $this->array_to_string($row);
            } else {
                $str .= $row.$delimiter;
            }
        }
    } else {
        $str .= $arr;
    }

    return $str;
}



?>