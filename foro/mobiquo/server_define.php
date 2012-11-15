<?php

defined('IN_MOBIQUO') or exit;

$server_param = array(

    'authorize_user' => array(
        'function'  => 'authorize_user_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcString)),
        'docstring' => 'authorize need two parameters,the first is user name(Base64), second is password.',
    ),
    
    'login' => array(
        'function'  => 'login_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBoolean, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64)),
        'docstring' => 'authorize need two parameters,the first is user name(Base64), second is password.',
    ),

    'get_forum' => array(
        'function'  => 'get_forum_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBoolean, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcBoolean),
                             array($xmlrpcArray)),
        'docstring' => 'no need parameters for get_forum.',
    ),
    
    'get_participated_forum' => array(
        'function'  => 'get_participated_forum_func',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no need parameters for get_forum.',
    ),

    'get_topic' => array(
        'function'  => 'get_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string,int,int,string)',
    ),

    'thank_post' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string)',
    ),
    
    'get_thread' => array(
        'function'  => 'get_thread_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt, $xmlrpcBoolean),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string,int,int)',
    ),

    'get_raw_post' => array(
        'function'  => 'get_raw_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string)',
    ),

    'save_raw_post' => array(
        'function'  => 'save_raw_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(string, base64, base64)',
    ),

    'get_user_topic' => array(
        'function'  => 'get_user_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(string)',
    ),

    'get_user_reply_post' => array(
        'function'  => 'get_user_reply_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(int,int,int,string)',
    ),
    
    'get_forum_status' => array(
        'function'  => 'get_forum_status_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcArray)),
        'docstring' => 'parameter should be array(array)',
    ),

    'get_new_topic' => array(
        'function'  => 'get_new_topic_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcInt, $xmlrpcInt)),
        'docstring' => 'no need parameters for get_forum',
    ),
    
    'get_latest_topic' => array(
        'function'  => 'get_latest_topic_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcInt, $xmlrpcInt)),
        'docstring' => 'no need parameters for get_forum',
    ),

    'get_subscribed_topic' => array(
        'function'  => 'get_subscribed_topic_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcArray, $xmlrpcInt, $xmlrpcInt)),
        'docstring' => 'no need parameters for get_subscribed_topic',
    ),
    
    'get_participated_topic' => array(
        'function'  => 'get_subscribed_topic_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt)),
        'docstring' => 'no need parameters for get_subscribed_topic',
    ),

    'get_bookmarked_topic' => array(
        'function'  => 'get_bookmarked_topic_func',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no need parameters for get_subscribed_topic',
    ),
    
    'get_user_info' => array(
        'function' => 'get_user_info_func',
        'signature' => array(array($xmlrpcStruct),
                             array($xmlrpcStruct, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcString)),
        'docstring' => 'Profile function',
    ),

    'get_config' => array(
        'function'  => 'get_config_func',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no need parameters for get_config',
    ),

    'logout_user' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no need parameters for logout',
    ),

    'create_topic' => array(
        'function'  => 'create_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(int,string,string)',
    ),

    'new_topic' => array(
        'function'  => 'create_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcArray, $xmlrpcString)),
        'docstring' => 'parameter should be array(string,byte,byte,[string],[array])',
    ),

    'reply_topic' => array(
        'function'  => 'reply_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(int,string,string)',
    ),

    'reply_post' => array(
        'function'  => 'reply_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcString, $xmlrpcBoolean)),
        'docstring' => 'parameter should be array(int,string,string)',
    ),

    'subscribe_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'subscribe_topic need one parameters as topic id.',
    ),

    'unsubscribe_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'unsubscribe_topic need one parameters as topic id.',
    ),

    'subscribe_forum' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'subscribe_forum need one parameters as board id.',
    ),

    'unsubscribe_forum' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'unsubscribe_foru need one parameters as board id.',
    ),

    'bookmark_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'subscribe_topic need one parameters as topic id.',
    ),

    'unbookmark_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'unsubscribe_topic need one parameters as topic id.',
    ),

    'get_inbox_stat' => array(
        'function'  => 'get_inbox_stat_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString)),
    ),

    'get_box_info' => array(
        'function'  => 'get_box_info_func',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no parameter but need login first',
    ),

    'get_box' => array(
        'function'  => 'get_box_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt, $xmlrpcDateTime),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string,int,int,date)',
    ),

    'get_message' => array(
        'function'  => 'get_message_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean)),
        'docstring' => 'get_message need parameter as message id and may need box id'
    ),

    'delete_message' => array(
        'function'  => 'delete_message_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString)),
        'docstring' => 'get_message need parameter as message id and may need box id'
    ),

    'create_message' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcInt, $xmlrpcString)),
        'docstring' => 'parameter should be array(array,string,string,[int, string])',
    ),

    'get_board_stat' => array(
        'function'  => 'get_board_stat_func',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'no parameter',
    ),

    'get_online_users' => array(
        'function'  => 'get_online_users_func',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'no parameter',
    ),

    'attach_image' => array(
        'function'  => 'attach_image_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcString)),
        'docstring' => 'parameter should be array()',
    ),

    'mark_all_as_read' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'no parameter',
    ),

    'get_unread_topic' => array(
        'function'  => 'get_unread_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct)),
        'docstring' => 'no parameter',
    ),
    
    'get_subscribed_forum' => array(
        'function'  => 'get_subscribed_forum_func',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'no parameter',
    ),
    
    'get_quote_post' => array(
        'function'  => 'get_quote_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'get_quote_post need one parameter as post id'
    ),
    
    'get_quote_pm' => array(
        'function'  => 'get_quote_pm_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string)',
    ),
    
    'search_topic'=> array(
        'function'  => 'search_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt, $xmlrpcString)),
        'docstring' => 'parameter should be array()',
    ),
    
    'search_post'=> array(
        'function'  => 'search_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt, $xmlrpcString)),
        'docstring' => 'parameter should be array()',
    ),
    
    'search' => array(
        'function' => 'search_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcStruct)),
    ),
    
    'login_user'=> array(
        'function'  => 'login_user_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBoolean)),
        'docstring' => 'parameter should be array()',
    ),
    
    'report_post' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be)',
    ),
    
    'get_thread_by_unread' => array(
        'function'  => 'get_thread_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcBoolean),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be)',
    ),
    
    'get_thread_by_post' => array(
        'function'  => 'get_thread_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcBoolean),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be)',
    ),
    
    'upload_attach' => array(
        'function'  => 'upload_attach_func',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'parameter should be',
    ),
    
    'upload_avatar' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'parameter should be',
    ),
    
    'remove_attachment' => array(
        'function'  => 'remove_attachment_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString)),
        'docstring' => 'parameter should be',
    ),
    
    
    // Moderation functions
    'm_stick_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray, $xmlrpcString),
                             array($xmlrpcArray, $xmlrpcString, $xmlrpcInt)),
    ),
    
    'm_close_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray, $xmlrpcString),
                             array($xmlrpcArray, $xmlrpcString, $xmlrpcInt)),
    ),
    
    'm_delete_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray, $xmlrpcString),
                             array($xmlrpcArray, $xmlrpcString, $xmlrpcInt, $xmlrpcBase64)),
    ),
    
    'm_delete_post' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray, $xmlrpcString),
                             array($xmlrpcArray, $xmlrpcString, $xmlrpcInt, $xmlrpcBase64)),
    ),
    
    'm_move_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString)),
    ),
    
    'm_merge_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString)),
    ),
    
    'm_rename_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcBase64)),
    ),
    
    'm_ban_user' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray, $xmlrpcBase64, $xmlrpcInt, $xmlrpcBase64)),
        'docstring' => '',
    ),
    
    'login_mod' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64)),
    ),
    
    'm_get_report_post' => array(
        'function'  => 'm_get_report_post_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcArray, $xmlrpcInt, $xmlrpcInt)),
        'docstring' => '',
    ),
    
    'm_get_moderate_post' => array(
        'function'  => 'm_get_report_post_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcArray, $xmlrpcInt, $xmlrpcInt)),
        'docstring' => '',
    ),
    
    'm_get_moderate_topic' => array(
        'function'  => 'm_get_report_post_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcArray, $xmlrpcInt, $xmlrpcInt)),
        'docstring' => '',
    ),
    
    //**********************************************
    // Puch related functions
    //**********************************************
    
    'update_push_status' => array(
        'function' => 'update_push_status_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcStruct),
                             array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64)),
    ),
);
