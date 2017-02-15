<?php
/**
 * Created by lyx.
 * User: 446127203@qq.com
 * Date: 2014/10/9 0009
 * Time: 下午 7:37
 *
 * 公用辅助函数类
 */
namespace Tiny;

class Func
{

    /**
     * @param $url
     * @param null $data   $reqType为post时，需要传入数据
     * @param bool $https  true标识是https请求，false标识http请求
     * @param string $reqType  标识get/post请求
     * @return mixed
     */
    public static function httpRequest($url,$data = null, $https = false, $reqType = 'get')
    {
        $ch = curl_init();

        //是否是https请求
        if ($https)
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //请求类型
        if ($reqType == 'post')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/x-www-form-urlencoded;charset=utf-8'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json;charset=utf-8'));
        $rtn = curl_exec($ch);
        if($errNo = curl_errno($ch))
            self::echoJson(0, 'curl error:' . $errNo);
        curl_close($ch);
        return $rtn;
    }

    /**
     * @param $url  url链接
     * @param null $data   post时，需要传入的参数
     * @param bool|false $https  http or https   默认是http请求
     * @param string $reqType get or post
     * @param int $dataType  1 指普通数据  http_build_query($data), 2指json json_encode($data)
     * @return mixed
     */
    public static function httpRequest2($url,$data = null, $https = false, $reqType = 'get', $dataType = 1)
    {
        $ch = curl_init();

        //是否是https请求
        if ($https)
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //请求类型
        if ($reqType == 'post')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        if ($dataType == 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/x-www-form-urlencoded;charset=utf-8'));
        } elseif($dataType == 2)
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json;charset=utf-8'));
        }
        $rtn = curl_exec($ch);
        if($errNo = curl_errno($ch))
            self::echoJson(0, 'curl error:' . $errNo);
        curl_close($ch);
        return $rtn;
    }

    public static function echoJson($ret, $msg = null, $exit = true)
    {
        $array['ret'] = (int)$ret;
        $msg && $array['msg'] = $msg;
        echo json_encode($array);
        $exit && exit;
    }

    /**
     * @param $arr array 数据数组或带\n\r字符串
     * @param $fileName string 生成的文件名
     */
    public static function any2excel($mixed, $fileName = null)
    {
        is_null($fileName) && $fileName = 'export_excel_' . date('Y-m-d H_i_s');

        header("content-type:text/html; charset=utf-8");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename={$fileName}.xls");
        if(is_array($mixed)){
            $str = '';
            foreach($mixed as $v1){
                $str .= '<tr>';
                foreach($v1 as $v2){
                    if((string)$v2 == (string)floatval($v2)){ // 数字
                        if($v2 > 4294967296)
                            $str .= '<td x:str class=xl2216681 nowrap>'.$v2.'</td>';
                        else
                            $str .= '<td x:num class=xl2216681 nowrap>'.$v2.'</td>';
                    }else
                        $str .= '<td x:str class=xl2216681 nowrap>'.$v2.'</td>';
                }
                $str .= '</tr>';
            }
            $opt='
		    <html xmlns:o="urn:schemas-microsoft-com:office:office"
		    xmlns:x="urn:schemas-microsoft-com:office:excel"
		    xmlns="http://www.w3.org/TR/REC-html40">
		    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		    <html>
		    <head>
		    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
		    <style id="Classeur1_16681_Styles"></style>
		    </head>
		    <body>
		    <div id="Classeur1_16681" align=center x:publishsource="Excel">
		    <table border=1 cellpadding=0 cellspacing=0 style="border-collapse: collapse">
		    '.$str.'
		    </table>
		    </div>
		    </body>
		    </html>';
        }else
            $opt = $mixed;

        die($opt);
    }
}