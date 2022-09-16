<?php

namespace PrestaShop\Module\MyModule;

use ObjectModel;

class CommentMyModule extends ObjectModel
{
    public $id;
    public $user_id;
    public $comment;

    /** @var array   Fields definition */
    public static $definition = [
        'table' => _DB_PREFIX_ . 'testcomment',
        'primary' => 'id',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'user_id' => [
                'type' => self::TYPE_INT,
                'size' => 11,
                'validate' => 'isunsignedInt',
                'required' => true
            ],
            'comment' => [
                'type' => self::TYPE_STRING,
                'size' => 255,
                'validate' => 'isCleanHtml',
                'required' => true
            ]
        ]
    ];
}
