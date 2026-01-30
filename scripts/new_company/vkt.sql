-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Апр 28 2023 г., 19:56
-- Версия сервера: 10.1.48-MariaDB
-- Версия PHP: 7.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vkt`
--

-- --------------------------------------------------------

--
-- Структура таблицы `0ctrl`
--

CREATE TABLE IF NOT EXISTS `0ctrl` (
  `id` int(11) NOT NULL,
  `ctrl_dir` bigint(20) NOT NULL,
  `tm` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `admin_passw` varchar(16) NOT NULL,
  `vk_group_id` int(11) NOT NULL,
  `vk_group_url` varchar(96) NOT NULL,
  `vk_confirmation_token` varchar(10) NOT NULL,
  `callback_server_id` int(11) NOT NULL,
  `senler_secret` varchar(64) NOT NULL,
  `senler_gid_partnerka` int(11) NOT NULL,
  `senler_gid_land` int(11) NOT NULL,
  `fee_1` int(11) NOT NULL,
  `fee_2` int(11) NOT NULL,
  `fee_hello` int(11) NOT NULL,
  `hold` int(11) NOT NULL,
  `keep` int(11) NOT NULL,
  `tg_bot_notif` varchar(64) NOT NULL,
  `tg_bot_msg` varchar(64) NOT NULL,
  `tg_bot_msg_name` varchar(32) NOT NULL,
  `land_txt` text NOT NULL,
  `thanks_txt` text NOT NULL,
  `bot_first_msg` text NOT NULL,
  `land` varchar(128) NOT NULL,
  `land_txt_p` text NOT NULL,
  `thanks_txt_p` text NOT NULL,
  `bot_first_msg_p` text NOT NULL,
  `land_p` varchar(128) NOT NULL,
  `pp` mediumtext NOT NULL,
  `pixel_ya` tinytext NOT NULL,
  `pixel_vk` tinytext NOT NULL,
  `test_uid` int(11) NOT NULL,
  `bizon_api_token` tinytext NOT NULL,
  `bizon_web_duration` int(11) NOT NULL,
  `bizon_web_zachet_proc` int(11) NOT NULL,
  `tm_end` int(11) NOT NULL,
  `del` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `0ctrl_lands__`
--

CREATE TABLE IF NOT EXISTS `0ctrl_lands__` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `ctrl_id` int(11) NOT NULL,
  `land_num` int(11) NOT NULL,
  `tm_scdl` int(11) NOT NULL,
  `land_url` tinytext NOT NULL,
  `land_name` tinytext NOT NULL,
  `land_txt` text NOT NULL,
  `thanks_txt` text NOT NULL,
  `bot_first_msg` text NOT NULL,
  `land_razdel` int(11) NOT NULL,
  `fl_patner_land` tinyint(4) NOT NULL,
  `del` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `0ctrl_vkt_send_tasks`
--

CREATE TABLE IF NOT EXISTS `0ctrl_vkt_send_tasks` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `ctrl_id` int(11) NOT NULL,
  `vkt_send_id` int(11) NOT NULL,
  `vkt_send_type` tinyint(4) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `anketa`
--

CREATE TABLE IF NOT EXISTS `anketa` (
  `id` int(11) NOT NULL,
  `email` varchar(48) NOT NULL,
  `qid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `answer` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `anketa_google`
--

CREATE TABLE IF NOT EXISTS `anketa_google` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `answ` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `anketa_questions`
--

CREATE TABLE IF NOT EXISTS `anketa_questions` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `comm` mediumtext NOT NULL,
  `num` varchar(11) NOT NULL,
  `fld_type` varchar(12) NOT NULL,
  `del` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `avangard`
--

CREATE TABLE IF NOT EXISTS `avangard` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_number` varchar(100) NOT NULL,
  `order_descr` text NOT NULL,
  `ticket` varchar(40) NOT NULL,
  `amount` int(11) NOT NULL,
  `amount1` int(11) NOT NULL,
  `c_name` varchar(128) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(64) NOT NULL,
  `vk_uid` int(11) NOT NULL,
  `res` int(11) NOT NULL,
  `best2pay_id` int(4) NOT NULL,
  `currency` int(11) NOT NULL,
  `prodamus_id` int(11) NOT NULL,
  `tm_end` int(11) NOT NULL,
  `gk_id` int(11) NOT NULL,
  `gk_uid` int(11) NOT NULL,
  `gk_cost_money` int(11) NOT NULL,
  `gk_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='avangard';

-- --------------------------------------------------------

--
-- Структура таблицы `avangard_s1`
--

CREATE TABLE IF NOT EXISTS `avangard_s1` (
  `id` int(11) NOT NULL,
  `s` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `times` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `avangard_stock`
--

CREATE TABLE IF NOT EXISTS `avangard_stock` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `ban`
--

CREATE TABLE IF NOT EXISTS `ban` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `bizon`
--

CREATE TABLE IF NOT EXISTS `bizon` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `dt` varchar(16) NOT NULL,
  `name` varchar(48) NOT NULL,
  `email` varchar(48) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `device` varchar(3) NOT NULL,
  `dt_from` varchar(8) NOT NULL,
  `dt_to` varchar(8) NOT NULL,
  `activity` int(11) NOT NULL,
  `click_banner` text NOT NULL,
  `click_button` text NOT NULL,
  `city` varchar(48) NOT NULL,
  `region` varchar(48) NOT NULL,
  `vk_uid` int(11) NOT NULL,
  `vk_name` varchar(48) NOT NULL,
  `comm` text NOT NULL,
  `webinar_id` varchar(48) NOT NULL,
  `attempt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `bizon_log`
--

CREATE TABLE IF NOT EXISTS `bizon_log` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `email` varchar(48) NOT NULL,
  `tm` int(11) NOT NULL,
  `dt` varchar(10) NOT NULL,
  `msg` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `blog`
--

CREATE TABLE IF NOT EXISTS `blog` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `topic_lat` varchar(255) NOT NULL,
  `article` text NOT NULL,
  `author` varchar(128) NOT NULL,
  `del` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `cards`
--

CREATE TABLE IF NOT EXISTS `cards` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uid_md5` varchar(32) NOT NULL,
  `uid_md5_n` tinyint(4) NOT NULL,
  `acc_id` int(11) NOT NULL,
  `acc_id_orig` int(11) NOT NULL,
  `fl_newmsg` int(11) NOT NULL,
  `tm_lastmsg` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `surname` varchar(32) NOT NULL,
  `mob` varchar(32) NOT NULL,
  `city` varchar(32) NOT NULL,
  `email` varchar(48) NOT NULL,
  `insta` varchar(64) NOT NULL,
  `comm` mediumtext NOT NULL,
  `comm1` mediumtext NOT NULL,
  `images` mediumtext NOT NULL,
  `source_id` int(11) NOT NULL,
  `source_vote` varchar(11) NOT NULL,
  `razdel` int(11) NOT NULL,
  `tm_delay` int(11) NOT NULL,
  `tm_schedule` int(11) NOT NULL,
  `scdl_opt` int(11) NOT NULL,
  `scdl_fl` int(11) NOT NULL,
  `scdl_web_id` int(11) NOT NULL,
  `fl` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tm_userid` int(11) NOT NULL,
  `del` tinyint(4) NOT NULL,
  `lock_tm` int(11) NOT NULL,
  `lock_user_id` int(11) NOT NULL,
  `dont_disp_in_new` tinyint(4) NOT NULL,
  `fl_sender` int(11) NOT NULL,
  `birthday` varchar(4) NOT NULL,
  `age` int(11) NOT NULL,
  `stage` int(11) NOT NULL,
  `anketa` int(4) NOT NULL,
  `week0` int(11) NOT NULL,
  `mob_search` varchar(15) NOT NULL,
  `got_calls` tinyint(4) NOT NULL,
  `utm_affiliate` int(11) NOT NULL,
  `pact_conversation_id` int(11) NOT NULL,
  `pact_insta_cid` int(11) NOT NULL,
  `telegram_id` bigint(14) NOT NULL,
  `telegram_nic` varchar(32) NOT NULL,
  `vk_id` int(11) NOT NULL,
  `tzoffset` int(4) NOT NULL,
  `tm_first_time_opened` int(11) NOT NULL,
  `wa_allowed` tinyint(4) NOT NULL,
  `funnel_id` int(11) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `cnt_active` int(11) NOT NULL,
  `tm_last_active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `cards_wa_name`
--

CREATE TABLE IF NOT EXISTS `cards_wa_name` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `ava` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `cart`
--

CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `price_num` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `qnt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `course_access`
--

CREATE TABLE IF NOT EXISTS `course_access` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `tm1` int(11) NOT NULL,
  `tm2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `course_access_prolong`
--

CREATE TABLE IF NOT EXISTS `course_access_prolong` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `cnt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `course_asanas`
--

CREATE TABLE IF NOT EXISTS `course_asanas` (
  `id` int(11) NOT NULL,
  `asana` varchar(128) NOT NULL,
  `fname` varchar(128) NOT NULL,
  `link` varchar(64) NOT NULL,
  `fl_beg` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `course_log`
--

CREATE TABLE IF NOT EXISTS `course_log` (
  `id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `tm` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `referer` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `discount`
--

CREATE TABLE IF NOT EXISTS `discount` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `dt1` int(11) NOT NULL,
  `dt2` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `product_id` int(11) NOT NULL DEFAULT '1',
  `price_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `expenses`
--

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `chanal_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `funnels`
--

CREATE TABLE IF NOT EXISTS `funnels` (
  `id` int(11) NOT NULL,
  `funnel` varchar(64) NOT NULL,
  `tm` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `head_control`
--

CREATE TABLE IF NOT EXISTS `head_control` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `comm` text NOT NULL,
  `del` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `index_log`
--

CREATE TABLE IF NOT EXISTS `index_log` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `client_name` varchar(32) NOT NULL,
  `client_phone` varchar(32) NOT NULL,
  `client_email` varchar(48) NOT NULL,
  `code` varchar(16) NOT NULL,
  `pact_phone` bigint(20) NOT NULL,
  `klid` int(11) NOT NULL,
  `tzoffset` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `lands`
--

CREATE TABLE IF NOT EXISTS `lands` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `land_num` int(11) NOT NULL,
  `tm_scdl` int(11) NOT NULL,
  `land_url` tinytext NOT NULL,
  `land_name` tinytext NOT NULL,
  `land_txt` text NOT NULL,
  `thanks_txt` text NOT NULL,
  `bot_first_msg` text NOT NULL,
  `land_razdel` int(11) NOT NULL,
  `fl_partner_land` tinyint(4) NOT NULL,
  `fl_disp_phone` tinyint(4) NOT NULL DEFAULT '1',
  `fl_disp_email` tinyint(4) NOT NULL,
  `fl_disp_comm` tinyint(4) NOT NULL,
  `label_disp_comm` tinytext NOT NULL,
  `del` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `last_uid`
--

CREATE TABLE IF NOT EXISTS `last_uid` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `leadgen_cost`
--

CREATE TABLE IF NOT EXISTS `leadgen_cost` (
  `id` int(11) NOT NULL,
  `cost_per_lead` int(11) NOT NULL,
  `tm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `leadgen_leads`
--

CREATE TABLE IF NOT EXISTS `leadgen_leads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `sale` tinyint(4) NOT NULL,
  `promo_code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `leadgen_log`
--

CREATE TABLE IF NOT EXISTS `leadgen_log` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `klid` int(11) NOT NULL,
  `utm_source` varchar(32) NOT NULL,
  `utm_medium` varchar(32) NOT NULL,
  `utm_campaign` varchar(32) NOT NULL,
  `utm_content` varchar(32) NOT NULL,
  `utm_term` varchar(32) NOT NULL,
  `utm_ab` varchar(16) NOT NULL,
  `fbp` varchar(255) NOT NULL,
  `fbc` varchar(255) NOT NULL,
  `fbclid` varchar(128) NOT NULL,
  `user_agent` text NOT NULL,
  `ip` varchar(24) NOT NULL,
  `referer` text NOT NULL,
  `bc_get` varchar(32) NOT NULL,
  `bc_code` varchar(32) NOT NULL,
  `res` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `leadgen_orders`
--

CREATE TABLE IF NOT EXISTS `leadgen_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `sum_pay` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uid1` int(11) NOT NULL,
  `comm` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `log_server`
--

CREATE TABLE IF NOT EXISTS `log_server` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `maillist`
--

CREATE TABLE IF NOT EXISTS `maillist` (
  `id` int(11) NOT NULL,
  `tm_cr` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm_send` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `msgs`
--

CREATE TABLE IF NOT EXISTS `msgs` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `acc_id` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `msg` mediumtext NOT NULL,
  `outg` tinyint(4) NOT NULL,
  `imp` int(11) NOT NULL,
  `new` int(11) NOT NULL,
  `vote` varchar(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `custom` int(11) NOT NULL,
  `razdel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `msgs_attachments`
--

CREATE TABLE IF NOT EXISTS `msgs_attachments` (
  `id` int(11) NOT NULL,
  `msgs_id` int(11) NOT NULL,
  `url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `msgs_hook`
--

CREATE TABLE IF NOT EXISTS `msgs_hook` (
  `tm` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `msgs_templates`
--

CREATE TABLE IF NOT EXISTS `msgs_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `msg` mediumtext NOT NULL,
  `del` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `pact_state`
--

CREATE TABLE IF NOT EXISTS `pact_state` (
  `id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `src` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `pact_test`
--

CREATE TABLE IF NOT EXISTS `pact_test` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `phone` bigint(20) NOT NULL,
  `test_id` int(11) NOT NULL,
  `res` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `partnerka_balance`
--

CREATE TABLE IF NOT EXISTS `partnerka_balance` (
  `id` int(11) NOT NULL,
  `klid` int(11) NOT NULL,
  `m` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `sum_p` int(11) NOT NULL,
  `sum_r` int(11) NOT NULL,
  `comm` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `partnerka_op`
--

CREATE TABLE IF NOT EXISTS `partnerka_op` (
  `id` int(11) NOT NULL,
  `klid_up` int(11) NOT NULL,
  `klid` int(11) NOT NULL,
  `avangard_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `fee` int(11) NOT NULL,
  `fee_sum` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `partnerka_pay`
--

CREATE TABLE IF NOT EXISTS `partnerka_pay` (
  `id` int(11) NOT NULL,
  `klid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `sum_pay` int(11) NOT NULL,
  `comm` text NOT NULL,
  `vid` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `partnerka_users`
--

CREATE TABLE IF NOT EXISTS `partnerka_users` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `klid` int(11) NOT NULL,
  `email` varchar(64) NOT NULL,
  `pwd` varchar(32) NOT NULL,
  `fee` int(11) NOT NULL,
  `fee2` int(11) NOT NULL,
  `bank_details` text NOT NULL,
  `typ` int(11) NOT NULL,
  `levels` int(11) NOT NULL,
  `del` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `pay_systems`
--

CREATE TABLE IF NOT EXISTS `pay_systems` (
  `id` int(11) NOT NULL,
  `prodamus_secret` tinytext NOT NULL,
  `prodamus_linktoform` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `pixel`
--

CREATE TABLE IF NOT EXISTS `pixel` (
  `id` int(11) NOT NULL,
  `pwd_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `dt1` int(11) NOT NULL,
  `tm1` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `pixel_pages`
--

CREATE TABLE IF NOT EXISTS `pixel_pages` (
  `id` int(11) NOT NULL,
  `pwd` varchar(512) NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `ppl`
--

CREATE TABLE IF NOT EXISTS `ppl` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `step` int(11) NOT NULL,
  `tm1` int(11) NOT NULL,
  `done` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `promo`
--

CREATE TABLE IF NOT EXISTS `promo` (
  `id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `pic_id` int(11) NOT NULL,
  `promo_name` varchar(255) NOT NULL,
  `promo_text` text NOT NULL,
  `num` int(11) NOT NULL,
  `del` tinyint(4) NOT NULL,
  `tm_wall_lastsent` int(11) NOT NULL,
  `vk_num` int(11) NOT NULL,
  `youtube` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `promocodes`
--

CREATE TABLE IF NOT EXISTS `promocodes` (
  `id` int(11) NOT NULL,
  `tm1` int(11) NOT NULL,
  `tm2` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `promocode` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `promo_send`
--

CREATE TABLE IF NOT EXISTS `promo_send` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `stop` tinyint(4) NOT NULL,
  `tm_reg` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `cnt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `promo_send_log`
--

CREATE TABLE IF NOT EXISTS `promo_send_log` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `promo_id` int(11) NOT NULL,
  `res` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='promo_send_log';

-- --------------------------------------------------------

--
-- Структура таблицы `quiz`
--

CREATE TABLE IF NOT EXISTS `quiz` (
  `id` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `hdr` varchar(36) NOT NULL,
  `point` varchar(8) NOT NULL,
  `label` varchar(64) NOT NULL,
  `comm` varchar(8) NOT NULL,
  `quantity` varchar(8) NOT NULL,
  `comment` text NOT NULL,
  `uid` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `tm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `quiz_utm`
--

CREATE TABLE IF NOT EXISTS `quiz_utm` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `utm_term` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `razdel`
--

CREATE TABLE IF NOT EXISTS `razdel` (
  `id` int(11) NOT NULL,
  `razdel_name` varchar(64) NOT NULL,
  `del` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `refs`
--

CREATE TABLE IF NOT EXISTS `refs` (
  `id` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `num1` int(11) NOT NULL,
  `pic` varchar(128) NOT NULL,
  `age` varchar(8) NOT NULL,
  `ref_name` varchar(128) NOT NULL,
  `ref_text` text NOT NULL,
  `ref_problem` text NOT NULL,
  `del` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `refs_new`
--

CREATE TABLE IF NOT EXISTS `refs_new` (
  `id` int(11) NOT NULL,
  `dir` varchar(48) NOT NULL,
  `first_name` varchar(24) NOT NULL,
  `last_name` varchar(24) NOT NULL,
  `age` int(11) NOT NULL,
  `brief` text NOT NULL,
  `del` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `russian_names`
--

CREATE TABLE IF NOT EXISTS `russian_names` (
  `id` int(11) NOT NULL,
  `name` varchar(22) NOT NULL,
  `sex` int(11) NOT NULL,
  `cnt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `sales_script_items`
--

CREATE TABLE IF NOT EXISTS `sales_script_items` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `typ` int(11) NOT NULL,
  `item` text NOT NULL,
  `comm` text NOT NULL,
  `del` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `sales_script_names`
--

CREATE TABLE IF NOT EXISTS `sales_script_names` (
  `id` int(11) NOT NULL,
  `sales_script_name` varchar(64) NOT NULL,
  `del` int(11) NOT NULL,
  `fl_call_script` int(11) NOT NULL,
  `fl_private` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `sender_log`
--

CREATE TABLE IF NOT EXISTS `sender_log` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `sp_template` int(11) NOT NULL,
  `num_log` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `senler_log`
--

CREATE TABLE IF NOT EXISTS `senler_log` (
  `id` int(11) NOT NULL,
  `typ` varchar(12) NOT NULL,
  `gid` int(11) NOT NULL,
  `dt` varchar(20) NOT NULL,
  `tm` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `source` varchar(20) NOT NULL,
  `first` int(11) NOT NULL,
  `full_unsubscribe` int(11) NOT NULL,
  `in_promo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `senler_stat`
--

CREATE TABLE IF NOT EXISTS `senler_stat` (
  `id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tag_name` varchar(128) NOT NULL,
  `tm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `sources`
--

CREATE TABLE IF NOT EXISTS `sources` (
  `id` int(11) NOT NULL,
  `source_name` varchar(128) NOT NULL,
  `priority` int(11) NOT NULL,
  `del` tinyint(4) NOT NULL,
  `razdel_id` int(11) NOT NULL,
  `fl_client` int(11) NOT NULL,
  `use_in_cards` tinyint(4) NOT NULL,
  `access_level` int(11) NOT NULL,
  `hant_level` int(11) NOT NULL,
  `for_touch` int(11) NOT NULL,
  `source_comm` varchar(512) NOT NULL,
  `fl_active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `stock`
--

CREATE TABLE IF NOT EXISTS `stock` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `telegram`
--

CREATE TABLE IF NOT EXISTS `telegram` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `confirmed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `tg_channel`
--

CREATE TABLE IF NOT EXISTS `tg_channel` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `tg_id` int(11) NOT NULL,
  `tg_nic` varchar(32) NOT NULL,
  `f_name` varchar(32) NOT NULL,
  `l_name` varchar(32) NOT NULL,
  `res` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `tg_public_yoga`
--

CREATE TABLE IF NOT EXISTS `tg_public_yoga` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `tg_id` int(11) NOT NULL,
  `tg_nic` varchar(32) NOT NULL,
  `f_name` varchar(32) NOT NULL,
  `l_name` varchar(32) NOT NULL,
  `res` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `tmp_wa_send`
--

CREATE TABLE IF NOT EXISTS `tmp_wa_send` (
  `id` int(11) NOT NULL,
  `mob` bigint(20) NOT NULL,
  `done` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `tz_info`
--

CREATE TABLE IF NOT EXISTS `tz_info` (
  `id` int(11) NOT NULL,
  `address` varchar(64) DEFAULT NULL,
  `postal_code` varchar(6) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `federal_district` varchar(20) DEFAULT NULL,
  `region_type` varchar(6) DEFAULT NULL,
  `region` varchar(20) DEFAULT NULL,
  `area_type` varchar(6) DEFAULT NULL,
  `area` varchar(20) DEFAULT NULL,
  `city_type` varchar(6) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `settlement_type` varchar(6) DEFAULT NULL,
  `settlement` varchar(20) DEFAULT NULL,
  `kladr_id` varchar(16) DEFAULT NULL,
  `fias_id` varchar(36) DEFAULT NULL,
  `fias_level` varchar(3) DEFAULT NULL,
  `capital_marker` varchar(3) DEFAULT NULL,
  `okato` varchar(12) DEFAULT NULL,
  `oktmo` varchar(12) DEFAULT NULL,
  `tax_office` varchar(4) DEFAULT NULL,
  `timezone` int(11) DEFAULT NULL,
  `geo_lat` varchar(10) DEFAULT NULL,
  `geo_lon` varchar(10) DEFAULT NULL,
  `population` int(11) DEFAULT NULL,
  `foundation_year` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `0ctrl` int(11) NOT NULL,
  `username` varchar(24) NOT NULL,
  `real_user_name` varchar(48) NOT NULL,
  `tg_nick` varchar(48) NOT NULL,
  `wa_user_name` varchar(48) NOT NULL,
  `email_from_name` varchar(64) NOT NULL,
  `email` varchar(48) NOT NULL,
  `uid` int(11) NOT NULL,
  `sip` int(11) NOT NULL,
  `callback_url` varchar(255) NOT NULL,
  `acc_id` int(11) NOT NULL,
  `klid` int(11) NOT NULL,
  `gk_code` varchar(16) NOT NULL,
  `passw` varchar(64) NOT NULL,
  `token` varchar(128) NOT NULL,
  `access_level` int(11) NOT NULL,
  `tm_lastlogin` int(11) NOT NULL,
  `comm` mediumtext NOT NULL,
  `telegram_id` bigint(14) NOT NULL,
  `pact_channel_id` int(11) NOT NULL,
  `fl_notify_if_new` int(11) NOT NULL,
  `fl_notify_if_other` int(11) NOT NULL,
  `fl_notify_of_own_only` tinyint(4) NOT NULL,
  `fl_allowlogin` int(11) NOT NULL,
  `del` int(11) NOT NULL,
  `pact_phone` varchar(15) NOT NULL,
  `garant` int(11) NOT NULL,
  `pact_company_id` int(11) NOT NULL,
  `pact_token` varchar(128) NOT NULL,
  `pact_channel_online` int(11) NOT NULL,
  `fb_pixel` bigint(20) NOT NULL,
  `leadgen` int(11) NOT NULL,
  `leadgen_rate` int(11) NOT NULL,
  `leadgen_cnt` int(11) NOT NULL,
  `leadgen_tm1` int(11) NOT NULL,
  `leadgen_stop_global` int(11) NOT NULL,
  `leadgen_stop_user_action` int(11) NOT NULL,
  `fee` int(11) NOT NULL,
  `fee2` int(11) NOT NULL,
  `levels` int(11) NOT NULL,
  `bank_details` int(11) NOT NULL,
  `chk_nalog` int(11) NOT NULL,
  `bc` varchar(11) NOT NULL,
  `direct_code` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `utm`
--

CREATE TABLE IF NOT EXISTS `utm` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `utm_source` varchar(32) NOT NULL,
  `utm_medium` varchar(32) NOT NULL,
  `utm_campaign` varchar(32) NOT NULL,
  `utm_content` varchar(32) NOT NULL,
  `utm_term` varchar(32) NOT NULL,
  `utm_ab` varchar(16) NOT NULL,
  `pwd_id` int(11) NOT NULL,
  `promo_code` varchar(16) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `mob` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist`
--

CREATE TABLE IF NOT EXISTS `vklist` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `vk_gid` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `tm_cr` int(11) NOT NULL,
  `tm_msg` int(11) NOT NULL,
  `tm_friends` int(11) NOT NULL,
  `tm_wall` int(11) NOT NULL,
  `res_msg` int(4) NOT NULL,
  `res_friends` int(4) NOT NULL,
  `res_wall` int(4) NOT NULL,
  `blocked` int(11) NOT NULL,
  `vote` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_acc`
--

CREATE TABLE IF NOT EXISTS `vklist_acc` (
  `id` int(11) NOT NULL,
  `vk_uid` int(11) NOT NULL,
  `login` varchar(24) NOT NULL,
  `passw` varchar(24) NOT NULL,
  `email` varchar(48) NOT NULL,
  `email_passw` varchar(24) NOT NULL,
  `tm` int(11) NOT NULL,
  `last_mid` int(11) NOT NULL,
  `ban_cnt` int(11) NOT NULL,
  `last_error` int(11) NOT NULL,
  `last_error_msg` varchar(255) NOT NULL,
  `token` varchar(512) NOT NULL,
  `name` varchar(255) NOT NULL,
  `comm` mediumtext NOT NULL,
  `tm_next_send_msg` int(11) NOT NULL,
  `fl_acc_allowed` int(11) NOT NULL,
  `fl_acc_not_allowed_for_new` int(11) NOT NULL,
  `fl_allow_read_from_all` int(11) NOT NULL COMMENT 'even if not in cards or vklist',
  `del` tinyint(4) NOT NULL,
  `fr_capcha_uid` int(11) NOT NULL,
  `num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_acc_log`
--

CREATE TABLE IF NOT EXISTS `vklist_acc_log` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `msg` mediumtext NOT NULL,
  `dt` varchar(24) NOT NULL,
  `uid` int(11) NOT NULL,
  `acc_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `err` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_ctrl`
--

CREATE TABLE IF NOT EXISTS `vklist_ctrl` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `tm_finished` int(11) NOT NULL,
  `cnt` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `stop` int(11) NOT NULL,
  `busy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_groups`
--

CREATE TABLE IF NOT EXISTS `vklist_groups` (
  `id` int(11) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `group_name` varchar(512) NOT NULL,
  `vote` varchar(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `msg` mediumtext NOT NULL,
  `fl_send_msg` tinyint(4) NOT NULL,
  `fl_send_wall` tinyint(4) NOT NULL,
  `fl_send_friends` tinyint(4) NOT NULL,
  `fl_autosend` tinyint(4) NOT NULL,
  `del` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_log`
--

CREATE TABLE IF NOT EXISTS `vklist_log` (
  `id` int(11) NOT NULL,
  `dt` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `acc_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `op` varchar(12) NOT NULL,
  `mode` tinyint(4) NOT NULL,
  `err` int(11) NOT NULL,
  `response` varchar(128) NOT NULL,
  `ctrl_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_scan_groups`
--

CREATE TABLE IF NOT EXISTS `vklist_scan_groups` (
  `id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `sex` varchar(1) NOT NULL,
  `first_name` varchar(24) NOT NULL,
  `last_name` varchar(24) NOT NULL,
  `city_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `city` varchar(24) NOT NULL,
  `country` varchar(12) NOT NULL,
  `bdate` varchar(10) NOT NULL,
  `age` tinyint(4) NOT NULL,
  `tm` int(11) NOT NULL,
  `d` tinyint(4) NOT NULL,
  `m` tinyint(4) NOT NULL,
  `y` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_scan_likes`
--

CREATE TABLE IF NOT EXISTS `vklist_scan_likes` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `tm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_scan_reposts`
--

CREATE TABLE IF NOT EXISTS `vklist_scan_reposts` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `tm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_scan_votes`
--

CREATE TABLE IF NOT EXISTS `vklist_scan_votes` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `vote_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vktrade2vk`
--

CREATE TABLE IF NOT EXISTS `vktrade2vk` (
  `code` int(11) NOT NULL,
  `vktrade_uid` int(11) NOT NULL,
  `vk_uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `promocode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vktrade_send_at_log`
--

CREATE TABLE IF NOT EXISTS `vktrade_send_at_log` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `tm1` int(11) NOT NULL,
  `tm2` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `msg_md5` varchar(32) NOT NULL,
  `fname` varchar(48) NOT NULL,
  `res_wa` tinyint(4) NOT NULL,
  `res_email` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vktrade_send_at_msgs`
--

CREATE TABLE IF NOT EXISTS `vktrade_send_at_msgs` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `msg` text NOT NULL,
  `tm` int(11) NOT NULL,
  `dt` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vktrade_send_unsubscribe`
--

CREATE TABLE IF NOT EXISTS `vktrade_send_unsubscribe` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `messenger_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vkt_send_1`
--

CREATE TABLE IF NOT EXISTS `vkt_send_1` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `vkt_send_tm` int(11) NOT NULL,
  `tm_shift` int(11) NOT NULL,
  `land_num` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `name_send` varchar(128) NOT NULL,
  `msg` text NOT NULL,
  `vk_attach` tinytext NOT NULL,
  `tg_image` tinytext NOT NULL,
  `tg_video` tinytext NOT NULL,
  `tg_video_note` tinytext NOT NULL,
  `tg_audio` tinytext NOT NULL,
  `tm1` int(11) NOT NULL,
  `tm2` int(11) NOT NULL,
  `fl_clients` tinyint(4) NOT NULL,
  `fl_partners` tinyint(4) NOT NULL,
  `fl_leads` tinyint(4) NOT NULL,
  `fl_razdel` int(11) NOT NULL,
  `del` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vkt_send_log`
--

CREATE TABLE IF NOT EXISTS `vkt_send_log` (
  `id` int(11) NOT NULL,
  `vkt_send_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `res_email` tinyint(4) NOT NULL,
  `res_vk` tinyint(4) NOT NULL,
  `res_wa` tinyint(4) NOT NULL,
  `res_tg` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `vk_wall_comments`
--

CREATE TABLE IF NOT EXISTS `vk_wall_comments` (
  `id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `vk_group_id` int(11) NOT NULL,
  `vk_post_id` int(11) NOT NULL,
  `vk_comment_id` int(11) NOT NULL,
  `vk_comment` text NOT NULL,
  `vk_uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `yd_log`
--

CREATE TABLE IF NOT EXISTS `yd_log` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `tm` int(11) NOT NULL,
  `get_key` varchar(16) NOT NULL,
  `get_val` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `0ctrl`
--
ALTER TABLE `0ctrl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `senler_secret` (`senler_secret`),
  ADD KEY `tm` (`tm`),
  ADD KEY `del` (`del`),
  ADD KEY `tm_end` (`tm_end`),
  ADD KEY `ctrl_id` (`ctrl_dir`);

--
-- Индексы таблицы `0ctrl_lands__`
--
ALTER TABLE `0ctrl_lands__`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `ctrl_id` (`ctrl_id`),
  ADD KEY `tm_scdl` (`tm_scdl`),
  ADD KEY `del` (`del`),
  ADD KEY `land_num` (`land_num`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `0ctrl_vkt_send_tasks`
--
ALTER TABLE `0ctrl_vkt_send_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `vkt_send_id` (`vkt_send_id`),
  ADD KEY `ctrl_dir` (`vkt_send_type`),
  ADD KEY `ctrl_id` (`ctrl_id`);

--
-- Индексы таблицы `anketa`
--
ALTER TABLE `anketa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`email`),
  ADD KEY `qid` (`qid`,`tm`);

--
-- Индексы таблицы `anketa_google`
--
ALTER TABLE `anketa_google`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `anketa_questions`
--
ALTER TABLE `anketa_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`),
  ADD KEY `num` (`num`);

--
-- Индексы таблицы `avangard`
--
ALTER TABLE `avangard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `order_number` (`order_number`),
  ADD KEY `res` (`res`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `vk_uid` (`vk_uid`),
  ADD KEY `best2pay` (`best2pay_id`),
  ADD KEY `prodamus_id` (`prodamus_id`),
  ADD KEY `tm_end` (`tm_end`),
  ADD KEY `gk_id` (`gk_id`),
  ADD KEY `gk_uid` (`gk_uid`),
  ADD KEY `gk_status` (`gk_status`);

--
-- Индексы таблицы `avangard_s1`
--
ALTER TABLE `avangard_s1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `avangard_stock`
--
ALTER TABLE `avangard_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `ban`
--
ALTER TABLE `ban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `bizon`
--
ALTER TABLE `bizon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `email` (`email`),
  ADD KEY `vk_uid` (`vk_uid`),
  ADD KEY `webinar_id` (`webinar_id`);

--
-- Индексы таблицы `bizon_log`
--
ALTER TABLE `bizon_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid_2` (`uid`),
  ADD KEY `uid` (`uid`,`acc_id`,`fl_newmsg`,`tm_lastmsg`,`acc_id_orig`) USING BTREE,
  ADD KEY `schedule` (`tm_schedule`),
  ADD KEY `ind` (`tm`,`source_id`,`del`,`razdel`,`tm_delay`,`fl`) USING BTREE,
  ADD KEY `user_id` (`user_id`),
  ADD KEY `birthday` (`birthday`),
  ADD KEY `tm_userid` (`tm_userid`),
  ADD KEY `scdl_fl` (`scdl_fl`),
  ADD KEY `stage` (`stage`),
  ADD KEY `anketa` (`anketa`),
  ADD KEY `week0` (`week0`),
  ADD KEY `mob_search` (`mob_search`),
  ADD KEY `got_calls` (`got_calls`),
  ADD KEY `came_from` (`utm_affiliate`),
  ADD KEY `uid_md5` (`uid_md5`),
  ADD KEY `pact_conversation_id` (`pact_conversation_id`),
  ADD KEY `wa_allowed` (`wa_allowed`),
  ADD KEY `pact_insta_cid` (`pact_insta_cid`),
  ADD KEY `scdl_web_id` (`scdl_web_id`),
  ADD KEY `funnel_id` (`funnel_id`),
  ADD KEY `telegram_id` (`telegram_id`),
  ADD KEY `lang` (`lang`),
  ADD KEY `telegram_nic` (`telegram_nic`),
  ADD KEY `vk_uid` (`vk_id`),
  ADD KEY `cnt_active` (`cnt_active`),
  ADD KEY `tm_last_active` (`tm_last_active`);

--
-- Индексы таблицы `cards_wa_name`
--
ALTER TABLE `cards_wa_name`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mob` (`cid`);

--
-- Индексы таблицы `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`product_id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `course_access`
--
ALTER TABLE `course_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm1` (`tm1`),
  ADD KEY `tm2` (`tm2`),
  ADD KEY `source_id` (`source_id`);

--
-- Индексы таблицы `course_access_prolong`
--
ALTER TABLE `course_access_prolong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `cnt` (`cnt`);

--
-- Индексы таблицы `course_asanas`
--
ALTER TABLE `course_asanas`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `course_log`
--
ALTER TABLE `course_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `discount`
--
ALTER TABLE `discount`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `dt` (`dt1`,`dt2`);

--
-- Индексы таблицы `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `chanal_id` (`chanal_id`);

--
-- Индексы таблицы `funnels`
--
ALTER TABLE `funnels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `uid` (`uid`),
  ADD KEY `funnel` (`funnel`);

--
-- Индексы таблицы `head_control`
--
ALTER TABLE `head_control`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `del` (`del`);

--
-- Индексы таблицы `index_log`
--
ALTER TABLE `index_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `md5` (`md5`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `lands`
--
ALTER TABLE `lands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `ctrl_id` (`user_id`),
  ADD KEY `tm_scdl` (`tm_scdl`),
  ADD KEY `del` (`del`),
  ADD KEY `land_num` (`land_num`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `last_uid`
--
ALTER TABLE `last_uid`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `leadgen_cost`
--
ALTER TABLE `leadgen_cost`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `leadgen_leads`
--
ALTER TABLE `leadgen_leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `sale` (`sale`),
  ADD KEY `uid` (`uid`),
  ADD KEY `promo_code` (`promo_code`);

--
-- Индексы таблицы `leadgen_log`
--
ALTER TABLE `leadgen_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `code` (`code`),
  ADD KEY `klid` (`klid`),
  ADD KEY `checked` (`res`),
  ADD KEY `utm_ab` (`utm_ab`),
  ADD KEY `bc_code` (`bc_code`);

--
-- Индексы таблицы `leadgen_orders`
--
ALTER TABLE `leadgen_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`,`uid1`);

--
-- Индексы таблицы `log_server`
--
ALTER TABLE `log_server`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `maillist`
--
ALTER TABLE `maillist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm_cr` (`tm_cr`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm_send` (`tm_send`);

--
-- Индексы таблицы `msgs`
--
ALTER TABLE `msgs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `msg` (`imp`,`acc_id`,`mid`,`tm`,`user_id`,`new`,`outg`,`uid`) USING BTREE,
  ADD KEY `vote` (`vote`),
  ADD KEY `source_id` (`source_id`),
  ADD KEY `tm_auto_answer` (`custom`),
  ADD KEY `uid` (`uid`),
  ADD KEY `razdel_id` (`razdel_id`);

--
-- Индексы таблицы `msgs_attachments`
--
ALTER TABLE `msgs_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `msgs_id` (`msgs_id`);

--
-- Индексы таблицы `msgs_hook`
--
ALTER TABLE `msgs_hook`
  ADD KEY `tm` (`tm`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `msgs_templates`
--
ALTER TABLE `msgs_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ind` (`del`);

--
-- Индексы таблицы `pact_state`
--
ALTER TABLE `pact_state`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `pact_test`
--
ALTER TABLE `pact_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `phone` (`phone`),
  ADD KEY `test_id` (`test_id`);

--
-- Индексы таблицы `partnerka_balance`
--
ALTER TABLE `partnerka_balance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `klid` (`klid`),
  ADD KEY `tm` (`m`,`y`,`tm`);

--
-- Индексы таблицы `partnerka_op`
--
ALTER TABLE `partnerka_op`
  ADD PRIMARY KEY (`id`),
  ADD KEY `klid_up` (`klid_up`),
  ADD KEY `klid` (`klid`),
  ADD KEY `avangard_id` (`avangard_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `level` (`level`);

--
-- Индексы таблицы `partnerka_pay`
--
ALTER TABLE `partnerka_pay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `klid` (`klid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `sum_p` (`sum_pay`),
  ADD KEY `vid` (`vid`);

--
-- Индексы таблицы `partnerka_users`
--
ALTER TABLE `partnerka_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `klid` (`klid`),
  ADD KEY `del` (`del`),
  ADD KEY `pwd` (`pwd`),
  ADD KEY `tm` (`tm`),
  ADD KEY `email` (`email`),
  ADD KEY `typ` (`typ`);

--
-- Индексы таблицы `pay_systems`
--
ALTER TABLE `pay_systems`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `pixel`
--
ALTER TABLE `pixel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pwd_id` (`pwd_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `dt1` (`dt1`),
  ADD KEY `tm1` (`tm1`);

--
-- Индексы таблицы `pixel_pages`
--
ALTER TABLE `pixel_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pwd` (`pwd`(191)),
  ADD KEY `weight` (`weight`);

--
-- Индексы таблицы `ppl`
--
ALTER TABLE `ppl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `uid` (`uid`),
  ADD KEY `done` (`done`),
  ADD KEY `step` (`step`),
  ADD KEY `tm1` (`tm1`);

--
-- Индексы таблицы `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`),
  ADD KEY `num` (`num`),
  ADD KEY `video_id` (`video_id`,`pic_id`),
  ADD KEY `tm_wall_lastsent` (`tm_wall_lastsent`),
  ADD KEY `vk_num` (`vk_num`);

--
-- Индексы таблицы `promocodes`
--
ALTER TABLE `promocodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm1` (`tm1`),
  ADD KEY `tm2` (`tm2`),
  ADD KEY `uid` (`uid`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `promocode` (`promocode`);

--
-- Индексы таблицы `promo_send`
--
ALTER TABLE `promo_send`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid_2` (`uid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `stop` (`stop`),
  ADD KEY `tm_reg` (`tm_reg`),
  ADD KEY `tm` (`tm`),
  ADD KEY `cnt` (`cnt`);

--
-- Индексы таблицы `promo_send_log`
--
ALTER TABLE `promo_send_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `promo_id` (`promo_id`);

--
-- Индексы таблицы `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `num` (`num`),
  ADD KEY `uid` (`uid`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `quiz_utm`
--
ALTER TABLE `quiz_utm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_id` (`visit_id`);

--
-- Индексы таблицы `razdel`
--
ALTER TABLE `razdel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`);

--
-- Индексы таблицы `refs`
--
ALTER TABLE `refs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`),
  ADD KEY `num` (`num`),
  ADD KEY `num1` (`num1`);

--
-- Индексы таблицы `refs_new`
--
ALTER TABLE `refs_new`
  ADD PRIMARY KEY (`id`),
  ADD KEY `age` (`age`),
  ADD KEY `del` (`del`);

--
-- Индексы таблицы `russian_names`
--
ALTER TABLE `russian_names`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Индексы таблицы `sales_script_items`
--
ALTER TABLE `sales_script_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sid` (`sid`),
  ADD KEY `num` (`num`),
  ADD KEY `del` (`del`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `sales_script_names`
--
ALTER TABLE `sales_script_names`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`),
  ADD KEY `fl_call_script` (`fl_call_script`),
  ADD KEY `fl_private` (`fl_private`);

--
-- Индексы таблицы `sender_log`
--
ALTER TABLE `sender_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `num_log` (`num_log`);

--
-- Индексы таблицы `senler_log`
--
ALTER TABLE `senler_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `typ` (`typ`),
  ADD KEY `gid` (`gid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `sid` (`sid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `first` (`first`),
  ADD KEY `full_unsubscribe` (`full_unsubscribe`),
  ADD KEY `in_promo` (`in_promo`);

--
-- Индексы таблицы `senler_stat`
--
ALTER TABLE `senler_stat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Индексы таблицы `sources`
--
ALTER TABLE `sources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`),
  ADD KEY `priority` (`priority`),
  ADD KEY `razdel_id` (`razdel_id`),
  ADD KEY `fl_client` (`fl_client`),
  ADD KEY `access_level` (`access_level`),
  ADD KEY `hant_level` (`hant_level`),
  ADD KEY `for_touch` (`for_touch`),
  ADD KEY `comm` (`source_comm`(191)),
  ADD KEY `fl_active` (`fl_active`);

--
-- Индексы таблицы `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `telegram`
--
ALTER TABLE `telegram`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`),
  ADD KEY `tm` (`tm`,`confirmed`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `tg_channel`
--
ALTER TABLE `tg_channel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `tg_id` (`tg_id`),
  ADD KEY `res` (`res`);

--
-- Индексы таблицы `tg_public_yoga`
--
ALTER TABLE `tg_public_yoga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `tg_id` (`tg_id`),
  ADD KEY `res` (`res`);

--
-- Индексы таблицы `tmp_wa_send`
--
ALTER TABLE `tmp_wa_send`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mob` (`mob`),
  ADD KEY `done` (`done`);

--
-- Индексы таблицы `tz_info`
--
ALTER TABLE `tz_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city` (`city`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`),
  ADD KEY `acc_id` (`acc_id`),
  ADD KEY `fl` (`fl_notify_if_new`,`fl_notify_if_other`),
  ADD KEY `sip` (`sip`),
  ADD KEY `klid` (`klid`),
  ADD KEY `gk_code` (`gk_code`),
  ADD KEY `pact_channel_id` (`pact_channel_id`),
  ADD KEY `0ctrl` (`0ctrl`),
  ADD KEY `bc` (`bc`),
  ADD KEY `direct_code` (`direct_code`),
  ADD KEY `leadgen_tm1` (`leadgen_tm1`),
  ADD KEY `leadgen_stop_global` (`leadgen_stop_global`),
  ADD KEY `leadgen_stop_user_action` (`leadgen_stop_user_action`),
  ADD KEY `levels` (`levels`);

--
-- Индексы таблицы `utm`
--
ALTER TABLE `utm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utm_source` (`utm_source`),
  ADD KEY `utm_campaign` (`utm_campaign`),
  ADD KEY `utm_content` (`utm_content`),
  ADD KEY `utm_medium` (`utm_medium`),
  ADD KEY `utm_term` (`utm_term`),
  ADD KEY `card_id` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `pwd_id` (`pwd_id`),
  ADD KEY `utm_ab` (`utm_ab`),
  ADD KEY `promo_code` (`promo_code`),
  ADD KEY `mob` (`mob`),
  ADD KEY `md5` (`md5`);

--
-- Индексы таблицы `vklist`
--
ALTER TABLE `vklist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vklist` (`group_id`,`tm_cr`,`tm_msg`,`tm_friends`,`tm_wall`,`res_msg`,`res_friends`,`res_wall`,`vote`,`vk_gid`,`blocked`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `vklist_acc`
--
ALTER TABLE `vklist_acc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `next_send` (`tm_next_send_msg`),
  ADD KEY `acc` (`tm`,`del`,`last_error`,`fl_acc_allowed`,`num`,`fr_capcha_uid`,`fl_acc_not_allowed_for_new`) USING BTREE;

--
-- Индексы таблицы `vklist_acc_log`
--
ALTER TABLE `vklist_acc_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `vklist_ctrl`
--
ALTER TABLE `vklist_ctrl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ind` (`user_id`,`tm`,`tm_finished`),
  ADD KEY `busy` (`busy`);

--
-- Индексы таблицы `vklist_groups`
--
ALTER TABLE `vklist_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vvv` (`tm`,`del`),
  ADD KEY `fl` (`fl_send_msg`,`fl_send_wall`,`fl_send_friends`,`fl_autosend`),
  ADD KEY `vote` (`vote`);

--
-- Индексы таблицы `vklist_log`
--
ALTER TABLE `vklist_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dt` (`dt`),
  ADD KEY `log` (`err`,`tm`,`acc_id`,`group_id`,`uid`,`op`,`mode`) USING BTREE,
  ADD KEY `ctrl_id` (`ctrl_id`);

--
-- Индексы таблицы `vklist_scan_groups`
--
ALTER TABLE `vklist_scan_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ind` (`gid`,`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `dmy` (`d`,`m`,`y`),
  ADD KEY `age` (`age`),
  ADD KEY `location` (`city_id`,`country_id`);

--
-- Индексы таблицы `vklist_scan_likes`
--
ALTER TABLE `vklist_scan_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`,`item_id`,`gid`);

--
-- Индексы таблицы `vklist_scan_reposts`
--
ALTER TABLE `vklist_scan_reposts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`,`item_id`,`gid`);

--
-- Индексы таблицы `vklist_scan_votes`
--
ALTER TABLE `vklist_scan_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `vote_id` (`vote_id`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `vktrade2vk`
--
ALTER TABLE `vktrade2vk`
  ADD PRIMARY KEY (`code`),
  ADD KEY `vktrade_uid` (`vktrade_uid`),
  ADD KEY `vk_uid` (`vk_uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `promocode` (`promocode`);

--
-- Индексы таблицы `vktrade_send_at_log`
--
ALTER TABLE `vktrade_send_at_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `source_id` (`source_id`),
  ADD KEY `tm1` (`tm1`),
  ADD KEY `tm2` (`tm2`),
  ADD KEY `msg_md5` (`msg_md5`),
  ADD KEY `res_email` (`res_email`);

--
-- Индексы таблицы `vktrade_send_at_msgs`
--
ALTER TABLE `vktrade_send_at_msgs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `vktrade_send_unsubscribe`
--
ALTER TABLE `vktrade_send_unsubscribe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `source_id` (`source_id`),
  ADD KEY `messenger_id` (`messenger_id`);

--
-- Индексы таблицы `vkt_send_1`
--
ALTER TABLE `vkt_send_1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `name_send` (`name_send`),
  ADD KEY `del` (`del`),
  ADD KEY `vkt_send_tm` (`vkt_send_tm`),
  ADD KEY `vkt_send_scdl_tm` (`land_num`),
  ADD KEY `vkt_send_sid` (`sid`);

--
-- Индексы таблицы `vkt_send_log`
--
ALTER TABLE `vkt_send_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `res_email` (`res_email`,`res_vk`,`res_wa`,`res_tg`),
  ADD KEY `vkt_send_id` (`vkt_send_id`);

--
-- Индексы таблицы `vk_wall_comments`
--
ALTER TABLE `vk_wall_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `vk_group_id` (`vk_group_id`),
  ADD KEY `vk_post_id` (`vk_post_id`),
  ADD KEY `vk_comment_id` (`vk_comment_id`),
  ADD KEY `vk_uid` (`vk_uid`);

--
-- Индексы таблицы `yd_log`
--
ALTER TABLE `yd_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `tm` (`tm`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `0ctrl`
--
ALTER TABLE `0ctrl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `0ctrl_lands__`
--
ALTER TABLE `0ctrl_lands__`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `0ctrl_vkt_send_tasks`
--
ALTER TABLE `0ctrl_vkt_send_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `anketa`
--
ALTER TABLE `anketa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `anketa_google`
--
ALTER TABLE `anketa_google`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `anketa_questions`
--
ALTER TABLE `anketa_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `avangard`
--
ALTER TABLE `avangard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `avangard_s1`
--
ALTER TABLE `avangard_s1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `avangard_stock`
--
ALTER TABLE `avangard_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `ban`
--
ALTER TABLE `ban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `bizon`
--
ALTER TABLE `bizon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `bizon_log`
--
ALTER TABLE `bizon_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `cards_wa_name`
--
ALTER TABLE `cards_wa_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `course_access`
--
ALTER TABLE `course_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `course_access_prolong`
--
ALTER TABLE `course_access_prolong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `course_asanas`
--
ALTER TABLE `course_asanas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `course_log`
--
ALTER TABLE `course_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `discount`
--
ALTER TABLE `discount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `funnels`
--
ALTER TABLE `funnels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `head_control`
--
ALTER TABLE `head_control`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `index_log`
--
ALTER TABLE `index_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `lands`
--
ALTER TABLE `lands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `leadgen_cost`
--
ALTER TABLE `leadgen_cost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `leadgen_leads`
--
ALTER TABLE `leadgen_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `leadgen_log`
--
ALTER TABLE `leadgen_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `leadgen_orders`
--
ALTER TABLE `leadgen_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `links`
--
ALTER TABLE `links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `log_server`
--
ALTER TABLE `log_server`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `maillist`
--
ALTER TABLE `maillist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `msgs`
--
ALTER TABLE `msgs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `msgs_attachments`
--
ALTER TABLE `msgs_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `msgs_templates`
--
ALTER TABLE `msgs_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `pact_state`
--
ALTER TABLE `pact_state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `pact_test`
--
ALTER TABLE `pact_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `partnerka_balance`
--
ALTER TABLE `partnerka_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `partnerka_op`
--
ALTER TABLE `partnerka_op`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `partnerka_pay`
--
ALTER TABLE `partnerka_pay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `partnerka_users`
--
ALTER TABLE `partnerka_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `pay_systems`
--
ALTER TABLE `pay_systems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `pixel`
--
ALTER TABLE `pixel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `pixel_pages`
--
ALTER TABLE `pixel_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `ppl`
--
ALTER TABLE `ppl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `promocodes`
--
ALTER TABLE `promocodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `promo_send`
--
ALTER TABLE `promo_send`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `promo_send_log`
--
ALTER TABLE `promo_send_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `quiz_utm`
--
ALTER TABLE `quiz_utm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `razdel`
--
ALTER TABLE `razdel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `refs`
--
ALTER TABLE `refs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `refs_new`
--
ALTER TABLE `refs_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `russian_names`
--
ALTER TABLE `russian_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `sales_script_items`
--
ALTER TABLE `sales_script_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `sales_script_names`
--
ALTER TABLE `sales_script_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `sender_log`
--
ALTER TABLE `sender_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `senler_log`
--
ALTER TABLE `senler_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `senler_stat`
--
ALTER TABLE `senler_stat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `telegram`
--
ALTER TABLE `telegram`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `tg_channel`
--
ALTER TABLE `tg_channel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `tg_public_yoga`
--
ALTER TABLE `tg_public_yoga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `tmp_wa_send`
--
ALTER TABLE `tmp_wa_send`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `tz_info`
--
ALTER TABLE `tz_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `utm`
--
ALTER TABLE `utm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist`
--
ALTER TABLE `vklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist_acc`
--
ALTER TABLE `vklist_acc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist_acc_log`
--
ALTER TABLE `vklist_acc_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist_ctrl`
--
ALTER TABLE `vklist_ctrl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist_groups`
--
ALTER TABLE `vklist_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist_log`
--
ALTER TABLE `vklist_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist_scan_groups`
--
ALTER TABLE `vklist_scan_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist_scan_likes`
--
ALTER TABLE `vklist_scan_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist_scan_reposts`
--
ALTER TABLE `vklist_scan_reposts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vklist_scan_votes`
--
ALTER TABLE `vklist_scan_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vktrade2vk`
--
ALTER TABLE `vktrade2vk`
  MODIFY `code` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vktrade_send_at_log`
--
ALTER TABLE `vktrade_send_at_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vktrade_send_at_msgs`
--
ALTER TABLE `vktrade_send_at_msgs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vktrade_send_unsubscribe`
--
ALTER TABLE `vktrade_send_unsubscribe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vkt_send_1`
--
ALTER TABLE `vkt_send_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vkt_send_log`
--
ALTER TABLE `vkt_send_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vk_wall_comments`
--
ALTER TABLE `vk_wall_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `yd_log`
--
ALTER TABLE `yd_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
