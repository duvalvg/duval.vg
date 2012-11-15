<?php

defined('IN_MOBIQUO') or exit;

require('config/config.php');
$mobiquo_config = get_mobiquo_config();

if(isset($mobiquo_config['mod_function']) && !empty($mobiquo_config['mod_function'])) {
    foreach($mobiquo_config['mod_function'] as $mod_function) {
        if (!function_exists($mod_function)) {
            eval("function $mod_function(){}");
        }
    }
}

mobi_parse_requrest();
if (!$request_name && isset($_POST['method_name'])) $request_name = $_POST['method_name'];
switch ($request_name) {
    case 'attach_image':
        if ($params_num >= 3) {
            $image = $request_params[0];

            $_FILES['attachment'] = array(
                'name'      => array($request_params[1]),
                'type'      => array($request_params[2] == 'JPG' ? 'image/jpeg' : 'image/png'),
                'error'     => array(0),
            );
            $_GET['board'] = intval($request_params[3]);
            $_GET['action'] = 'attach_image';
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'login':
    case 'authorize_user':
        if ($params_num == 2 || $params_num == 4) {
            $_POST['user'] = $request_params[0];
            $_POST['password'] = $request_params[1];
            $_POST['cookielength'] = -1;
            $_GET['action'] = 'login2';
            if($params_num == 4)
            {
                $_POST['push'] = $request_params[3];
            }
        }else {
            get_error('Parameter Error');
        }
        break;
    case 'get_bookmarked_topic':
        $start_num = intval(isset($request_params[0]) ? $request_params[0] : '0');
        $end_num = intval(isset($request_params[1]) ? $request_params[1] : '19');
        if ($start_num > $end_num) {
            get_error('Parameter Error');
        } elseif ($end_num - $start_num >= 50) {
            $end_num = $start_num + 49;
        }
        $limit_num = $end_num - $start_num + 1;
        break;
    case 'create_message':
        if ($params_num == 3 || $params_num == 5) {
            $_GET['action'] = 'pm';
            $_GET['sa'] = 'send2';

            if ($params_num == 5 && $request_params[3] == 1) {
                $_POST['replied_to'] = intval($request_params[4]);
                $_POST['pm_head'] = intval($request_params[4]);
            }
            $_POST['f'] = 'inbox';
            $_POST['outbox'] = 1;
            $_POST['recipient_to'] = $request_params[0];
            $_POST['subject'] = $request_params[1];
            $_POST['message'] = $request_params[2];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'create_topic':
        if ($params_num >= 4) {
            $_GET['action'] = 'post2';
            $_GET['start'] = 0;
            $_GET['board'] = intval($request_params[0]);

            $_POST['icon'] = 'xx';
            $_POST['subject'] = $request_params[1];
            $_POST['message'] = $request_params[3];
            $_POST['attachments'] = isset($request_params[4]) ? explode('.', $request_params[4]) : array();
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'new_topic':
        if ($params_num >= 3) {
            $_GET['action'] = 'post2';
            $_GET['start'] = 0;
            $_GET['board'] = intval($request_params[0]);

            $_POST['icon'] = 'xx';
            $_POST['subject'] = $request_params[1];
            $_POST['message'] = $request_params[2];
            $_POST['attachments'] = isset($request_params[4]) ? explode('.', implode('.', $request_params[4])) : array();
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'delete_message':
        if ($params_num == 1 || $params_num == 2) {
            $_GET['action'] = 'pm';
            $_GET['sa'] = 'pmactions';
            $_GET['start'] = 0;
            $_GET['pm_actions'] = array(intval($request_params[0]) => 'delete');
            $_GET['f'] = isset($request_params[1]) ? $request_params[1] : 'inbox';
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_board_stat': break;
    case 'get_box':
        if ($params_num >= 1) {
            $_GET['action'] = 'pm';
            $_GET['f'] = $request_params[0] == 'sent' ? 'sent' : 'inbox';
            $start_num = intval(isset($request_params[1]) ? $request_params[1] : '0');
            $end_num = intval(isset($request_params[2]) ? $request_params[2] : '19');
            if ($start_num > $end_num) {
                get_error('Parameter Error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
            $_GET['start'] = $start_num;
            $_GET['sort'] = 'date';
            $_GET['desc'] = '';
            $limit_num = $end_num - $start_num + 1;
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_box_info': break;
    case 'get_config': break;
    case 'get_forum': 
        if($params_num < 3)
        {
            $_GET['return_description'] = isset($request_params[0]) ? $request_params[0] : true;
            if (isset($request_params[1]))
            {
                $_GET['forum_id'] = $request_params[1];
                if ($_GET['forum_id'] == '-1') $_GET['forum_id'] = '0';
            }
        }    
        else
        {
            get_error('Parameter Error');
        }
        break;
    case 'get_forum_status':
        if ($params_num == 1 && is_array($request_params[0]))
            $_GET['forum_ids'] = array_map('intval', $request_params[0]);
         else
            get_error('Parameter Error');

        break;
    case 'get_inbox_stat':
        $pm_last_checked_time = isset($request_params[0]) ? $request_params[0] : 0;
        $subscribed_topic_last_checked_time = isset($request_params[1]) ? $request_params[1] : 0;
        break;
    case 'get_message':
        if ($params_num == 1 || $params_num == 2 || $params_num == 3) {
            $msg_id = intval($request_params[0]);
            $box_id = (isset($request_params[1]) && $request_params[1] == 'sent') ? 'sent' : 'inbox';
            $return_html = isset($request_params[2]) ? $request_params[2] : false;
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_latest_topic':
    case 'get_new_topic':
        if ($params_num == 2 or $params_num == 0) {
            $start_num = intval(isset($request_params[0]) ? $request_params[0] : '0');
            $end_num = intval(isset($request_params[1]) ? $request_params[1] : '19');
            if ($start_num > $end_num) {
                get_error('Parameter Error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
            $topic_per_page = $end_num - $start_num + 1;
            $_GET['start'] = $start_num;
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_online_users':
        $_GET['action'] = 'who';
        $_POST['show'] = 'members';
        break;
    case 'get_raw_post':
        if ($params_num == 1) {
            $_GET['action'] = 'post';
            //$_GET['topic'] = 18;
            $_GET['msg'] = intval($request_params[0]);
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_subscribed_topic':
        if (isset($request_params[1]))
            list($start, $limit) = process_page($request_params[0], $request_params[1]);
        else
            list($start, $limit) = array(0, 20);
        break;
    case 'get_subscribed_forum': break;
    case 'get_thread':
        if ($params_num >= 1) {
            $start_num = intval(isset($request_params[1]) ? $request_params[1] : '0');
            $end_num = intval(isset($request_params[2]) ? $request_params[2] : '19');
            if ($start_num > $end_num) {
                get_error('Parameter Error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
            $_GET['topic'] = intval($request_params[0]).'.'.$start_num;
            $return_html = isset($request_params[3]) ? $request_params[3] : false;
            $post_per_page = $end_num - $start_num + 1;
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_thread_by_unread':
        if ($params_num >= 1 && $params_num <= 3) {
            $_GET['topic'] = intval($request_params[0]);
            $post_per_page = isset($request_params[1]) ? intval($request_params[1]) : 20;
            $return_html = isset($request_params[2]) ? $request_params[2] : false;
            $_GET['start'] = 'new';
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_thread_by_post':
        if ($params_num >= 1 && $params_num <= 3) {
            $_GET['msg'] = intval($request_params[0]);
            $post_per_page = isset($request_params[1]) ? intval($request_params[1]) : 20;
            $return_html = isset($request_params[2]) ? $request_params[2] : false;
            $_GET['start'] = 'msg'.$_GET['msg'];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_topic':
        if ($params_num >= 1) {
            $start_num = intval(isset($request_params[1]) ? $request_params[1] : '0');
            $end_num = intval(isset($request_params[2]) ? $request_params[2] : '19');
            if ($start_num > $end_num) {
                get_error('Parameter Error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
            $_GET['board'] = intval($request_params[0]).'.'.$start_num;
            $topic_per_page = $end_num - $start_num + 1;
            $mode = isset($request_params[3]) ? $request_params[3] : '';
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_user_info':
        if ($params_num <= 2) {
            $_GET['action'] = 'profile';
            if (isset($request_params[1]) && !empty($request_params[1]))
                $_GET['u'] = $request_params[1];
            elseif (isset($request_params[0]))
                $_GET['user'] = $request_params[0];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_user_reply_post':
        if ($params_num == 1) {
            $_GET['action'] = 'profile';
            $_GET['area'] = 'showposts';
            $_GET['user'] = $request_params[0];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_user_topic':
        if ($params_num == 1) {
            $_GET['action'] = 'profile';
            $_GET['area'] = 'showposts';
            $_GET['sa'] = 'topics';
            $_GET['user'] = $request_params[0];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'logout_user':
        $_GET['action'] = 'logout';
        break;
    case 'reply_topic':
        if ($params_num >= 4) {
            $_GET['action'] = 'post2';
            $_GET['start'] = 0;

            $_POST['icon'] = 'xx';
            $_POST['topic'] = intval($request_params[0]);
            $_POST['subject'] = $request_params[3];
            $_POST['message'] = $request_params[2];
            $_POST['attachments'] = isset($request_params[4]) ? explode('.', $request_params[4]) : array();
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'reply_post':
        if ($params_num >= 4) {
            $_GET['action'] = 'post2';
            $_GET['start'] = 0;

            $_POST['icon'] = 'xx';
            $_POST['topic'] = intval($request_params[1]);
            $_POST['subject'] = $request_params[2];
            $_POST['message'] = $request_params[3];
            $_POST['attachments'] = isset($request_params[4]) ? explode('.', implode('.', $request_params[4])) : array();
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'save_raw_post':
        if ($params_num == 3) {
            $_GET['action'] = 'post2';
            $_GET['start'] = 0;
            $_GET['msg'] = $request_params[0];

            $_POST['icon'] = 'xx';
            $_POST['subject'] = $request_params[1];
            $_POST['message'] = $request_params[2];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'subscribe_topic':
        if ($params_num == 1) {
            $_GET['action'] = 'notify';
            $_GET['sa'] = 'on';
            $_GET['topic'] = $request_params[0];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'thank_post':
        if ($params_num == 1) {
            $_GET['msg'] =  $request_params[0];
            $_GET['action'] = 'thankyou';
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'unsubscribe_topic':
        if ($params_num == 1) {
            $_GET['action'] = 'notify';
            $_GET['sa'] = 'off';
            $_GET['topic'] = $request_params[0];
            $_GET['isUnsubscribeAll'] = $request_params[0] == 'ALL'?  1 : 0 ;
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'bookmark_topic':
        if ($params_num == 1) {
            $_GET['tid'] = ($request_params[0]);
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'unbookmark_topic':
        if ($params_num == 1) {
            $_GET['tid'] = intval($request_params[0]);
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'mark_all_as_read':
        if ($params_num == 0) {
            $_GET['action'] = 'markasread';
            $_GET['sa'] = 'all';
        } elseif($params_num == 1) {
            $_GET['action'] = 'markasread';
            $_GET['sa'] = 'board';
            $_GET['board'] = $request_params[0];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'subscribe_forum':
        if ($params_num == 1) {
            $_GET['action'] = 'notifyboard';
            $_GET['sa'] = 'on';
            $_GET['board'] = $request_params[0];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'unsubscribe_forum':
        if ($params_num == 1) {
            $_GET['action'] = 'notifyboard';
            $_GET['sa'] = 'off';
            $_GET['board'] = $request_params[0];
            $_GET['isUnsubscribeAll'] = $request_params[0] == 'ALL'?  1 : 0 ;
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_unread_topic':
        if ($params_num == 2 or $params_num == 0) {
            $start_num = intval(isset($request_params[0]) ? $request_params[0] : '0');
            $end_num = intval(isset($request_params[1]) ? $request_params[1] : '19');
            if ($start_num > $end_num) {
                get_error('Parameter Error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
            $topic_per_page = $end_num - $start_num + 1;
            $_GET['action'] = 'unread';
            $_GET['start'] = $start_num;
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_participated_topic':
        $start_num = 0;
        $topic_per_page = 20;
        if ($params_num == 1)
            $search_user = $request_params[0];
        elseif ($params_num == 2) {
            $start_num = intval(isset($request_params[0]) ? $request_params[0] : '0');
            $end_num = intval(isset($request_params[1]) ? $request_params[1] : '19');
            if ($start_num > $end_num) {
                get_error('Parameter Error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
            $topic_per_page = $end_num - $start_num + 1;
        } elseif ($params_num == 3) {
            $search_user = $request_params[0];
            $start_num = intval(isset($request_params[1]) ? $request_params[1] : '0');
            $end_num = intval(isset($request_params[2]) ? $request_params[2] : '19');
            if ($start_num > $end_num) {
                get_error('Parameter Error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
            $topic_per_page = $end_num - $start_num + 1;
        }
        break;
    case 'get_quote_post':
        if ($params_num == 1) {
            $_GET['action'] = 'post';
            $_GET['quote'] = $request_params[0];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'get_quote_pm':
        if ($params_num == 1) {
            $_GET['action'] = 'pm';
            $_GET['sa'] = 'send';
            $_GET['quote'] = '';
            $_GET['pmsg'] = $request_params[0];
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'search':
        if ($params_num == 1)
        {
            $search_filter = $request_params[0];
            $topic_per_page = isset($search_filter['perpage']) ? $search_filter['perpage'] : 20;

            $_GET['pagenumber'] = isset($search_filter['page']) ? $search_filter['page'] : 1;
            $_GET['start'] =  ($_GET['pagenumber'] - 1)*$topic_per_page;
            $_GET['action'] = 'search2';

            if (isset($search_filter['searchid']) && !empty($search_filter['searchid']))
            {
                $_GET['params'] = $search_filter['searchid'];
            }
            else
            {
                $_POST['sort'] = 'relevance|desc';
                $_POST['submit'] = 'Search';
                $_POST['advanced'] = 1;
                $_POST['searchtype'] = 1;

                isset($search_filter['keywords']) && $_POST['search'] = $search_filter['keywords'];
                if(isset($search_filter['threadid']) && !empty($search_filter['threadid']))
                {
                    $_POST['topic'] = $search_filter['threadid'];
                    $_POST['show_complete'] = 1;
                    $_POST['showposts'] = 1;
                }
                else
                {
                    isset($search_filter['searchuser']) && $_POST['userspec'] = $search_filter['searchuser'];
                    isset($search_filter['threadid']) && $_POST['searchthreadid'] = $search_filter['threadid'];
                    isset($search_filter['titleonly']) && $_POST['subject_only'] = $search_filter['titleonly'];

                    if(isset($search_filter['forumid']) && !empty($search_filter['forumid']))
                    {
                        $_POST['brd'][] = $search_filter['forumid'];
                    }
                    //Specify search date
                    if(isset($search_filter['searchtime'])&& is_numeric($search_filter['searchtime']))
                    {
                        $_POST['maxage'] = $search_filter['searchtime']/86400;
                        $_POST['minage'] = 0;
                    }

                    //Specify show as post or topic
                    if(isset($search_filter['showposts']))
                    {
                        $_POST['show_complete'] = $search_filter['showposts'];
                        //$_POST['showposts'] = $search_filter['showposts'];
                    }
                }
            }
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'search_topic':
        if ($params_num >= 1) {
            $start_num = intval(isset($request_params[1]) ? $request_params[1] : '0');
            $end_num = intval(isset($request_params[2]) ? $request_params[2] : '19');
            if ($start_num > $end_num) {
                get_error('Parameter Error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
            $topic_per_page = $end_num - $start_num + 1;

            $_GET['action'] = 'search2';

            $_POST['subject_only'] = 1;
            $_POST['search'] = $request_params[0];
            $_POST['submit'] = 'Search';
            $_POST['advanced'] = 0;
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'search_post':
        if ($params_num >= 1) {
            $start_num = intval(isset($request_params[1]) ? $request_params[1] : '0');
            $end_num = intval(isset($request_params[2]) ? $request_params[2] : '19');
            if ($start_num > $end_num) {
                get_error('Parameter Error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
            $topic_per_page = $end_num - $start_num + 1;

            $_GET['action'] = 'search2';

            $_POST['search'] = $request_params[0];
            $_POST['submit'] = 'Search';
            $_POST['advanced'] = 0;

        } else {
            get_error('Parameter Error');
        }
        break;
    case 'login_user':
        if ($params_num == 3) {
            $_POST['user'] = $request_params[0];
            $_POST['password'] = $request_params[1];
            $_POST['hash_passwrd'] = sha1($request_params[0].$request_params[1]);
            $_POST['cookielength'] = -1 ;
            $_GET['action'] = 'login2';
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'report_post':
        if ($params_num == 1 || $params_num == 2) {
            $_GET['action'] = 'reporttm';
            $_GET['msg'] = $request_params[0];
            $_POST['msg'] = $request_params[0];
            $_POST['comment'] = isset($request_params[1]) ? $request_params[1] : '';
            $_POST['submit'] = 'Submit';
        } else {
            get_error('Parameter Error');
        }
        break;
    case 'upload_avatar':
        $_GET['action'] = 'profile';
        $_GET['area'] = 'forumprofile';
        $_GET['save'] = '';
        $_POST['avatar_choice'] = 'upload';
        $_POST['sa'] = 'forumprofile';
        $_FILES['attachment'] = $_FILES['upload'];
        $server_data = '<?xml version="1.0"?><methodCall><methodName>upload_avatar</methodName><params></params></methodCall>';
        break;
    case 'upload_attach':
        $_GET['action'] = 'post2';
        $_GET['board'] = $_POST['forum_id'];
        $group_id = $_POST['group_id'] ? unserialize(urldecode($_POST['group_id'])) : array();
        $_POST['subject'] = 'tmp subject';
        $_POST['message'] = 'tmp message';
        $server_data = '<?xml version="1.0"?><methodCall><methodName>upload_attach</methodName><params></params></methodCall>';
        break;
    case 'remove_attachment':
        $_GET['action'] = 'post2';
        $attachment_id = explode('.', $request_params[0]);
        $group_id = $request_params[2] ? unserialize(urldecode($request_params[2])) : array();
        if (isset($group_id[$attachment_id[0]])) unset($group_id[$attachment_id[0]]);
        $_POST['attach_del'] = array_keys($group_id);
        array_unshift($_POST['attach_del'], 0);
        $_GET['board'] = $request_params[1];
        $_GET['msg'] = isset($request_params[3]) ? $request_params[3] : 0;
        $_POST['subject'] = 'tmp subject';
        $_POST['message'] = 'tmp message';
        break;

    // moderation functions
    case 'm_stick_topic':
        $_GET['action'] = 'sticky';
        $_GET['topic'] = $request_params[0];
        break;
    case 'm_close_topic':
        $_GET['action'] = 'lock';
        $_GET['topic'] = $request_params[0];
        break;
    case 'm_delete_topic':
        $_GET['action'] = 'removetopic2';
        $_GET['topic'] = $request_params[0];
        break;
    case 'm_delete_post':
        $_GET['action'] = 'deletemsg';
        $_GET['msg'] = $request_params[0];
        $_GET['recent'] = 1;
        break;
    case 'm_move_topic':
        $_GET['action'] = 'movetopic2';
        $_GET['topic'] = $request_params[0];
        $_POST['toboard'] = $request_params[1];
        break;
    case 'm_merge_topic':
        $_GET['action'] = 'mergetopics';
        $_GET['sa'] = 'execute';
        $_POST['subject'] = $request_params[1];
        $_POST['topics'] = $request_params;
        $_POST['notifications'] = $request_params;
        break;
    case 'm_rename_topic':
        $_GET['action'] = 'rename_topic';
        $_GET['topic'] = $request_params[0];
        $_POST['custom_subject'] = $request_params[1];
        $_POST['enforce_subject'] = 1;
        break;
    case 'm_ban_user':
        $_GET['action'] = 'admin';
        $_GET['area'] = 'ban';
        $_GET['sa'] = 'edit';
        $_POST['ban_name'] = $request_params[0];
        $_POST['reason'] = $request_params[2];
        $_POST['expiration'] = 'never';
        $_POST['full_ban'] = 1;
        $_POST['ban_suggestion'] = array('email', 'user');
        $_POST['add_ban'] = 'Add';
        $_POST['expiration'] = 'never';
        break;
    case 'login_mod':
        $_GET['action'] = 'admin';
        $_POST['user'] = $request_params[0];
        $_POST['password'] = $request_params[1];
        break;
    case 'm_get_moderate_topic':
    case 'm_get_moderate_post':
    case 'm_get_report_post':
        $_GET['action'] = 'moderate';
        $_GET['area'] = 'reports';
        $_GET['sa'] = 'open';
        $start_num = intval(isset($request_params[0]) ? $request_params[0] : '0');
        $end_num = intval(isset($request_params[1]) ? $request_params[1] : '19');
        if ($start_num > $end_num) {
            get_error('Parameter Error');
        } elseif ($end_num - $start_num >= 50) {
            $end_num = $start_num + 49;
        }
        $post_per_page = $end_num - $start_num + 1;
        $_GET['start'] = $start_num;
        break;
    case 'update_push_status':
        if ($params_num == 1)
        {
            $_POST['settings'] = $request_params[0];
            $_GET['action'] = 'update_push_status';
        }
        else if($params_num == 3) 
        {
            $_POST['settings'] = $request_params[0];
            $_POST['user'] = $request_params[1];
            $_POST['password'] = $request_params[2];
            $_POST['cookielength'] = -1;
            $_GET['action'] = 'login2';
        }else {
            get_error('Parameter Error');
        }
        break;
}

