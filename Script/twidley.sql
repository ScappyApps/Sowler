-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 14-Nov-2018 às 14:57
-- Versão do servidor: 10.1.34-MariaDB
-- PHP Version: 7.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `twidley`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_comments`
--

CREATE TABLE `so_comments` (
  `comment_id` int(20) NOT NULL,
  `post_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `postText` text,
  `postFile` varchar(180) DEFAULT NULL,
  `postImage` varchar(180) DEFAULT NULL,
  `postVideo` varchar(180) DEFAULT NULL,
  `postGif` varchar(180) DEFAULT NULL,
  `postSticker` varchar(180) DEFAULT NULL,
  `postYoutube` varchar(150) DEFAULT NULL,
  `time` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `hour` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_comments_like`
--

CREATE TABLE `so_comments_like` (
  `id` int(20) NOT NULL,
  `post_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `comment_id` int(20) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_config`
--

CREATE TABLE `so_config` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `so_config`
--

INSERT INTO `so_config` (`id`, `name`, `value`) VALUES
(1, 'site_name', 'Twidley'),
(2, 'theme', 'olympus'),
(3, 'maintenance_mode', '0'),
(4, 'language', 'en-US'),
(5, 'smtp_host', ''),
(6, 'smtp_email', ''),
(7, 'smtp_password', ''),
(8, 'google_map_api', 'AIzaSyBOfpaMO_tMMsuvS2T4zx4llbtsFqMuT9Y'),
(9, 'giphy_api', '4fwnD2DNjILnxLWvgkqPamI7ceP8VOUB'),
(10, 'yandex_api', 'trnsl.1.1.20180713T194926Z.f1af20bec26c804d.57873b89711db04b2ae53054b5d05adbaeec18db'),
(12, 'version', '2.0.1'),
(14, 'app_token', ''),
(15, 'amazon_s3', '0'),
(16, 'amazon_s3_key', '0'),
(17, 'amazon_s3_s_key', '0'),
(18, 'bucket_amazon_name', '0'),
(19, 'region', 'us-east-1'),
(20, 'message_alert', ''),
(21, 'live_stream', '0'),
(22, 'live_stream_all_users', '0'),
(23, 'adsense', '0'),
(24, 'client_id', ''),
(25, 'secret', ''),
(26, 'color_box', '1'),
(27, 'type_button', 'reactions'),
(28, 'floopz_music', '1'),
(29, 'moments', '1'),
(30, 'search_background', '#00b3a2'),
(31, 'border_search_background', '#00a797'),
(32, 'header_background', '#00a796'),
(33, 'header_background_two', '#009688'),
(34, 'cover_background', '#02d0bb'),
(35, 'border_button', '#009688'),
(36, 'color_text_button', '#009688'),
(37, 'color_link', '#009688'),
(38, 'color_total_notifications', '#009688'),
(39, 'color_border', '#009688'),
(40, 'websocket', '0'),
(39, 'site_port', '3000');

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_emoticons`
--

CREATE TABLE `so_emoticons` (
  `id` int(11) NOT NULL,
  `key` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `so_emoticons`
--

INSERT INTO `so_emoticons` (`id`, `key`) VALUES
(1, '1f600'),
(2, '263a'),
(3, '1f970'),
(4, '1f601');

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_followers`
--

CREATE TABLE `so_followers` (
  `id` int(20) NOT NULL,
  `from_id` int(20) NOT NULL,
  `to_id` int(20) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `privacy` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_hashtags`
--

CREATE TABLE `so_hashtags` (
  `id` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL DEFAULT '',
  `tag` varchar(255) NOT NULL DEFAULT '',
  `last_trend_time` int(11) NOT NULL DEFAULT '0',
  `trend_use_num` int(11) NOT NULL DEFAULT '0',
  `expire` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_like`
--

CREATE TABLE `so_like` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_messages`
--

CREATE TABLE `so_messages` (
  `id` int(20) NOT NULL,
  `from_id` int(20) NOT NULL,
  `to_id` int(20) NOT NULL,
  `postText` text,
  `postFile` varchar(180) DEFAULT NULL,
  `postImage` varchar(180) DEFAULT NULL,
  `postVideo` varchar(180) DEFAULT NULL,
  `postGif` varchar(180) DEFAULT NULL,
  `postSticker` varchar(180) DEFAULT NULL,
  `postYoutube` varchar(150) DEFAULT NULL,
  `time` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `hour` varchar(10) NOT NULL,
  `seen` int(1) NOT NULL DEFAULT '0',
  `deleted_one` int(1) NOT NULL,
  `deleted_two` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_notifications`
--

CREATE TABLE `so_notifications` (
  `id` int(20) NOT NULL,
  `comment_id` int(20) NOT NULL,
  `post_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `to_id` int(20) DEFAULT NULL,
  `type` varchar(120) NOT NULL,
  `time` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `seen` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_open_chats`
--

CREATE TABLE `so_open_chats` (
  `id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_posts`
--

CREATE TABLE `so_posts` (
  `post_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `postText` text,
  `postFile` varchar(180) DEFAULT NULL,
  `postImage` varchar(180) DEFAULT NULL,
  `postVideo` varchar(180) DEFAULT NULL,
  `postGif` varchar(180) DEFAULT NULL,
  `postSticker` varchar(180) DEFAULT NULL,
  `postType` varchar(35) DEFAULT NULL,
  `postYoutube` varchar(150) DEFAULT NULL,
  `time` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `hour` varchar(10) NOT NULL,
  `share_id` int(30) NOT NULL,
  `amazon` int(1) NOT NULL,
  `html_title` varchar(180) DEFAULT NULL,
  `html_image` varchar(180) DEFAULT NULL,
  `html_body` text,
  `html_url` varchar(180) DEFAULT NULL,
  `background` varchar(180) DEFAULT NULL,
  `titleMoment` varchar(180) DEFAULT NULL,
  `categoryMoment` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_reactions`
--

CREATE TABLE `so_reactions` (
  `id` int(20) NOT NULL,
  `from_id` int(20) NOT NULL,
  `to_id` int(20) NOT NULL,
  `post_id` int(20) NOT NULL,
  `comment_id` int(20) NOT NULL,
  `type` varchar(25) NOT NULL DEFAULT 'reaction_like'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_stickers`
--

CREATE TABLE `so_stickers` (
  `id` int(20) NOT NULL,
  `sticker_id` int(20) NOT NULL,
  `image` varchar(180) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `so_stickers`
--

INSERT INTO `so_stickers` (`id`, `sticker_id`, `image`) VALUES
(2, 2, 'upload/stickers/Meep/1322665222_13_b29824d15b945e7d8287108108271933.png'),
(3, 2, 'upload/stickers/Meep/586484157_13_318dea1d08766dffed30af8f1221967b.png'),
(4, 2, 'upload/stickers/Meep/1868840101_13_4d88bf9fdc530a09ff231ae8e6bc8183.png'),
(5, 2, 'upload/stickers/Meep/952055133_13_60d2d8f4c7860a4604c027ab8259f326.png'),
(6, 2, 'upload/stickers/Meep/196758538_13_dcffbe7eb57abc0036a10ec1ebc8e063.png'),
(7, 2, 'upload/stickers/Meep/1569651652_13_48f6a086683f1942459193e41464ec2e.png'),
(8, 2, 'upload/stickers/Meep/39902901_13_3f041ad4e19c74007609f7c2277efdfd.png'),
(9, 2, 'upload/stickers/Meep/1077261031_13_d05ef2283fa3d5daeb208c5a4522036f.png'),
(10, 2, 'upload/stickers/Meep/1559817737_13_36d247b54c8cf773304644a276fdad40.png'),
(11, 2, 'upload/stickers/Meep/149957007_13_b2db84c87330fe8ffd1015b9258eca8a.png'),
(12, 2, 'upload/stickers/Meep/1752734396_13_801e1d8de4f23b641e3c216c50dc031a.png'),
(13, 2, 'upload/stickers/Meep/1361805912_13_d1ee711a8492281739fda1ba3973285c.png'),
(14, 2, 'upload/stickers/Meep/740023459_13_dfd6d5df2da3e4fb7ec0b8239b0cd206.png'),
(15, 2, 'upload/stickers/Meep/26601173_13_3767ce721ef453269e8cb971c1554999.png'),
(16, 2, 'upload/stickers/Meep/470575373_13_22d58c028c74cc22e7d0d1cbf77a4c49.png'),
(17, 2, 'upload/stickers/Meep/1554185141_13_dde27f2c3b478a109c4d7194700fd9f0.png'),
(18, 2, 'upload/stickers/Meep/30394001_13_8e20ab44bd46c76139bdb44094ee235a.png'),
(19, 2, 'upload/stickers/Meep/430771469_13_c6ca1908287574796f5a6cad015d2050.png'),
(20, 2, 'upload/stickers/Meep/40784468_13_c1588f80d568fdb33f5e2fecb8f167e1.png'),
(21, 2, 'upload/stickers/Meep/2086329646_13_a4a69fe7346cd658c0b2e798f4ec74e9.png'),
(22, 2, 'upload/stickers/Meep/1714519003_13_862768f5a51388c2e938d6d061fbbe38.png'),
(23, 2, 'upload/stickers/Meep/1921662295_13_6e7b952e1969e4b14a7d413a464b98ee.png'),
(24, 2, 'upload/stickers/Meep/405940528_13_805232e2b438d8bc3b3ada7001f5786d.png'),
(25, 2, 'upload/stickers/Meep/1997258228_13_c27f1902c011c1336a8eb1aebaa76972.png'),
(26, 2, 'upload/stickers/Meep/831209899_13_7d98eafca6fa25a446e368c37534ad47.png'),
(27, 2, 'upload/stickers/Meep/161287325_13_5be257e83037a184b2b63d5e9c7cf055.png'),
(28, 2, 'upload/stickers/Meep/298075945_13_48745a6c9de35d2604f28a784efa9c37.png'),
(29, 2, 'upload/stickers/Meep/51333695_13_d5500a65fe21549a9a859cde069a71d2.png'),
(30, 2, 'upload/stickers/Meep/956900276_13_e7d63279276a9dfdd419da5101f607e9.png'),
(31, 2, 'upload/stickers/Meep/1863233275_13_8a4f46d30bb20de5e3d103aa098f55f4.png'),
(32, 2, 'upload/stickers/Meep/1172758260_13_14eb2e3349165de85f464450e591f0b3.png'),
(33, 2, 'upload/stickers/Meep/833684770_13_1342fb652eb94a73ddf3049f031a6f0c.png'),
(34, 3, 'upload/stickers/TontonFriendsToo/466357797_14_9365b5c800de2f726533e72ee2e4d2bf.png'),
(35, 3, 'upload/stickers/TontonFriendsToo/1560616177_14_a13deafcd83cfb221db5c247becf2f24.png'),
(36, 3, 'upload/stickers/TontonFriendsToo/1337551180_14_6d43197e76f3c42d762d7d73763b34d8.png'),
(37, 3, 'upload/stickers/TontonFriendsToo/484501143_14_57c8ff0370ec3c6de3e7910e95e109eb.png'),
(38, 3, 'upload/stickers/TontonFriendsToo/406664866_14_e795fc965f0548e59e4aa4308a4422a1.png'),
(39, 3, 'upload/stickers/TontonFriendsToo/1466917631_14_42f0b99d2a030a1bb3731eb8402a7bc4.png'),
(40, 3, 'upload/stickers/TontonFriendsToo/1770869003_14_7fc06502649a14463b424e1a904db0bc.png'),
(41, 3, 'upload/stickers/TontonFriendsToo/879527121_14_6cbcf87cfde7d7e8d4132fcc54e28280.png'),
(42, 3, 'upload/stickers/TontonFriendsToo/1258869158_14_65a9304908b4c711bc19f418d82d67e3.png'),
(43, 3, 'upload/stickers/TontonFriendsToo/152793202_14_d599a2fffc4f930f8ec0a669d60ba634.png'),
(44, 3, 'upload/stickers/TontonFriendsToo/1969879844_14_88bbf4b11705cd151fba977badffccd9.png'),
(45, 3, 'upload/stickers/TontonFriendsToo/2030762461_14_10b4d4f44e1c185331fc55f58e5b1810.png'),
(46, 3, 'upload/stickers/TontonFriendsToo/1617150548_14_cf33de86247bebbafd4a24d32b080ea5.png'),
(47, 3, 'upload/stickers/TontonFriendsToo/1086581306_14_6c65041926ea1c1ffead2615d89c35b0.png'),
(48, 3, 'upload/stickers/TontonFriendsToo/264615720_14_7ba3152cb60308928080df95f81e77a5.png'),
(49, 3, 'upload/stickers/TontonFriendsToo/850145785_14_1f0d3b6f3d280a1994e32f5c9e0ade1d.png'),
(50, 3, 'upload/stickers/TontonFriendsToo/1200260758_14_771f00ff35a560605ed1c92bf77cf9ad.png'),
(51, 3, 'upload/stickers/TontonFriendsToo/480222251_14_be42deacde052985d45049890ceafd30.png'),
(52, 3, 'upload/stickers/TontonFriendsToo/1214193499_14_4a4a03484a27eaaf24a017399d676436.png'),
(53, 3, 'upload/stickers/TontonFriendsToo/12171087_14_c7da0667c0dfbc20a2b939463a67cb11.png'),
(54, 3, 'upload/stickers/TontonFriendsToo/503156655_14_b52d1e10b6ea0aa3c4e070e2fd4e6acb.png'),
(55, 3, 'upload/stickers/TontonFriendsToo/1451451430_14_929315fd6d385e5af3341b929fbcb561.png'),
(56, 3, 'upload/stickers/TontonFriendsToo/487825433_14_24390d58aadb36796513d3298f0b5913.png'),
(57, 3, 'upload/stickers/TontonFriendsToo/1665180135_14_f9e809b8f5b12cb7be7edf1374a210dc.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_stickers_store`
--

CREATE TABLE `so_stickers_store` (
  `id` int(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `image` varchar(180) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `so_stickers_store`
--

INSERT INTO `so_stickers_store` (`id`, `name`, `image`) VALUES
(2, 'Meep', 'upload/stickers/697284939_13_559e8cfd7b90727e92b775746f6b5e6c.png'),
(3, 'Tonton Friends Too', 'upload/stickers/1364604229_14_f7f6adcac0711a87b7a861147812eea8_sticker.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_users`
--

CREATE TABLE `so_users` (
  `user_id` int(20) NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(160) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `avatar` varchar(180) NOT NULL DEFAULT 'upload/images/d-avatar.png',
  `cover` varchar(180) NOT NULL,
  `about` text,
  `language` varchar(10) NOT NULL DEFAULT 'en-US',
  `gender` varchar(11) NOT NULL DEFAULT 'male',
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `registered_day` int(2) NOT NULL,
  `registered_month` int(2) NOT NULL,
  `registered_year` int(4) NOT NULL,
  `lastseen` int(11) NOT NULL,
  `auto_message` text,
  `ip_address` varchar(30) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `token_active` varchar(160) DEFAULT NULL,
  `privacy` int(1) NOT NULL DEFAULT '1',
  `country` varchar(20) NOT NULL DEFAULT 'Brazil',
  `admin` int(1) NOT NULL DEFAULT '0',
  `verified` int(1) NOT NULL DEFAULT '0',
  `amazon_avatar` int(1) NOT NULL,
  `amazon_cover` int(1) NOT NULL,
  `connected_via` varchar(32) NOT NULL DEFAULT 'pc',
  `secret_key` varchar(80) DEFAULT NULL,
  `all_posts` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_user_stickers`
--

CREATE TABLE `so_user_stickers` (
  `id` int(20) NOT NULL,
  `sticker_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `so_comments`
--
ALTER TABLE `so_comments`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `so_comments_like`
--
ALTER TABLE `so_comments_like`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_config`
--
ALTER TABLE `so_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_emoticons`
--
ALTER TABLE `so_emoticons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_followers`
--
ALTER TABLE `so_followers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_hashtags`
--
ALTER TABLE `so_hashtags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_trend_time` (`last_trend_time`),
  ADD KEY `trend_use_num` (`trend_use_num`),
  ADD KEY `tag` (`tag`),
  ADD KEY `expire` (`expire`);

--
-- Indexes for table `so_like`
--
ALTER TABLE `so_like`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_messages`
--
ALTER TABLE `so_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_notifications`
--
ALTER TABLE `so_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_open_chats`
--
ALTER TABLE `so_open_chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_posts`
--
ALTER TABLE `so_posts`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `so_reactions`
--
ALTER TABLE `so_reactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_stickers`
--
ALTER TABLE `so_stickers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_stickers_store`
--
ALTER TABLE `so_stickers_store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_users`
--
ALTER TABLE `so_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `so_user_stickers`
--
ALTER TABLE `so_user_stickers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `so_comments`
--
ALTER TABLE `so_comments`
  MODIFY `comment_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_comments_like`
--
ALTER TABLE `so_comments_like`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_config`
--
ALTER TABLE `so_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `so_emoticons`
--
ALTER TABLE `so_emoticons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `so_followers`
--
ALTER TABLE `so_followers`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_hashtags`
--
ALTER TABLE `so_hashtags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_like`
--
ALTER TABLE `so_like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_messages`
--
ALTER TABLE `so_messages`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_notifications`
--
ALTER TABLE `so_notifications`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_open_chats`
--
ALTER TABLE `so_open_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_posts`
--
ALTER TABLE `so_posts`
  MODIFY `post_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_reactions`
--
ALTER TABLE `so_reactions`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_stickers`
--
ALTER TABLE `so_stickers`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `so_stickers_store`
--
ALTER TABLE `so_stickers_store`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `so_users`
--
ALTER TABLE `so_users`
  MODIFY `user_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `so_user_stickers`
--
ALTER TABLE `so_user_stickers`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
