<?php


namespace app\traits;
use mailer\tp6\Mailer;
/**
 * EmailTrait
 * created on 2021/12/21 15:02
 * created by chengzhigang
 */
trait EmailTrait
{

    /**
     * @desc 发送邮件
     * @method send
     * @param $receiver 收件人
     * @param $title 标题
     * @param $content 文本内容
     * @throws \mailer\lib\Exception
     * @author chengzhigang
     * @time 2021/12/21 16:36
     */
    public function send($receiver,$title,$content){
        $mailer = Mailer::instance();
        $mailer->to($receiver)
            ->subject($title)
            ->text($content)
            ->send();
    }
}
