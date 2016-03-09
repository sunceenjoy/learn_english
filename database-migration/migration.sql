-- phpMyAdmin SQL Dump
-- version 4.3.10
-- http://www.phpmyadmin.net
--
-- Host: mysqlconnect.mmyyabb.com
-- Generation Time: Feb 11, 2016 at 10:18 AM
-- Server version: 5.6.23-log
-- PHP Version: 5.5.9-1ubuntu4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `english_local`
--

-- --------------------------------------------------------

--
-- Table structure for table `english_migration_versions`
--

CREATE TABLE IF NOT EXISTS `english_migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `english_migration_versions`
--

INSERT INTO `english_migration_versions` (`version`) VALUES
('20160211100014');

-- --------------------------------------------------------

--
-- Table structure for table `learning_history`
--

CREATE TABLE IF NOT EXISTS `learning_history` (
  `id` int(11) NOT NULL,
  `type` char(10) NOT NULL,
  `status` int(11) NOT NULL,
  `success` int(11) NOT NULL DEFAULT '0',
  `fail` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `learning_history`
--

INSERT INTO `learning_history` (`id`, `type`, `status`, `success`, `fail`, `date`) VALUES
(1, 'words', 1, 3, 0, '2016-01-13'),
(2, 'words', 2, 1, 0, '2016-01-12'),
(3, 'phrases', 3, 2, 2, '2016-01-13'),
(4, 'words', 2, 1, 0, '2016-01-14');

-- --------------------------------------------------------

--
-- Table structure for table `phrases`
--

CREATE TABLE IF NOT EXISTS `phrases` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `means` varchar(1024) NOT NULL,
  `pronunciation` char(10) NOT NULL,
  `voice` varchar(1024) NOT NULL,
  `success` tinyint(4) NOT NULL DEFAULT '0',
  `failure` tinyint(4) NOT NULL DEFAULT '0',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL COMMENT '0新 1简单 2中等 3困难 4生僻',
  `create_time` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `phrases`
--

INSERT INTO `phrases` (`id`, `name`, `means`, `pronunciation`, `voice`, `success`, `failure`, `update_time`, `status`, `create_time`) VALUES
(1, 'how are you   ', '你好', '', 'default/naturalreaders/how-are-you.mp3', 0, 0, '2015-12-17 14:22:04', 2, '2015-11-24 14:46:44'),
(2, 'I''m a good boy ', '', '', 'default/I''m-a-good-boy.mp3', 0, 0, '2015-12-02 14:51:05', 0, '2015-11-24 14:46:46'),
(3, 'thank you ', '谢谢', '', 'default/thank-you.mp3', 0, 0, '2015-12-17 14:23:05', 2, '2015-11-24 14:46:48'),
(4, 'nice work', '', '', 'default/nice-work.mp3', 1, 0, '2016-01-13 16:55:32', 3, '2015-12-02 13:32:54'),
(5, 'It''s been a long road. ', '这个历经坎坷，不简单的过程的意思', '', 'default/naturalreaders/It''s-been-a-long-road..mp3', 0, 1, '2016-01-13 16:55:45', 3, '2016-01-13 09:53:35'),
(6, 'pilot test  ', '小规模试验；初步试验', '', 'default/naturalreaders/pilot-test.mp3', 0, 1, '2016-01-13 16:55:42', 3, '2016-01-13 09:54:21'),
(7, 'high end  ', '高端', '', 'default/naturalreaders/high-end.mp3', 1, 0, '2016-01-13 16:55:33', 3, '2016-01-13 10:00:27');

-- --------------------------------------------------------

--
-- Table structure for table `phrases_marker`
--

CREATE TABLE IF NOT EXISTS `phrases_marker` (
  `status` int(11) NOT NULL,
  `page` int(11) NOT NULL,
  `word` char(20) NOT NULL,
  `success` int(11) NOT NULL DEFAULT '0',
  `failure` int(11) NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `phrases_marker`
--

INSERT INTO `phrases_marker` (`status`, `page`, `word`, `success`, `failure`, `date_time`) VALUES
(0, 6, 'generation', 0, 2, '2015-01-27 11:27:32'),
(1, 13, 'sandwich', 3, 2, '2015-01-24 01:06:10'),
(2, 1, 'trigger', 3, 2, '2015-01-24 01:11:11'),
(3, 0, '', 3, 2, '0000-00-00 00:00:00'),
(4, 6, 'contractor', 3, 2, '2015-02-03 00:24:24');

-- --------------------------------------------------------

--
-- Table structure for table `pronunciation`
--

CREATE TABLE IF NOT EXISTS `pronunciation` (
  `id` bigint(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `means` varchar(100) NOT NULL,
  `voice` varchar(210) NOT NULL,
  `pronunciation` varchar(20) NOT NULL DEFAULT '',
  `success` int(11) NOT NULL DEFAULT '0',
  `failure` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL COMMENT '0 新词 1简单 2 中等 3 复杂  4生僻',
  `create_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pronunciation`
--

INSERT INTO `pronunciation` (`id`, `name`, `means`, `voice`, `pronunciation`, `success`, `failure`, `status`, `create_time`, `update_time`) VALUES
(1, 'good morning, every body.', 'NONE', 'default/good-morning,-every-body..mp3', 'NONE', 0, 0, 1, '2015-11-20 15:20:07', '2015-11-24 15:59:03'),
(2, 'love me', 'NONE', 'default/naturalreaders/love-me.mp3', 'NONE', 0, 0, 0, '2015-11-20 15:16:00', '2015-12-02 15:35:04'),
(3, 'good one', 'NONE', 'default/good-one.mp3', 'NONE', 0, 0, 0, '2015-11-24 11:58:41', '2015-11-24 15:59:03'),
(4, 'that''s nice', 'NONE', 'default/that''s-nice.mp3', 'NONE', 0, 0, 0, '2015-11-24 12:01:27', '2015-11-24 15:59:03'),
(5, 'good', 'n. 好处；善行；慷慨的行为\nadj. 好的；优良的；愉快的；虔诚的\nadv. 好\nn. (Good)人名；(英)古德；(瑞典)戈德', 'jinshan/g/good.mp3', 'ɡʊd', 0, 0, 0, '2015-12-02 15:40:38', '2015-12-02 15:42:45');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL,
  `name` char(10) NOT NULL,
  `json` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `name`, `json`) VALUES
(1, '', ''),
(2, '', ''),
(3, 'abc'')', ''),
(4, 'abc'')', ''),
(5, 'abc'')', ''),
(6, 'abc'')', ''),
(7, 'abc'')', ''),
(8, 'abc'')', ''),
(9, 'abc'')', ''),
(10, 'abc'')', ''),
(11, 'abc'')', ''),
(12, 'abc'')', ''),
(13, 'abc'')', ''),
(14, 'abc'')', ''),
(15, 'abc'')', ''),
(16, 'abc'')', ''),
(17, 'abc'')', ''),
(18, 'abc'')', ''),
(19, 'abc'')', ''),
(20, 'abc'')', ''),
(21, 'abc'')', ''),
(22, 'abc', ''),
(23, 'abc'')', ''),
(24, 'aqqbc', ''),
(25, 'abc'')', ''),
(26, 'aqqbc', ''),
(27, 'abc'')', ''),
(28, 'abc'')', ''),
(29, 'abc'')', ''),
(30, 'abc'')', ''),
(31, 'aqqbc', ''),
(32, 'abc'')', ''),
(33, 'abc'')', ''),
(34, 'aqqbc', ''),
(35, 'abc'')', ''),
(36, 'abc'')', ''),
(37, 'aqqbc', ''),
(38, 'abc'')', ''),
(39, 'aqqbc', ''),
(40, 'abc'')', ''),
(41, 'aqqbc', ''),
(42, 'abc'')', ''),
(43, 'aqqbc', ''),
(44, 'abc'')', ''),
(45, 'aqqbc', ''),
(46, 'abc'')', ''),
(47, 'aqqbc', ''),
(48, 'abc'')', ''),
(49, 'aqqbc', ''),
(50, 'abc'')', ''),
(51, 'aqqbc', ''),
(52, 'abc'')', ''),
(53, 'aqqbc', ''),
(54, 'abc'')', ''),
(55, 'aqqbc', ''),
(56, 'abc'')', ''),
(57, 'aqqbc', ''),
(58, 'abc'')', ''),
(59, 'aqqbc', ''),
(60, 'abc'')', '{"status":0}'),
(61, 'abc'')', '{"status":0}'),
(62, 'abc'')', '{"status":0}');

-- --------------------------------------------------------

--
-- Table structure for table `words`
--

CREATE TABLE IF NOT EXISTS `words` (
  `id` bigint(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `means` varchar(100) NOT NULL,
  `voice` varchar(20) NOT NULL,
  `pronunciation` varchar(20) NOT NULL DEFAULT '',
  `success` int(11) NOT NULL DEFAULT '0',
  `failure` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL COMMENT '0 新词 1简单 2 中等 3 复杂  4生僻',
  `create_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `words`
--

INSERT INTO `words` (`id`, `name`, `means`, `voice`, `pronunciation`, `success`, `failure`, `status`, `create_time`, `update_time`) VALUES
(1, 'good', 'n. 好处；善行；慷慨的行为\nadj. 好的；优良的；愉快的；虔诚的\nadv. 好\nn. (Good)人名；(英)古德；(瑞典)戈德', 'merriam/g/good.mp3', 'ɡʊd', 0, 0, 0, '2015-11-23 15:15:58', '2015-12-02 15:47:38'),
(2, 'nice', 'adj. 精密的；美好的；细微的；和蔼的\nn. (Nice)人名；(英)尼斯', 'google/n/nice.mp3', 'naɪs', 0, 0, 1, '2015-11-23 15:16:32', '2016-01-14 11:24:27'),
(3, 'veteran', 'n. 老兵；老手；富有经验的人；老运动员\nadj. 经验丰富的；老兵的', 'v/veteran.mp3', '''vɛtərən', 0, 0, 0, '2015-11-23 14:45:16', '2015-11-24 15:59:03'),
(4, 'boy', 'n. 男孩；男人\nn. (Boy)人名；(英、德、西、意、刚(金)、印尼、瑞典)博伊；(法)布瓦', 'b/boy.mp3', 'bɔɪ', 6, 0, 0, '2015-11-23 15:16:31', '2015-11-24 15:59:03'),
(5, 'home', 'n. 家，住宅；产地；家乡；避难所\nadj. 国内的，家庭的；有效的\nvt. 归巢，回家\nadv. 在家，回家；深入地\nn. (Home)人名；(德、芬)霍梅；(英、尼)霍姆', 'h/home.mp3', 'hom', 2, 0, 2, '2015-11-23 15:10:05', '2016-01-14 11:25:30'),
(6, 'family', 'n. 家庭；亲属；家族；子女；[生]科；语族；[化]族\nadj. 家庭的；家族的；适合于全家的', 'youdao/f/family.mp3', '''fæməli', 15, 0, 1, '2015-11-23 15:16:26', '2016-01-13 16:45:57'),
(7, 'big', 'adj. 大的；重要的；量大的\nadv. 大量地；顺利；夸大地\nn. (Big)人名；(土)比格', 'b/big.mp3', 'bɪɡ', 0, 0, 0, '2015-11-23 10:46:20', '2015-11-24 15:59:03'),
(8, 'bed', 'n. 床；基础；河底， 海底\nvt. 使睡觉；安置，嵌入；栽种\nvi. 上床；分层', 'b/bed.mp3', 'bɛd', 0, 0, 0, '2015-11-23 10:53:03', '2015-11-24 15:59:03'),
(9, 'kill', 'n. 杀戮；屠杀\nvt. 杀死；扼杀；使终止；抵消\nadj. 致命的；致死的\nvi. 杀死\nn. (Kill)人名；(德)基尔', 'k/kill.mp3', 'kɪl', 0, 0, 0, '2015-11-23 10:56:14', '2015-11-24 15:59:03'),
(10, 'fuck', 'n. 性交；杂种；一丁点儿\nn. (Fuck)人名；(德)富克\nvt. 与...性交；诅咒；欺骗\nint. 他妈的\nvi. 性交；鬼混', 'f/fuck.mp3', 'fʌk', 0, 0, 0, '2015-11-24 15:57:50', '2015-11-24 15:59:03'),
(11, 'one', 'num. （数字）一;一个;（基数）一，第一\nn. 一个人;一点钟;一体;独一\npron. 一个人;任何人;本人，人家;东西\nadj. 某一个的;一体的;一方的', 'google/o/one.mp3', 'wʌn', 0, 0, 0, '2015-12-02 13:17:40', '2015-12-02 13:17:42'),
(12, 'two', 'n. 两个;两个东西;两点钟;一对\nadj. 两个的;我同\nnum. 两个;第二;二', 'jinshan/t/two.mp3', 'tu', 0, 0, 0, '2015-12-02 13:19:11', '2015-12-02 13:19:20'),
(13, 'collective', 'n. 集团；集合体；集合名词\nadj. 集体的；共同的；集合的；集体主义的', 'google/c/collective.', 'kə''lɛktɪv', 0, 0, 0, '2016-01-13 10:00:04', '2016-01-13 10:00:05'),
(14, 'restrictive', 'n. 限制词\nadj. 限制的；限制性的；约束的', 'google/r/restrictive', 'rɪ''strɪktɪv', 0, 0, 0, '2016-01-13 10:25:05', '2016-01-13 10:25:07'),
(15, 'dang', 'n. (Dang)人名；(印、德、印尼)丹格\nint. 讨厌；见鬼（等于damn）', 'google/d/dang.mp3', 'dæŋ', 0, 0, 0, '2016-01-13 13:36:47', '2016-01-13 13:36:48');

-- --------------------------------------------------------

--
-- Table structure for table `words_marker`
--

CREATE TABLE IF NOT EXISTS `words_marker` (
  `status` int(11) NOT NULL,
  `page` int(11) NOT NULL,
  `word` char(20) NOT NULL,
  `success` int(11) NOT NULL DEFAULT '0',
  `failure` int(11) NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `words_marker`
--

INSERT INTO `words_marker` (`status`, `page`, `word`, `success`, `failure`, `date_time`) VALUES
(0, 6, 'generation', 0, 2, '2015-01-27 11:27:32'),
(1, 13, 'sandwich', 3, 2, '2015-01-24 01:06:10'),
(2, 1, 'trigger', 3, 2, '2015-01-24 01:11:11'),
(3, 0, '', 3, 2, '0000-00-00 00:00:00'),
(4, 6, 'contractor', 3, 2, '2015-02-03 00:24:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `english_migration_versions`
--
ALTER TABLE `english_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `learning_history`
--
ALTER TABLE `learning_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phrases`
--
ALTER TABLE `phrases`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `phrases_marker`
--
ALTER TABLE `phrases_marker`
  ADD PRIMARY KEY (`status`);

--
-- Indexes for table `pronunciation`
--
ALTER TABLE `pronunciation`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`), ADD KEY `name` (`name`);

--
-- Indexes for table `words`
--
ALTER TABLE `words`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `words_marker`
--
ALTER TABLE `words_marker`
  ADD PRIMARY KEY (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `learning_history`
--
ALTER TABLE `learning_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `phrases`
--
ALTER TABLE `phrases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `pronunciation`
--
ALTER TABLE `pronunciation`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT for table `words`
--
ALTER TABLE `words`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;