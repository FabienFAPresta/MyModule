<?php

declare(strict_types=1);

namespace PrestaShop\Module\MyModule;

use ObjectModel;

class CommentMyModule extends ObjectModel
{
    public $id;
    public $user_id;
    public $comment;

    /** @var array   Fields definition */
    public static $definition = [
        'table' => 'testcomment',
        'primary' => 'id',
        'multilang' => false,
        'multilang_shop' => false,
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
