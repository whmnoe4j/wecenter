<?php/*+--------------------------------------------------------------------------|   Anwsion [#RELEASE_VERSION#]|   ========================================|   by Anwsion dev team|   (c) 2011 - 2012 Anwsion Software|   http://www.anwsion.com|   ========================================|   Support: zhengqiang@gmail.com|   +---------------------------------------------------------------------------*/if (!defined('IN_ANWSION')){	die;}class people_class extends AWS_MODEL{		// 更新个人首页计数	public function update_views($uid)	{		if (AWS_APP::cache()->get('update_views_people_' . md5(session_id()) . '_' . intval($question_id)))		{			return false;		}				AWS_APP::cache()->set('update_views_people_' . md5(session_id()) . '_' . intval($question_id), time(), get_setting('cache_level_normal'));				return $this->query('UPDATE ' . $this->get_table('users') . ' SET views_count = views_count + 1 WHERE uid = ' . intval($uid));	}		public function get_user_reputation_topic($uid, $user_reputation, $limit = 10)	{		$reputation_topics = $this->get_users_reputation_topic($uid, array(			$uid => $user_reputation		), $limit);				return $reputation_topics[$uid];	}		public function get_users_reputation_topic($uids, $users_reputation, $limit = 10)	{		if ($users_reputation_topics = $this->model('reputation')->get_reputation_topic($uids))		{			foreach ($users_reputation_topics as $key => $val)			{				if ($val['reputation'] < 1)				{					continue;				}								$reputation_topics[$val['uid']][] = $val;			}		}				if ($reputation_topics)		{			foreach ($reputation_topics AS $uid => $reputation_topic)			{				$reputation_topic = array_slice(aasort($reputation_topic, 'reputation', 'DESC'), 0, $limit);								foreach ($reputation_topic as $key => $val)				{					$topic_ids[$val['topic_id']] = $val['topic_id'];				}								foreach ($reputation_topic as $key => $val)				{						if ($val['reputation'] && $users_reputation[$uid])					{						$reputation_topic[$key]['percent'] = round(($val['reputation'] / $users_reputation[$uid]) * 100);					}					else					{						$reputation_topic[$key]['percent'] = 0;					}										$reputation_topic[$key]['topic_title'] = $topics[$val['topic_id']]['topic_title'];					$reputation_topic[$key]['url_token'] = $topics[$val['topic_id']]['url_token'];				}								$reputation_topics[$uid] = $reputation_topic;			}						$topics = $this->model('topic')->get_topics_by_ids($topic_ids);						foreach ($reputation_topics as $uid => $reputation_topic)			{				foreach ($reputation_topic as $key => $val)				{					$reputation_topics[$uid][$key]['topic_title'] = $topics[$val['topic_id']]['topic_title'];					$reputation_topics[$uid][$key]['url_token'] = $topics[$val['topic_id']]['url_token'];				}			}		}				return $reputation_topics;	}}