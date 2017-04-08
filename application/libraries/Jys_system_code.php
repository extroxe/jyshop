<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_system_code.php
 *
 *     Description: 系统字典类
 *
 *         Created: 2016-11-22 21:21:01
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Jys_system_code {

    /**
     * 商品状态
     */
    const COMMODITY_STATUS = "commodity_status";
    /**
     * 商品状态：删除
     */
    const COMMODITY_STATUS_DELETE = 0;
    /**
     * 商品状态：上架
     */
    const COMMODITY_STATUS_PUTAWAY = 1;
    /**
     * 商品状态：下架
     */
    const COMMODITY_STATUS_SOLDOUT = 2;

    /**
     * 优惠券状态
     */
    const DISCOUNT_COUPON_STATUS = "discount_coupon_status";
    /**
     * 优惠券状态：发布
     */
    const DISCOUNT_COUPON_STATUS_PULISHED = 1;
    /**
     * 优惠券状态：未发布
     */
    const DISCOUNT_COUPON_STATUS_UNPULISHED = 2;

    /**
     * 用户的惠券状态
     */
    const USER_DISCOUNT_COUPON_STATUS = "user_discount_coupon_status";
    /**
     * 用户的惠券状态：未使用
     */
    const USER_DISCOUNT_COUPON_STATUS_UNUSED = 1;
    /**
     * 用户的惠券状态：已使用
     */
    const USER_DISCOUNT_COUPON_STATUS_USED = 2;
    /**
     * 用户的惠券状态：已过期
     */
    const USER_DISCOUNT_COUPON_STATUS_EXPIRED = 3;

    /**
     * Banner的位置
     */
    const BANNER_POSITION = "banner_position";
    /**
     * Banner的位置：PC首页
     */
    const BANNER_POSITION_PC_HOME = 1;
    /**
     * Banner的位置：weixin首页
     */
    const BANNER_POSITION_WEIXIN_HOME = 2;
    /**
     * Banner的位置：weixin积分商城首页
     */
    const BANNER_POSITION_WEIXIN_INTEGRAL_HOME = 3;
    /**
     * Banner的位置：weixin积分商城兑换页
     */
    const BANNER_POSITION_WEIXIN_INTEGRAL_EXCHANGE = 4;
    /**
     * Banner的位置：weixin积分抽奖页
     */
    const BANNER_POSITION_WEIXIN_INTEGRAL_SWEEPSTAKES = 5;

    /**
     * 支付方式
     */
    const PAYMENT = "payment";
    /**
     * 支付方式：微信支付
     */
    const PAYMENT_WXPAY = 1;
    /**
     * 支付方式：支付宝
     */
    const PAYMENT_ALIPAY = 2;
    /**
     * 支付方式：中国银联
     */
    const PAYMENT_UNIONPAY = 3;
    /**
     * 支付方式：积分
     */
    const PAYMENT_POINTPAY = 4;
    /**
     * 支付方式：积分抽奖
     */
    const PAYMENT_INTEGRAL_SWEEPSTAKES = 5;
    /**
     * 支付方式：积分夺宝
     */
    const PAYMENT_INTEGRAL_INDIANA = 6;

    /**
     * 终端类型
     */
    const TERMINAL_TYPE = "terminal_type";
    /**
     * 终端类型：PC端
     */
    const TERMINAL_TYPE_PC = 1;
    /**
     * 终端类型：微信
     */
    const TERMINAL_TYPE_WEIXIN = 2;

    /**
     * 商品类型
     */
    const COMMODITY_TYPE = "commodity_type";
    /**
     * 商品类型：基因商品
     */
    const COMMODITY_TYPE_GENE = 1;
    /**
     * 商品类型：实物商品
     */
    const COMMODITY_TYPE_ENTITY = 2;
    /**
     * 商品类型：会员商品
     */
    const COMMODITY_TYPE_MEMBER = 3;

    /**
     * 订单状态
     */
    const ORDER_STATUS = "order_status";
    /**
     * 订单状态：未付款
     */
    const ORDER_STATUS_NOT_PAID = 10;
    /**
     * 订单状态：已付款
     */
    const ORDER_STATUS_PAID = 20;
    /**
     * 订单状态：已发货
     */
    const ORDER_STATUS_DELIVERED = 30;
    /**
     * 订单状态：已寄回
     */
    const ORDER_STATUS_SENT_BACK = 40;
    /**
     * 订单状态：正在检测
     */
    const ORDER_STATUS_ASSAYING = 50;
    /**
     * 订单状态：已完成
     */
    const ORDER_STATUS_FINISHED = 60;
    /**
     * 订单状态：退款中
     */
    const ORDER_STATUS_REFUNDING = 70;
    /**
     * 订单状态：已退款
     */
    const ORDER_STATUS_REFUNDED = 80;
    /**
     * 订单状态：未退款
     */
    const ORDER_STATUS_UNREFUNDED = 90;
    /**
     * 订单状态：已取消
     */
    const ORDER_STATUS_CANCELED = 100;

    /**
     * 退款状态
     */
    const REFUND_STATUS = "refund_status";
    /**
     * 退款状态：退款中
     */
    const REFUND_STATUS_APPLYING = 10;
    /**
     * 退款状态：同意退款
     */
    const REFUND_STATUS_AGREED = 20;
    /**
     * 退款状态：拒绝退款
     */
    const REFUND_STATUS_REJECTED = 30;

    /**
     * 角色：普通用户
     */
    const ROLE = "role";
    /**
     * 角色：普通用户
     */
    const ROLE_USER = 10;
    /**
     * 角色：管理员
     */
    const ROLE_ADMINISTRATOR = 20;
    /**
     * 角色：代理商
     */
    const ROLE_AGENT = 30;

    /**
     * 性别
     */
    const GENDER = 'gender';
    /**
     * 性别：女
     */
    const GENDER_FEMALE = 0;
    /**
     * 性别：男
     */
    const GENDER_MALE = 1;

    /**
     * 验证码用途
     */
    const VERIFICATION_CODE_PURPOSE = 'verification_code_purpose';
    /**
     * 验证码用途：注册
     */
    const VERIFICATION_CODE_PURPOSE_REGISTER = 1;
    /**
     * 验证码用途：手机
     */
    const VERIFICATION_CODE_PURPOSE_PHONE = 2;
    /**
     * 验证码用途：邮件
     */
    const VERIFICATION_CODE_PURPOSE_EMAIL = 3;
    /**
     * 验证码用途：查询报告
     */
    const VERIFICATION_CODE_PURPOSE_SEARCH_REPORT = 4;
    /**
     * 验证码用途：找回密码
     */
    const VERIFICATION_CODE_PURPOSE_FIND_PASSWORD = 5;
    /**
     * 文章状态
     */
    const ARTICLE_STATUS = 'article_status';
    /**
     * 文章状态：发表
     */
    const ARTICLE_STATUS_PUBLISHED = 1;
    /**
     * 文章状态：未发表
     */
    const ARTICLE_STATUS_UNPUBLISHED = 2;

    /**
     * 商品推荐类型
     */
    const RECOMMEND_COMMODITY_STATUS = "recommend_commodity_status";
    /**
     * 商品推荐类型：热卖商品
     */
    const RECOMMEND_COMMODITY_STATUS_HOT_SALE = 1;
    /**
     * 商品推荐类型：热换商品
     */
    const RECOMMEND_COMMODITY_STATUS_HOT_EXCHANGE = 2;
    /**
     * 临床病史
     */
    const CHINICAL_HISTORY = "clinical_history";
    /**
     * 临床病史：手术
     */
    const CHINICAL_HISTORY_OPERATION = 10;
    /**
     * 临床病史：放疗
     */
    const CHINICAL_HISTORY_RADIONTHERAPY = 20;
    /**
     * 临床病史：化疗
     */
    const CHINICAL_HISTORY_CHEMOTHERAPY = 30;
    /**
     * 临床病史：靶向药物治疗
     */
    const CHINICAL_HISTORY_TARGETED_THERAPIES = 40;
    /**
     * 亲属关系
     */
    const RELATION = "relation";
    /**
     * 家属关系：父亲
     */
    const RELATION_FATHER = 10;
    /**
     * 家属关系：母亲
     */
    const RELATION_MOTHER = 20;
    /**
     * 健康状态
     */
    const HEALTH_STATUS = "health_status";
    /**
     * 健康状态：健康
     */
    const HEALTH_STATUS_HEALTH = 10;
    /**
     * 健康状态：亚健康
     */
    const HEALTH_STATUS_SUB_HEALTH = 20;
    /**
     * 健康状态：疾病
     */
    const HEALTH_STATUS_ILLNESS = 30;
    /**
     * 帖子状态
     */
    const POST_STATUS = 'post_status';
    /**
     * 草稿
     */
    const POST_STATUS_DRAFT = 1;
    /**
     * 已发表
     */
    const POST_STATUS_PUBLISHED = 2;
    /**
     * 已删除
     */
    const POST_STATUS_DELETED = 3;
    /**
     * 评论状态
     */
    const COMMENT_STATUS = 'comment_status';
    /**
     * 已发表
     */
    const COMMENT_STATUS_PUBLISHED = 1;
    /**
     * 已删除（管理员）
     */
    const COMMENT_STATUS_MANAGER_DELETED = 2;
    /**
     * 已删除（楼主）
     */
    const COMMENT_STATUS_LANDLORD_DELETED = 3;
    /**
     * 已删除（本人）
     */
    const COMMENT_STATUS_OWNER_DELETED = 4;
    /**
     * 站内信信息状态
     */
    const MESSAGE_STATUS = 'message_status';
    /**
     * 草稿
     */
    const MESSAGE_STATUS_UNREAD = 0;
    /**
     * 已发表
     */
    const MESSAGE_STATUS_READ = 1;
    /**
     * 积分夺宝活动状态
     */
    const INTEGRAL_INDIANA_STATUS = 'integral_indiana_status';
    /**
     * 进行中
     */
    const INTEGRAL_INDIANA_STATUS_DOING = 1;
    /**
     * 已结束
     */
    const INTEGRAL_INDIANA_STATUS_DONE = 2;
    /**
     * 已删除
     */
    const INTEGRAL_INDIANA_STATUS_DELETED = 3;
    /**
     * 积分夺宝结果状态
     */
    const INTEGRAL_INDIANA_RESULT_STATUS = 'integral_indiana_result_status';
    /**
     * 未操作
     */
    const INTEGRAL_INDIANA_RESULT_STATUS_SYSTEM_EXTRACTION = 0;
    /**
     * 审核通过
     */
    const INTEGRAL_INDIANA_RESULT_STATUS_PASS = 1;
    /**
     * 已领取
     */
    const INTEGRAL_INDIANA_RESULT_STATUS_RECEIVED = 2;
    /**
     * 积分抽奖结果状态
     */
    const SWEEPSTAKES_RESULT_STATUS = 'sweepstakes_result_status';
    /**
     * 未领取
     */
    const SWEEPSTAKES_RESULT_STATUS_NOT_RECEIVE = 0;
    /**
     * 已领取
     */
    const SWEEPSTAKES_RESULT_STATUS_RECEIVED = 1;

}