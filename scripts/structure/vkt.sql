-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el8
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Янв 28 2026 г., 16:51
-- Версия сервера: 8.0.36
-- Версия PHP: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
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

CREATE TABLE `0ctrl` (
  `id` int NOT NULL,
  `ctrl_dir` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `tm` int NOT NULL,
  `uid` int NOT NULL,
  `admin_passw` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `company_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vk_group_id` int NOT NULL,
  `vk_group_url` varchar(96) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vk_confirmation_token` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `callback_server_id` int NOT NULL,
  `senler_secret` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `senler_gid_partnerka` int NOT NULL,
  `senler_gid_land` int NOT NULL,
  `fee_1` float NOT NULL,
  `fee_2` float NOT NULL,
  `fee_hello` int NOT NULL,
  `hold` int NOT NULL,
  `keep` int NOT NULL,
  `tg_bot_notif` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tg_bot_msg` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tg_bot_msg_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tg_bot_msg_off_income` tinyint NOT NULL,
  `land_txt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `thanks_txt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bot_first_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `land` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `land_txt_p` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `thanks_txt_p` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bot_first_msg_p` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `land_p` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `oferta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `agreement` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `oferta_referal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pixel_ya` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pixel_vk` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pixel_add` text COLLATE utf8mb4_general_ci NOT NULL,
  `test_uid` int NOT NULL,
  `bizon_api_token` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bizon_web_duration` int NOT NULL,
  `bizon_web_zachet_proc` int NOT NULL,
  `unisender_secret` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email_from` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email_from_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm_end` int NOT NULL,
  `partnerka_adlink` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pact_secret` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pact_company_id` int NOT NULL,
  `vsegpt_secret` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vsegpt_model` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vsegpt_delay_sec` int NOT NULL,
  `insales_shop_id` int NOT NULL,
  `insales_token` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `insales_shop` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `insales_status` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `insales_bonuses` tinyint NOT NULL,
  `insales_delay_fee` int NOT NULL,
  `api_secret` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `fl_cabinet2` tinyint NOT NULL,
  `del` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `0ctrl_tools`
--

CREATE TABLE `0ctrl_tools` (
  `id` int NOT NULL,
  `tool` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ctrl_id` int NOT NULL,
  `tool_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tool_val` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `0ctrl_vkt_send_tasks`
--

CREATE TABLE `0ctrl_vkt_send_tasks` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `ctrl_id` int NOT NULL,
  `vkt_send_id` int NOT NULL,
  `vkt_send_type` tinyint NOT NULL,
  `uid` int NOT NULL,
  `order_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `anketa`
--

CREATE TABLE `anketa` (
  `id` int NOT NULL,
  `email` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `qid` int NOT NULL,
  `tm` int NOT NULL,
  `answer` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `anketa_google`
--

CREATE TABLE `anketa_google` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `answ` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `anketa_questions`
--

CREATE TABLE `anketa_questions` (
  `id` int NOT NULL,
  `question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `comm` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `num` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fld_type` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `del` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `avangard`
--

CREATE TABLE `avangard` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `tm_pay` int NOT NULL,
  `pay_system` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sku` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `product_id` int NOT NULL,
  `order_id` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `order_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `order_descr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ticket` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `amount` int NOT NULL,
  `amount1` int NOT NULL,
  `c_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vk_uid` int NOT NULL,
  `res` int NOT NULL,
  `best2pay_id` int NOT NULL,
  `currency` int NOT NULL,
  `prodamus_id` int NOT NULL,
  `land_num` int NOT NULL,
  `tm_end` int NOT NULL,
  `gk_id` int NOT NULL,
  `gk_uid` int NOT NULL,
  `gk_cost_money` int NOT NULL,
  `gk_status` int NOT NULL,
  `fee_1` float DEFAULT '0',
  `fee_2` float DEFAULT '0',
  `comm` text COLLATE utf8mb4_general_ci NOT NULL,
  `promocode_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='avangard';

-- --------------------------------------------------------

--
-- Структура таблицы `avangard_s1`
--

CREATE TABLE `avangard_s1` (
  `id` int NOT NULL,
  `s` int NOT NULL,
  `pid` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `times` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `avangard_stock`
--

CREATE TABLE `avangard_stock` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `uid` int NOT NULL,
  `product_id` int NOT NULL,
  `amount` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ban`
--

CREATE TABLE `ban` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `bizon`
--

CREATE TABLE `bizon` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `dt` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ip` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `device` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dt_from` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dt_to` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `activity` int NOT NULL,
  `click_banner` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `click_button` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `city` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `region` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vk_uid` int NOT NULL,
  `vk_name` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `comm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `webinar_id` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `attempt` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `bizon_log`
--

CREATE TABLE `bizon_log` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `email` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm` int NOT NULL,
  `dt` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `blog`
--

CREATE TABLE `blog` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `topic_lat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `article` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `author` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `del` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cards`
--

CREATE TABLE `cards` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `uid_md5` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `uid_md5_n` tinyint NOT NULL,
  `acc_id` int NOT NULL,
  `man_id` int NOT NULL,
  `fl_newmsg` int NOT NULL,
  `tm_lastmsg` int NOT NULL,
  `tm` int NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `surname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `mob` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `city` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `insta` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `comm` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm_comm` int NOT NULL,
  `comm1` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `images` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `source_id` int NOT NULL,
  `source_vote` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `razdel` int NOT NULL,
  `tm_delay` int NOT NULL,
  `tm_delay_imp` int NOT NULL,
  `tm_schedule` int NOT NULL,
  `scdl_opt` int NOT NULL,
  `scdl_fl` int NOT NULL,
  `scdl_web_id` int NOT NULL,
  `fl` int NOT NULL,
  `user_id` int NOT NULL,
  `del` tinyint NOT NULL,
  `lock_tm` int NOT NULL,
  `lock_user_id` int NOT NULL,
  `dont_disp_in_new` tinyint NOT NULL,
  `fl_gpt` int NOT NULL,
  `birthday` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `age` int NOT NULL,
  `stage` int NOT NULL,
  `anketa` int NOT NULL,
  `tm_user_id` int NOT NULL,
  `mob_search` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `got_calls` tinyint NOT NULL,
  `utm_affiliate` int NOT NULL,
  `card_hold_tm` int NOT NULL,
  `card_keep` tinyint NOT NULL,
  `pact_conversation_id` int NOT NULL,
  `pact_insta_cid` int NOT NULL,
  `telegram_id` bigint NOT NULL,
  `telegram_nic` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vk_id` int NOT NULL,
  `tzoffset` int NOT NULL,
  `tm_first_time_opened` int NOT NULL,
  `wa_allowed` tinyint NOT NULL,
  `funnel_id` int NOT NULL,
  `lang` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `cnt_active` int NOT NULL,
  `tm_last_active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cards0ctrl`
--

CREATE TABLE `cards0ctrl` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `ctrl_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cards2other`
--

CREATE TABLE `cards2other` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `tool` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `tool_uid` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cards_add`
--

CREATE TABLE `cards_add` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `par` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `val` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `val_text` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cards_wa_name`
--

CREATE TABLE `cards_wa_name` (
  `id` int NOT NULL,
  `cid` int NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ava` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `uid` int NOT NULL,
  `price_num` int NOT NULL,
  `tm` int NOT NULL,
  `qnt` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `course_access`
--

CREATE TABLE `course_access` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `source_id` int NOT NULL,
  `tm1` int NOT NULL,
  `tm2` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `course_access_prolong`
--

CREATE TABLE `course_access_prolong` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `cnt` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `course_asanas`
--

CREATE TABLE `course_asanas` (
  `id` int NOT NULL,
  `asana` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fname` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `link` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fl_beg` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `course_log`
--

CREATE TABLE `course_log` (
  `id` int NOT NULL,
  `ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm` int NOT NULL,
  `uid` int NOT NULL,
  `referer` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `csrf`
--

CREATE TABLE `csrf` (
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `token_name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `tm` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `discount`
--

CREATE TABLE `discount` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `dt1` int NOT NULL,
  `dt2` int NOT NULL,
  `price` int NOT NULL,
  `product_id` int NOT NULL DEFAULT '1',
  `price_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `chanal_id` int NOT NULL,
  `amount` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `funnels`
--

CREATE TABLE `funnels` (
  `id` int NOT NULL,
  `funnel` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm` int NOT NULL,
  `uid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `head_control`
--

CREATE TABLE `head_control` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `user_id` int NOT NULL,
  `tm` int NOT NULL,
  `comm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `del` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `index_log`
--

CREATE TABLE `index_log` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `md5` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `client_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `client_phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `client_email` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pact_phone` bigint NOT NULL,
  `klid` int NOT NULL,
  `tzoffset` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `invoices`
--

CREATE TABLE `invoices` (
  `id` int NOT NULL,
  `tm` int NOT NULL DEFAULT '0',
  `num` int NOT NULL DEFAULT '0',
  `company` varchar(512) COLLATE utf8mb4_general_ci NOT NULL,
  `inn` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `uid` int NOT NULL DEFAULT '0',
  `product_id` int NOT NULL,
  `product_descr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qnt` int NOT NULL DEFAULT '0',
  `qnt_descr` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'шт',
  `price` int NOT NULL DEFAULT '0',
  `valuta` varchar(12) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'RUB',
  `nds` int NOT NULL DEFAULT '0',
  `comm` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `lands`
--

CREATE TABLE `lands` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `user_id` int NOT NULL,
  `land_num` int NOT NULL,
  `fl_not_disp_in_cab` int NOT NULL,
  `tm_scdl` int NOT NULL,
  `tm_scdl_period` int NOT NULL,
  `land_url` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `land_name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `land_txt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `thanks_txt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bot_first_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `land_razdel` int NOT NULL,
  `land_tag` int NOT NULL,
  `land_man_id` int NOT NULL,
  `fl_partner_land` tinyint NOT NULL,
  `fl_not_notify` tinyint NOT NULL,
  `fl_disp_phone` tinyint NOT NULL DEFAULT '1',
  `fl_disp_email` tinyint NOT NULL,
  `fl_disp_comm` tinyint NOT NULL,
  `label_disp_comm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fl_disp_phone_rq` int NOT NULL DEFAULT '1',
  `fl_disp_email_rq` int NOT NULL,
  `fl_disp_city` int NOT NULL,
  `fl_disp_city_rq` int NOT NULL,
  `product_id` int NOT NULL,
  `btn_label` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bizon_duration` int NOT NULL,
  `bizon_zachot` int NOT NULL,
  `land_type` tinyint NOT NULL,
  `del` tinyint NOT NULL,
  `fl_cashier` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `last_uid`
--

CREATE TABLE `last_uid` (
  `id` int NOT NULL,
  `uid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `leadgen_cost`
--

CREATE TABLE `leadgen_cost` (
  `id` int NOT NULL,
  `cost_per_lead` int NOT NULL,
  `tm` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `leadgen_leads`
--

CREATE TABLE `leadgen_leads` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `sale` tinyint NOT NULL,
  `promo_code` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `leadgen_log`
--

CREATE TABLE `leadgen_log` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `code` int NOT NULL,
  `klid` int NOT NULL,
  `utm_source` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_medium` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_campaign` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_content` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_term` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_ab` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fbp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fbc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fbclid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ip` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `referer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bc_get` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bc_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `res` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `leadgen_orders`
--

CREATE TABLE `leadgen_orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `tm` int NOT NULL,
  `amount` int NOT NULL,
  `sum_pay` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `links`
--

CREATE TABLE `links` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `uid1` int NOT NULL,
  `comm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `log_server`
--

CREATE TABLE `log_server` (
  `id` int NOT NULL,
  `tm` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `maillist`
--

CREATE TABLE `maillist` (
  `id` int NOT NULL,
  `tm_cr` int NOT NULL,
  `uid` int NOT NULL,
  `tm_send` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ma_acc`
--

CREATE TABLE `ma_acc` (
  `id` int NOT NULL,
  `acc_name` varchar(255) CHARACTER SET utf8mb3 NOT NULL,
  `del` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ma_cat`
--

CREATE TABLE `ma_cat` (
  `id` int NOT NULL,
  `cat_name` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `del` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ma_clients`
--

CREATE TABLE `ma_clients` (
  `id` int NOT NULL,
  `client` varchar(255) CHARACTER SET utf8mb3 NOT NULL,
  `del` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `ma_op`
--

CREATE TABLE `ma_op` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `tm_to` int NOT NULL,
  `cat_id` int NOT NULL,
  `client_id` int NOT NULL,
  `acc_id` int NOT NULL,
  `credit` int NOT NULL,
  `debit` int NOT NULL,
  `comm` text CHARACTER SET utf8mb3 NOT NULL,
  `user_id` int NOT NULL,
  `del` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `msgs`
--

CREATE TABLE `msgs` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `acc_id` int NOT NULL,
  `mid` int NOT NULL,
  `tm` int NOT NULL,
  `user_id` int NOT NULL,
  `msg` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `outg` tinyint NOT NULL,
  `imp` int NOT NULL,
  `new` int NOT NULL,
  `vote` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `source_id` int NOT NULL,
  `custom` int NOT NULL,
  `razdel_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `msgs_attachments`
--

CREATE TABLE `msgs_attachments` (
  `id` int NOT NULL,
  `msgs_id` int NOT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `msgs_hook`
--

CREATE TABLE `msgs_hook` (
  `tm` int NOT NULL,
  `uid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `msgs_templates`
--

CREATE TABLE `msgs_templates` (
  `id` int NOT NULL,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `msg` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `del` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `novofon_log`
--

CREATE TABLE `novofon_log` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `client_number` bigint NOT NULL,
  `man_number` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `talk_time_duration` int NOT NULL,
  `total_time_duration` int NOT NULL,
  `wait_time_duration` int NOT NULL,
  `call_session_id` int NOT NULL,
  `record` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `transcribe` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `gpt` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `val` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pact_state`
--

CREATE TABLE `pact_state` (
  `id` int NOT NULL,
  `channel_id` int NOT NULL,
  `tm` int NOT NULL,
  `state` tinyint NOT NULL,
  `src` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pact_test`
--

CREATE TABLE `pact_test` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `phone` bigint NOT NULL,
  `test_id` int NOT NULL,
  `res` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `partnerka_balance`
--

CREATE TABLE `partnerka_balance` (
  `id` int NOT NULL,
  `klid` int NOT NULL,
  `m` int NOT NULL,
  `y` int NOT NULL,
  `tm` int NOT NULL,
  `sum_p` int NOT NULL,
  `sum_r` int NOT NULL,
  `comm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `partnerka_op`
--

CREATE TABLE `partnerka_op` (
  `id` int NOT NULL,
  `klid_up` int NOT NULL,
  `klid` int NOT NULL,
  `avangard_id` int NOT NULL,
  `uid` int NOT NULL,
  `product_id` int NOT NULL,
  `amount` int NOT NULL,
  `fee` int NOT NULL,
  `fee_sum` int NOT NULL,
  `tm` int NOT NULL,
  `level` int NOT NULL,
  `comm` varchar(512) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `partnerka_pay`
--

CREATE TABLE `partnerka_pay` (
  `id` int NOT NULL,
  `klid` int NOT NULL,
  `tm` int NOT NULL,
  `sum_pay` int NOT NULL,
  `comm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vid` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `partnerka_spec`
--

CREATE TABLE `partnerka_spec` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `pid` int NOT NULL,
  `fee_1` float NOT NULL,
  `fee_2` float NOT NULL,
  `fee_cnt` int NOT NULL,
  `hold` int NOT NULL,
  `keep` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `partnerka_users`
--

CREATE TABLE `partnerka_users` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `klid` int NOT NULL,
  `email` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pwd` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fee` int NOT NULL,
  `fee2` int NOT NULL,
  `bank_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `typ` int NOT NULL,
  `levels` int NOT NULL,
  `del` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pay_systems`
--

CREATE TABLE `pay_systems` (
  `id` int NOT NULL,
  `unisender_template` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fl_disp_prodamus` int NOT NULL,
  `prodamus_secret` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prodamus_linktoform` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fl_disp_alfa` int NOT NULL,
  `alfa_secret` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alfa_url` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alfa_passw` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fl_disp_yookassa` int NOT NULL,
  `yookassa_passw` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `yookassa_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `robokassa_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `robokassa_passw_1` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `robokassa_passw_2` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fl_disp_robokassa` int NOT NULL,
  `lava_api_key` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `fl_disp_lava` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pixel`
--

CREATE TABLE `pixel` (
  `id` int NOT NULL,
  `pwd_id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `dt1` int NOT NULL,
  `tm1` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pixel_pages`
--

CREATE TABLE `pixel_pages` (
  `id` int NOT NULL,
  `pwd` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `weight` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int UNSIGNED NOT NULL,
  `dbase` varchar(255) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `user` varchar(255) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `query` text COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `col_name` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `col_type` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `col_length` text COLLATE utf8mb3_bin,
  `col_collation` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) COLLATE utf8mb3_bin DEFAULT '',
  `col_default` text COLLATE utf8mb3_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int UNSIGNED NOT NULL,
  `db_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `column_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `transformation_options` varchar(255) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `input_transformation` varchar(255) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) COLLATE utf8mb3_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `settings_data` text COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `export_type` varchar(10) COLLATE utf8mb3_bin NOT NULL,
  `template_name` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `template_data` text COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `tables` text COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `db` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `table` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sqlquery` text COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `item_name` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `item_type` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `db_name` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `page_nr` int UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `tables` text COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Recently accessed tables';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `master_table` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `master_field` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `foreign_db` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `foreign_table` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `foreign_field` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `db_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `search_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `search_data` text COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `pdf_page_number` int NOT NULL DEFAULT '0',
  `x` float UNSIGNED NOT NULL DEFAULT '0',
  `y` float UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `display_field` varchar(64) COLLATE utf8mb3_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `db_name` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `prefs` text COLLATE utf8mb3_bin NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `version` int UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text COLLATE utf8mb3_bin NOT NULL,
  `schema_sql` text COLLATE utf8mb3_bin,
  `data_sql` longtext COLLATE utf8mb3_bin,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') COLLATE utf8mb3_bin DEFAULT NULL,
  `tracking_active` int UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `config_data` text COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='User preferences storage for phpMyAdmin';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `tab` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `allowed` enum('Y','N') COLLATE utf8mb3_bin NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Структура таблицы `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) COLLATE utf8mb3_bin NOT NULL,
  `usergroup` varchar(64) COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Users and their assignments to user groups';

-- --------------------------------------------------------

--
-- Структура таблицы `ppl`
--

CREATE TABLE `ppl` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `uid` int NOT NULL,
  `step` int NOT NULL,
  `tm1` int NOT NULL,
  `done` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE `product` (
  `id_product` int NOT NULL,
  `id` int UNSIGNED NOT NULL,
  `sku` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price0` int NOT NULL,
  `price1` int NOT NULL,
  `price2` int NOT NULL,
  `descr` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descr_comm` text COLLATE utf8mb4_general_ci NOT NULL,
  `term` int NOT NULL,
  `source_id` int NOT NULL,
  `razdel` int NOT NULL,
  `tag_id` int NOT NULL,
  `installment` int NOT NULL,
  `fee_1` float NOT NULL,
  `fee_2` float NOT NULL,
  `fee_cnt` int NOT NULL,
  `discount` float NOT NULL,
  `hold` int NOT NULL,
  `keep` tinyint NOT NULL,
  `stock` int NOT NULL,
  `senler` int NOT NULL,
  `sp` int NOT NULL,
  `sp_template` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `in_use` int NOT NULL,
  `vid` int NOT NULL,
  `del` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `promo`
--

CREATE TABLE `promo` (
  `id` int NOT NULL,
  `video_id` int NOT NULL,
  `pic_id` int NOT NULL,
  `promo_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `promo_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `num` int NOT NULL,
  `del` tinyint NOT NULL,
  `tm_wall_lastsent` int NOT NULL,
  `vk_num` int NOT NULL,
  `youtube` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `promocodes`
--

CREATE TABLE `promocodes` (
  `id` int NOT NULL,
  `tm1` int NOT NULL,
  `tm2` int NOT NULL,
  `uid` int NOT NULL,
  `product_id` int NOT NULL,
  `discount` int NOT NULL,
  `price` int NOT NULL,
  `promocode` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fee_1` float NOT NULL,
  `fee_2` float NOT NULL,
  `cnt` int NOT NULL DEFAULT '-1',
  `fl_fix_partner` tinyint NOT NULL,
  `hold` int NOT NULL,
  `keep` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `promo_send`
--

CREATE TABLE `promo_send` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `stop` tinyint NOT NULL,
  `tm_reg` int NOT NULL,
  `tm` int NOT NULL,
  `cnt` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `promo_send_log`
--

CREATE TABLE `promo_send_log` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `promo_id` int NOT NULL,
  `res` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='promo_send_log';

-- --------------------------------------------------------

--
-- Структура таблицы `quiz`
--

CREATE TABLE `quiz` (
  `id` int NOT NULL,
  `num` int NOT NULL,
  `hdr` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `point` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `label` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `comm` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `quantity` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `uid` int NOT NULL,
  `visit_id` int NOT NULL,
  `tm` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `quiz_utm`
--

CREATE TABLE `quiz_utm` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `utm_term` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `razdel`
--

CREATE TABLE `razdel` (
  `id` int NOT NULL,
  `razdel_num` int NOT NULL,
  `razdel_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `razdel_color` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fl_not_send` tinyint NOT NULL,
  `del` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `refs`
--

CREATE TABLE `refs` (
  `id` int NOT NULL,
  `num` int NOT NULL,
  `num1` int NOT NULL,
  `pic` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `age` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ref_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ref_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ref_problem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `del` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `refs_new`
--

CREATE TABLE `refs_new` (
  `id` int NOT NULL,
  `dir` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `first_name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `last_name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `age` int NOT NULL,
  `brief` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `del` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `russian_names`
--

CREATE TABLE `russian_names` (
  `id` int NOT NULL,
  `name` varchar(22) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sex` int NOT NULL,
  `cnt` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `sales_script_items`
--

CREATE TABLE `sales_script_items` (
  `id` int NOT NULL,
  `sid` int NOT NULL,
  `num` int NOT NULL,
  `typ` int NOT NULL,
  `item` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `comm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `del` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `sales_script_names`
--

CREATE TABLE `sales_script_names` (
  `id` int NOT NULL,
  `sales_script_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `del` int NOT NULL,
  `fl_call_script` int NOT NULL,
  `fl_private` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `sender_log`
--

CREATE TABLE `sender_log` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `sp_template` int NOT NULL,
  `num_log` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `senler_log`
--

CREATE TABLE `senler_log` (
  `id` int NOT NULL,
  `typ` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `gid` int NOT NULL,
  `dt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm` int NOT NULL,
  `sid` int NOT NULL,
  `uid` int NOT NULL,
  `source` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `first` int NOT NULL,
  `full_unsubscribe` int NOT NULL,
  `in_promo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `senler_stat`
--

CREATE TABLE `senler_stat` (
  `id` int NOT NULL,
  `tag_id` int NOT NULL,
  `uid` int NOT NULL,
  `tag_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `short_links`
--

CREATE TABLE `short_links` (
  `id` int NOT NULL,
  `hash` varchar(10) NOT NULL,
  `params_json` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `clicks` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `sources`
--

CREATE TABLE `sources` (
  `id` int NOT NULL,
  `source_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `priority` int NOT NULL,
  `del` tinyint NOT NULL,
  `razdel_id` int NOT NULL,
  `fl_client` int NOT NULL,
  `use_in_cards` tinyint NOT NULL,
  `access_level` int NOT NULL,
  `hant_level` int NOT NULL,
  `for_touch` int NOT NULL,
  `source_comm` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fl_active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `stock`
--

CREATE TABLE `stock` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `amount` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tags`
--

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `tag_name` varchar(32) CHARACTER SET utf8mb3 NOT NULL,
  `tag_color` varchar(8) CHARACTER SET utf8mb3 NOT NULL,
  `fl_not_send` tinyint NOT NULL,
  `del` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tags_op`
--

CREATE TABLE `tags_op` (
  `id` int NOT NULL,
  `tag_id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `telegram`
--

CREATE TABLE `telegram` (
  `id` int NOT NULL,
  `code` int NOT NULL,
  `uid` int NOT NULL,
  `user_id` int NOT NULL,
  `tm` int NOT NULL,
  `confirmed` int NOT NULL,
  `ctrl_id` int NOT NULL,
  `ctrl_dir` varchar(32) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tg_channel`
--

CREATE TABLE `tg_channel` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `tg_id` int NOT NULL,
  `tg_nic` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `f_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `l_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `res` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tg_public_yoga`
--

CREATE TABLE `tg_public_yoga` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `tg_id` int NOT NULL,
  `tg_nic` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `f_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `l_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `res` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tmp_wa_send`
--

CREATE TABLE `tmp_wa_send` (
  `id` int NOT NULL,
  `mob` bigint NOT NULL,
  `done` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tz_info`
--

CREATE TABLE `tz_info` (
  `id` int NOT NULL,
  `address` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `postal_code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `country` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `federal_district` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `region_type` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `region` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `area_type` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `area` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `city_type` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `city` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `settlement_type` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `settlement` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `kladr_id` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `fias_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `fias_level` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `capital_marker` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `okato` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `oktmo` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tax_office` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `timezone` int DEFAULT NULL,
  `geo_lat` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `geo_lon` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `population` int DEFAULT NULL,
  `foundation_year` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `0ctrl` int NOT NULL,
  `username` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `real_user_name` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tg_nick` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `wa_user_name` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email_from_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `uid` int NOT NULL,
  `sip` int NOT NULL,
  `callback_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `acc_id` int NOT NULL,
  `klid` int NOT NULL,
  `gk_code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `passw` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `token` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `access_level` int NOT NULL,
  `tm_lastlogin` int NOT NULL,
  `comm` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `telegram_id` bigint NOT NULL,
  `pact_channel_id` int NOT NULL,
  `fl_notify_if_new` int NOT NULL,
  `fl_notify_if_other` int NOT NULL,
  `fl_notify_of_own_only` tinyint NOT NULL,
  `fl_allowlogin` int NOT NULL,
  `del` int NOT NULL,
  `pact_phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `garant` int NOT NULL,
  `pact_company_id` int NOT NULL,
  `pact_token` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pact_channel_online` int NOT NULL,
  `fb_pixel` bigint NOT NULL,
  `leadgen` int NOT NULL,
  `leadgen_rate` int NOT NULL,
  `leadgen_cnt` int NOT NULL,
  `leadgen_tm1` int NOT NULL,
  `leadgen_stop_global` int NOT NULL,
  `leadgen_stop_user_action` int NOT NULL,
  `fee` int NOT NULL,
  `fee2` int NOT NULL,
  `levels` int NOT NULL,
  `bank_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm_locked` int NOT NULL DEFAULT '0',
  `bc` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `direct_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sip_internal_number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users_billing`
--

CREATE TABLE `users_billing` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `tm` int NOT NULL,
  `vid` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `credit` int NOT NULL,
  `debit` int NOT NULL,
  `comm` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users_log`
--

CREATE TABLE `users_log` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempt_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `attempt_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_method` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'password',
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `status_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `device_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_created` tinyint(1) DEFAULT '0',
  `response_time_ms` int UNSIGNED DEFAULT NULL,
  `brute_force_flag` tinyint(1) DEFAULT '0',
  `redirect_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log of all user login attempts';

-- --------------------------------------------------------

--
-- Структура таблицы `users_notif`
--

CREATE TABLE `users_notif` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `fl_key` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `fl_val` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users_pbx`
--

CREATE TABLE `users_pbx` (
  `id` int NOT NULL,
  `man_number` int NOT NULL,
  `vsegpt_model` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `prompt` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `utm`
--

CREATE TABLE `utm` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `utm_source` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_medium` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_campaign` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_content` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_term` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `utm_ab` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pwd_id` int NOT NULL,
  `promo_code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `md5` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `mob` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist`
--

CREATE TABLE `vklist` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `vk_gid` int NOT NULL,
  `group_id` int NOT NULL,
  `tm_cr` int NOT NULL,
  `tm_msg` int NOT NULL,
  `tm_friends` int NOT NULL,
  `tm_wall` int NOT NULL,
  `res_msg` int NOT NULL,
  `res_friends` int NOT NULL,
  `res_wall` int NOT NULL,
  `blocked` int NOT NULL,
  `vote` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_acc`
--

CREATE TABLE `vklist_acc` (
  `id` int NOT NULL,
  `vk_uid` int NOT NULL,
  `login` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `passw` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email_passw` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm` int NOT NULL,
  `last_mid` int NOT NULL,
  `ban_cnt` int NOT NULL,
  `last_error` int NOT NULL,
  `last_error_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `token` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `comm` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm_next_send_msg` int NOT NULL,
  `fl_acc_allowed` int NOT NULL,
  `fl_acc_not_allowed_for_new` int NOT NULL,
  `fl_allow_read_from_all` int NOT NULL COMMENT 'even if not in cards or vklist',
  `del` tinyint NOT NULL,
  `fr_capcha_uid` int NOT NULL,
  `num` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_acc_log`
--

CREATE TABLE `vklist_acc_log` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `msg` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dt` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `uid` int NOT NULL,
  `acc_id` int NOT NULL,
  `user_id` int NOT NULL,
  `err` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_ctrl`
--

CREATE TABLE `vklist_ctrl` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `tm` int NOT NULL,
  `tm_finished` int NOT NULL,
  `cnt` int NOT NULL,
  `group_id` int NOT NULL,
  `stop` int NOT NULL,
  `busy` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_groups`
--

CREATE TABLE `vklist_groups` (
  `id` int NOT NULL,
  `url` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `group_name` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vote` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm` int NOT NULL,
  `msg` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fl_send_msg` tinyint NOT NULL,
  `fl_send_wall` tinyint NOT NULL,
  `fl_send_friends` tinyint NOT NULL,
  `fl_autosend` tinyint NOT NULL,
  `del` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_log`
--

CREATE TABLE `vklist_log` (
  `id` int NOT NULL,
  `dt` int NOT NULL,
  `tm` int NOT NULL,
  `acc_id` int NOT NULL,
  `group_id` int NOT NULL,
  `uid` int NOT NULL,
  `op` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `mode` tinyint NOT NULL,
  `err` int NOT NULL,
  `response` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ctrl_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_scan_groups`
--

CREATE TABLE `vklist_scan_groups` (
  `id` int NOT NULL,
  `gid` int NOT NULL,
  `uid` int NOT NULL,
  `sex` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `first_name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `last_name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `city_id` int NOT NULL,
  `country_id` int NOT NULL,
  `city` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `country` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bdate` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `age` tinyint NOT NULL,
  `tm` int NOT NULL,
  `d` tinyint NOT NULL,
  `m` tinyint NOT NULL,
  `y` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_scan_likes`
--

CREATE TABLE `vklist_scan_likes` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `item_id` int NOT NULL,
  `gid` int NOT NULL,
  `tm` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_scan_reposts`
--

CREATE TABLE `vklist_scan_reposts` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `item_id` int NOT NULL,
  `gid` int NOT NULL,
  `tm` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vklist_scan_votes`
--

CREATE TABLE `vklist_scan_votes` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `vote_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vktrade2vk`
--

CREATE TABLE `vktrade2vk` (
  `code` int NOT NULL,
  `vktrade_uid` int NOT NULL,
  `vk_uid` int NOT NULL,
  `tm` int NOT NULL,
  `promocode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vktrade_send_at_log`
--

CREATE TABLE `vktrade_send_at_log` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `tm` int NOT NULL,
  `tm1` int NOT NULL,
  `tm2` int NOT NULL,
  `source_id` int NOT NULL,
  `msg_md5` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fname` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `res_wa` tinyint NOT NULL,
  `res_email` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vktrade_send_at_msgs`
--

CREATE TABLE `vktrade_send_at_msgs` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm` int NOT NULL,
  `dt` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vktrade_send_unsubscribe`
--

CREATE TABLE `vktrade_send_unsubscribe` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `source_id` int NOT NULL,
  `messenger_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vkt_send_1`
--

CREATE TABLE `vkt_send_1` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `vkt_send_tm` int NOT NULL,
  `tm_shift` int NOT NULL,
  `land_num` int NOT NULL,
  `sid` int NOT NULL,
  `name_send` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email_template` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email_from` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email_from_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vk_attach` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tg_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tg_video` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tg_video_note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tg_audio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tm1` int NOT NULL,
  `tm2` int NOT NULL,
  `fl_clients` tinyint NOT NULL,
  `fl_partners` tinyint NOT NULL,
  `fl_leads` tinyint NOT NULL,
  `fl_tg` tinyint NOT NULL,
  `fl_vk` tinyint NOT NULL,
  `fl_email` int NOT NULL,
  `fl_razdel` int NOT NULL,
  `fl_land` int NOT NULL,
  `fl_tag` int NOT NULL,
  `fl_chk` int NOT NULL,
  `del` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vkt_send_log`
--

CREATE TABLE `vkt_send_log` (
  `id` int NOT NULL,
  `vkt_send_id` int NOT NULL,
  `uid` int NOT NULL,
  `tm_event` int NOT NULL,
  `tm` int NOT NULL,
  `tg_id` bigint NOT NULL,
  `vk_id` bigint NOT NULL,
  `wa_id` bigint NOT NULL,
  `email` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `res_email` tinyint NOT NULL,
  `res_vk` tinyint NOT NULL,
  `res_wa` tinyint NOT NULL,
  `res_tg` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vkt_send_log_1`
--

CREATE TABLE `vkt_send_log_1` (
  `id` int NOT NULL,
  `tmm` bigint DEFAULT NULL,
  `tg_id` int NOT NULL,
  `vk_id` int NOT NULL,
  `email` varchar(20) CHARACTER SET utf8mb3 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `vk_wall_comments`
--

CREATE TABLE `vk_wall_comments` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `vk_group_id` int NOT NULL,
  `vk_post_id` int NOT NULL,
  `vk_comment_id` int NOT NULL,
  `vk_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vk_uid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `webhook_log`
--

CREATE TABLE `webhook_log` (
  `id` int NOT NULL,
  `tm` int NOT NULL,
  `log_name` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `hook` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `yd_log`
--

CREATE TABLE `yd_log` (
  `id` int NOT NULL,
  `visit_id` int NOT NULL,
  `tm` int NOT NULL,
  `get_key` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `get_val` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `ctrl_id` (`ctrl_dir`),
  ADD KEY `insales` (`insales_shop_id`);

--
-- Индексы таблицы `0ctrl_tools`
--
ALTER TABLE `0ctrl_tools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tool` (`tool`),
  ADD KEY `tool_key` (`tool_key`),
  ADD KEY `ctrl_id` (`ctrl_id`) USING BTREE;

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
  ADD KEY `gk_status` (`gk_status`),
  ADD KEY `pay_system` (`pay_system`),
  ADD KEY `sku` (`sku`),
  ADD KEY `land_num` (`land_num`),
  ADD KEY `promocode` (`ticket`),
  ADD KEY `promocode_id` (`promocode_id`),
  ADD KEY `tm_pay` (`tm_pay`);

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
  ADD KEY `uid` (`uid`,`acc_id`,`fl_newmsg`,`tm_lastmsg`,`man_id`) USING BTREE,
  ADD KEY `schedule` (`tm_schedule`),
  ADD KEY `ind` (`tm`,`source_id`,`del`,`razdel`,`tm_delay`,`fl`) USING BTREE,
  ADD KEY `user_id` (`user_id`),
  ADD KEY `birthday` (`birthday`),
  ADD KEY `scdl_fl` (`scdl_fl`),
  ADD KEY `stage` (`stage`),
  ADD KEY `anketa` (`anketa`),
  ADD KEY `week0` (`tm_user_id`),
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
  ADD KEY `tm_last_active` (`tm_last_active`),
  ADD KEY `city_idx` (`city`),
  ADD KEY `tm_delay_imp` (`tm_delay_imp`);

--
-- Индексы таблицы `cards0ctrl`
--
ALTER TABLE `cards0ctrl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ctrl_id` (`ctrl_id`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `cards2other`
--
ALTER TABLE `cards2other`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tool` (`tool`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tool_2` (`tool`),
  ADD KEY `tool_uid` (`tool_uid`);

--
-- Индексы таблицы `cards_add`
--
ALTER TABLE `cards_add`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `par` (`par`),
  ADD KEY `val` (`val`);

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
-- Индексы таблицы `csrf`
--
ALTER TABLE `csrf`
  ADD PRIMARY KEY (`token`),
  ADD KEY `token_name` (`token_name`),
  ADD KEY `tm` (`tm`);

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
-- Индексы таблицы `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `num` (`num`),
  ADD KEY `inn` (`inn`),
  ADD KEY `uid` (`uid`);

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
  ADD KEY `tm` (`tm`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `tm_scdl_period` (`tm_scdl_period`),
  ADD KEY `fl_not_disp_in_cab` (`fl_not_disp_in_cab`),
  ADD KEY `idx_lands_fl_casher` (`fl_cashier`);

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
-- Индексы таблицы `ma_acc`
--
ALTER TABLE `ma_acc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`);

--
-- Индексы таблицы `ma_cat`
--
ALTER TABLE `ma_cat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_del` (`del`);

--
-- Индексы таблицы `ma_clients`
--
ALTER TABLE `ma_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`) USING BTREE;

--
-- Индексы таблицы `ma_op`
--
ALTER TABLE `ma_op`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `cat_id` (`cat_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `acc_id` (`acc_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `del` (`del`);

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
-- Индексы таблицы `novofon_log`
--
ALTER TABLE `novofon_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `client_number` (`client_number`),
  ADD KEY `man_number` (`man_number`),
  ADD KEY `call_session_id` (`call_session_id`);

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
-- Индексы таблицы `partnerka_spec`
--
ALTER TABLE `partnerka_spec`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `pid` (`pid`);

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
-- Индексы таблицы `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Индексы таблицы `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Индексы таблицы `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Индексы таблицы `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Индексы таблицы `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Индексы таблицы `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Индексы таблицы `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Индексы таблицы `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Индексы таблицы `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Индексы таблицы `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Индексы таблицы `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Индексы таблицы `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Индексы таблицы `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Индексы таблицы `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Индексы таблицы `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Индексы таблицы `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Индексы таблицы `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Индексы таблицы `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

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
-- Индексы таблицы `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `del` (`del`),
  ADD KEY `id` (`id`) USING BTREE;

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
  ADD KEY `promocode` (`promocode`),
  ADD KEY `fl_fix_partner` (`fl_fix_partner`);

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
  ADD KEY `del` (`del`),
  ADD KEY `razdel_num` (`razdel_num`);

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
-- Индексы таблицы `short_links`
--
ALTER TABLE `short_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash` (`hash`),
  ADD KEY `idx_hash` (`hash`),
  ADD KEY `idx_expires` (`expires_at`);

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
-- Индексы таблицы `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `del` (`del`);

--
-- Индексы таблицы `tags_op`
--
ALTER TABLE `tags_op`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tag_id` (`tag_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `user_id` (`user_id`);

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
  ADD KEY `levels` (`levels`),
  ADD KEY `sip_internal_number` (`sip_internal_number`),
  ADD KEY `idx_tm_locked` (`tm_locked`);

--
-- Индексы таблицы `users_billing`
--
ALTER TABLE `users_billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vid` (`vid`),
  ADD KEY `tm` (`tm`);

--
-- Индексы таблицы `users_log`
--
ALTER TABLE `users_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_attempt_at` (`attempt_at`),
  ADD KEY `idx_ip` (`attempt_ip`(15)),
  ADD KEY `idx_success_status` (`success`,`status_code`),
  ADD KEY `idx_brute_force` (`brute_force_flag`,`attempt_at`),
  ADD KEY `idx_session` (`session_id`(32));

--
-- Индексы таблицы `users_notif`
--
ALTER TABLE `users_notif`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users_pbx`
--
ALTER TABLE `users_pbx`
  ADD PRIMARY KEY (`id`),
  ADD KEY `man_number` (`man_number`) USING BTREE;

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
  ADD KEY `vkt_send_sid` (`sid`),
  ADD KEY `fl_tg` (`fl_tg`),
  ADD KEY `fl_email` (`fl_email`),
  ADD KEY `fl_vk` (`fl_vk`);

--
-- Индексы таблицы `vkt_send_log`
--
ALTER TABLE `vkt_send_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tm` (`tm`),
  ADD KEY `res_email` (`res_email`,`res_vk`,`res_wa`,`res_tg`),
  ADD KEY `vkt_send_id` (`vkt_send_id`),
  ADD KEY `tg_id` (`tg_id`),
  ADD KEY `vk_id` (`vk_id`),
  ADD KEY `wa_id` (`wa_id`),
  ADD KEY `email` (`email`),
  ADD KEY `tm_event` (`tm_event`);

--
-- Индексы таблицы `vkt_send_log_1`
--
ALTER TABLE `vkt_send_log_1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tg_id` (`tg_id`),
  ADD KEY `vk_id` (`vk_id`),
  ADD KEY `email` (`email`);

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
-- Индексы таблицы `webhook_log`
--
ALTER TABLE `webhook_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tm` (`tm`),
  ADD KEY `log_name` (`log_name`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `0ctrl_tools`
--
ALTER TABLE `0ctrl_tools`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `0ctrl_vkt_send_tasks`
--
ALTER TABLE `0ctrl_vkt_send_tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `anketa`
--
ALTER TABLE `anketa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `anketa_google`
--
ALTER TABLE `anketa_google`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `anketa_questions`
--
ALTER TABLE `anketa_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `avangard`
--
ALTER TABLE `avangard`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `avangard_s1`
--
ALTER TABLE `avangard_s1`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `avangard_stock`
--
ALTER TABLE `avangard_stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ban`
--
ALTER TABLE `ban`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `bizon`
--
ALTER TABLE `bizon`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `bizon_log`
--
ALTER TABLE `bizon_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cards0ctrl`
--
ALTER TABLE `cards0ctrl`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cards2other`
--
ALTER TABLE `cards2other`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cards_add`
--
ALTER TABLE `cards_add`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cards_wa_name`
--
ALTER TABLE `cards_wa_name`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `course_access`
--
ALTER TABLE `course_access`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `course_access_prolong`
--
ALTER TABLE `course_access_prolong`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `course_asanas`
--
ALTER TABLE `course_asanas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `course_log`
--
ALTER TABLE `course_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `discount`
--
ALTER TABLE `discount`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `funnels`
--
ALTER TABLE `funnels`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `head_control`
--
ALTER TABLE `head_control`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `index_log`
--
ALTER TABLE `index_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `lands`
--
ALTER TABLE `lands`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `leadgen_cost`
--
ALTER TABLE `leadgen_cost`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `leadgen_leads`
--
ALTER TABLE `leadgen_leads`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `leadgen_log`
--
ALTER TABLE `leadgen_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `leadgen_orders`
--
ALTER TABLE `leadgen_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `links`
--
ALTER TABLE `links`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `log_server`
--
ALTER TABLE `log_server`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `maillist`
--
ALTER TABLE `maillist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ma_acc`
--
ALTER TABLE `ma_acc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ma_cat`
--
ALTER TABLE `ma_cat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ma_clients`
--
ALTER TABLE `ma_clients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ma_op`
--
ALTER TABLE `ma_op`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `msgs`
--
ALTER TABLE `msgs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `msgs_attachments`
--
ALTER TABLE `msgs_attachments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `msgs_templates`
--
ALTER TABLE `msgs_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `novofon_log`
--
ALTER TABLE `novofon_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pact_state`
--
ALTER TABLE `pact_state`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pact_test`
--
ALTER TABLE `pact_test`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `partnerka_balance`
--
ALTER TABLE `partnerka_balance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `partnerka_op`
--
ALTER TABLE `partnerka_op`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `partnerka_pay`
--
ALTER TABLE `partnerka_pay`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `partnerka_spec`
--
ALTER TABLE `partnerka_spec`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `partnerka_users`
--
ALTER TABLE `partnerka_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pay_systems`
--
ALTER TABLE `pay_systems`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pixel`
--
ALTER TABLE `pixel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pixel_pages`
--
ALTER TABLE `pixel_pages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ppl`
--
ALTER TABLE `ppl`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `product`
--
ALTER TABLE `product`
  MODIFY `id_product` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `promocodes`
--
ALTER TABLE `promocodes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `promo_send`
--
ALTER TABLE `promo_send`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `promo_send_log`
--
ALTER TABLE `promo_send_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `quiz_utm`
--
ALTER TABLE `quiz_utm`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `razdel`
--
ALTER TABLE `razdel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `refs`
--
ALTER TABLE `refs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `refs_new`
--
ALTER TABLE `refs_new`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `russian_names`
--
ALTER TABLE `russian_names`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sales_script_items`
--
ALTER TABLE `sales_script_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sales_script_names`
--
ALTER TABLE `sales_script_names`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sender_log`
--
ALTER TABLE `sender_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `senler_log`
--
ALTER TABLE `senler_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `senler_stat`
--
ALTER TABLE `senler_stat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `short_links`
--
ALTER TABLE `short_links`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tags_op`
--
ALTER TABLE `tags_op`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `telegram`
--
ALTER TABLE `telegram`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tg_channel`
--
ALTER TABLE `tg_channel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tg_public_yoga`
--
ALTER TABLE `tg_public_yoga`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tmp_wa_send`
--
ALTER TABLE `tmp_wa_send`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tz_info`
--
ALTER TABLE `tz_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users_billing`
--
ALTER TABLE `users_billing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users_log`
--
ALTER TABLE `users_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users_notif`
--
ALTER TABLE `users_notif`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users_pbx`
--
ALTER TABLE `users_pbx`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `utm`
--
ALTER TABLE `utm`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist`
--
ALTER TABLE `vklist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist_acc`
--
ALTER TABLE `vklist_acc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist_acc_log`
--
ALTER TABLE `vklist_acc_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist_ctrl`
--
ALTER TABLE `vklist_ctrl`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist_groups`
--
ALTER TABLE `vklist_groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist_log`
--
ALTER TABLE `vklist_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist_scan_groups`
--
ALTER TABLE `vklist_scan_groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist_scan_likes`
--
ALTER TABLE `vklist_scan_likes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist_scan_reposts`
--
ALTER TABLE `vklist_scan_reposts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vklist_scan_votes`
--
ALTER TABLE `vklist_scan_votes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vktrade2vk`
--
ALTER TABLE `vktrade2vk`
  MODIFY `code` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vktrade_send_at_log`
--
ALTER TABLE `vktrade_send_at_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vktrade_send_at_msgs`
--
ALTER TABLE `vktrade_send_at_msgs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vktrade_send_unsubscribe`
--
ALTER TABLE `vktrade_send_unsubscribe`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vkt_send_1`
--
ALTER TABLE `vkt_send_1`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vkt_send_log`
--
ALTER TABLE `vkt_send_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vkt_send_log_1`
--
ALTER TABLE `vkt_send_log_1`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vk_wall_comments`
--
ALTER TABLE `vk_wall_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `webhook_log`
--
ALTER TABLE `webhook_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `yd_log`
--
ALTER TABLE `yd_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
