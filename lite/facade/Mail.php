<?php

declare(strict_types=1);

namespace lite\facade;

use think\Facade;
use lite\library\mail\Mail as MailManager;

/**
 * @see MailManager
 * @method static bool send(string $mail, string $name) 发送邮件通知
 * @method static bool sendCode(string $mail, string $code_event) 发送邮件验证码
 * @method static bool check(string $mail, string $code_event, $code, bool $exception = true) 验证邮件验证码
 * @method static self setFrom(string $mail, string $name) 设置发送人
 * @method static self addAddress(string $address, string $name) 添加接受人
 * @method static self addAddresses(array $addresses) 批量添加接收人
 * @method static self addReplyTo(string $address, string $name) 添加回复人
 * @method static self addReplyTos(array $addresses) 批量添加回复人
 * @method static self addCC(string $address, string $name) 添加抄送人
 * @method static self addCCs(array $addresses) 批量添加抄送人
 * @method static self addBCC(string $address, string $name) 添加密抄人
 * @method static self addBCCs(array $addresses) 批量添加秘密抄送人
 * @method static self addAttachment(string $path, ...$other) 添加附件
 * @method static self addAttachments(array $attachments) 批量添加附件
 * @method static self setSubject(string $subject) 添加邮件主题
 * @method static self setBody(string $body) 添加邮件主体
 * @method static self setAltBody(string $altBody) 添加邮件附加信息
 * @method static self setLanguage(string $language) 设置语言
 * 
 */
class Mail extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'mail';
    }
}
