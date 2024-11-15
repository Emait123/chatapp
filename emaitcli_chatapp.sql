-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 15, 2024 lúc 09:19 PM
-- Phiên bản máy phục vụ: 10.6.20-MariaDB
-- Phiên bản PHP: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `emaitcli_chatapp`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat_history`
--

CREATE TABLE `chat_history` (
  `id` int(11) NOT NULL,
  `tel_user_id` bigint(20) NOT NULL COMMENT 'telegram user id',
  `add_date` datetime NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(32) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chat_history`
--

INSERT INTO `chat_history` (`id`, `tel_user_id`, `add_date`, `message`, `type`, `deleted`) VALUES
(25, 7050621296, '2024-08-26 08:09:09', 'xin nghỉ ngày kia', 'user', 1),
(26, 7050621296, '2024-08-26 08:09:21', 'Thông tin nghỉ phép:\nBắt đầu:28/08/2024 08:00\nKết thúc:28/08/2024 17:00\nLý do:\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 1),
(27, 7050621296, '2024-08-26 08:10:34', 'nghỉ để đi khám bệnh', 'user', 1),
(28, 7050621296, '2024-08-26 08:10:35', 'Thông tin nghỉ phép:\nBắt đầu:28/08/2024 08:00\nKết thúc:28/08/2024 17:00\nLý do:đi khám bệnh\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(29, 7050621296, '2024-08-26 08:11:04', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 1),
(30, 7050621296, '2024-08-26 09:26:40', 'đúng rồi', 'user', 1),
(31, 7050621296, '2024-08-26 09:28:50', 'Thông tin nghỉ phép:\nBắt đầu: 28/08/2024 08:00\nKết thúc: 28/08/2024 17:00\nLý do: đi khám bệnh\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(32, 7050621296, '2024-08-26 09:28:48', 'đúng rồi', 'user', 1),
(33, 7050621296, '2024-08-26 09:35:02', 'xin nghỉ phép ngày kia', 'user', 1),
(34, 7050621296, '2024-08-26 09:35:14', 'Thông tin nghỉ phép:\nBắt đầu: 28/08/2024 08:00\nKết thúc: 28/08/2024 17:00\nLý do: \nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 1),
(35, 7050621296, '2024-08-26 09:35:33', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 1),
(36, 7050621296, '2024-08-26 09:35:45', 'nghỉ để đi khám bệnh', 'user', 1),
(37, 7050621296, '2024-08-26 09:35:48', 'Thông tin nghỉ phép:\nBắt đầu: 28/08/2024 08:00\nKết thúc: 28/08/2024 17:00\nLý do: đi khám bệnh\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(38, 7050621296, '2024-08-26 09:40:10', 'a lô', 'user', 1),
(39, 7050621296, '2024-08-26 09:40:25', 'tôi muốn xin nghỉ ngày kia để đi khám bệnh', 'user', 1),
(40, 7050621296, '2024-08-26 09:40:26', 'Thông tin nghỉ phép:\nBắt đầu: 28/08/2024 08:00\nKết thúc: 28/08/2024 17:00\nLý do: đi khám bệnh\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(41, 7050621296, '2024-08-26 09:45:24', 'tôi muốn xin nghỉ ngày kia để đi khám bệnh', 'user', 1),
(42, 7050621296, '2024-08-26 09:45:26', 'Thông tin nghỉ phép:\nBắt đầu: 28/08/2024 08:00\nKết thúc: 28/08/2024 17:00\nLý do: đi khám bệnh\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(43, 7050621296, '2024-08-26 09:47:09', 'tôi muốn xin nghỉ ngày kia để đi khám bệnh', 'user', 1),
(44, 7050621296, '2024-08-26 09:47:10', 'Thông tin nghỉ phép:\nBắt đầu: 28/08/2024 08:00\nKết thúc: 28/08/2024 17:00\nLý do: đi khám bệnh\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(45, 7050621296, '2024-08-26 09:47:55', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 1),
(46, 7050621296, '2024-08-26 09:48:07', 'thôi đúng rồi', 'user', 1),
(47, 7050621296, '2024-08-26 17:10:50', 'Xin nghỉ ngày kia để đi khám bệnh', 'user', 1),
(48, 7050621296, '2024-08-26 17:10:52', 'Thông tin nghỉ phép:\nBắt đầu: 28/08/2024 08:00\nKết thúc: 28/08/2024 17:00\nLý do: khám bệnh\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(49, 7050621296, '2024-08-27 01:43:17', 'Xin nghỉ ngày kia để đi khám bệnh', 'user', 1),
(50, 7050621296, '2024-08-27 01:43:18', 'Thông tin nghỉ phép:\nBắt đầu: 29/08/2024 08:00\nKết thúc: 29/08/2024 17:00\nLý do: khám bệnh\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(51, 7050621296, '2024-08-27 01:44:30', 'Xin nghỉ ngày kia để đi khám bệnh', 'user', 1),
(52, 7050621296, '2024-08-27 01:44:31', 'Thông tin nghỉ phép:\nBắt đầu: 29/08/2024 08:00\nKết thúc: 29/08/2024 17:00\nLý do: khám bệnh\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(53, 7050621296, '2024-08-27 01:47:24', 'Xin nghỉ ngày kia để đi khám bệnh', 'user', 1),
(54, 7050621296, '2024-08-27 01:47:43', 'Thông tin nghỉ phép;\nBắt đầu: 29/08/2024 08:00;\nKết thúc: 29/08/2024 17:00;\nLý do: khám bệnh;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(55, 7050621296, '2024-08-27 02:24:14', 'Xin nghỉ cuối tuần này để đi khám bệnh', 'user', 1),
(56, 7050621296, '2024-08-27 02:24:20', 'Thông tin nghỉ phép;\nBắt đầu: 31/08/2024 08:00;\nKết thúc: 01/09/2024 17:00;\nLý do: khám bệnh;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(57, 7050621296, '2024-08-27 02:24:43', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 1),
(58, 7050621296, '2024-08-27 02:24:55', 'Sửa thành nghỉ thứ 6', 'user', 1),
(59, 7050621296, '2024-08-27 02:24:57', 'Thông tin nghỉ phép;\nBắt đầu: 30/08/2024 08:00;\nKết thúc: 30/08/2024 17:00;\nLý do: khám bệnh;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(60, 7050621296, '2024-08-27 03:19:27', 'xin nghỉ từ thứ 5 đến thứ 6 do nhà có việc bận', 'user', 1),
(61, 7050621296, '2024-08-27 03:19:28', 'Thông tin nghỉ phép;\nBắt đầu: 29/08/2024 08:00;\nKết thúc: 30/08/2024 17:00;\nLý do: nhà có việc bận;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(62, 7050621296, '2024-08-27 03:24:26', 'xin nghỉ từ thứ 3 đến thứ 4 tuần sau để đi khám bệnh', 'user', 1),
(63, 7050621296, '2024-08-27 03:24:37', 'Thông tin nghỉ phép;\nBắt đầu: 03/09/2024 08:00;\nKết thúc: 04/09/2024 17:00;\nLý do: đi khám bệnh;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(64, 7050621296, '2024-08-27 03:32:04', 'Xin nghỉ thứ 5 đến thứ 6 tuần này để đi khám bệnh', 'user', 1),
(65, 7050621296, '2024-08-27 03:32:07', 'Thông tin nghỉ phép;\nBắt đầu: 29/08/2024 08:00;\nKết thúc: 30/08/2024 17:00;\nLý do: đi khám bệnh;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(66, 7050621296, '2024-08-27 03:32:16', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 1),
(67, 7050621296, '2024-08-27 03:32:33', 'Sửa thành nghỉ thứ 6 thôi', 'user', 1),
(68, 7050621296, '2024-08-27 03:32:34', 'Thông tin nghỉ phép;\nBắt đầu: 30/08/2024 08:00;\nKết thúc: 30/08/2024 17:00;\nLý do: đi khám bệnh;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(69, 5580139045, '2024-08-27 03:40:00', 'Hôm nay tớ nghỉ nhé.', 'user', 0),
(70, 5580139045, '2024-08-27 03:40:02', 'Thông tin nghỉ phép;\nBắt đầu: 27/08/2024 08:00;\nKết thúc: 27/08/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 0),
(71, 5580139045, '2024-08-27 03:41:23', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 0),
(72, 5580139045, '2024-08-27 03:41:41', 'Tớ nghỉ ốm.', 'user', 0),
(73, 5580139045, '2024-08-27 03:41:43', 'Thông tin nghỉ phép;\nBắt đầu: 27/08/2024 08:00;\nKết thúc: 27/08/2024 17:00;\nLý do: bị ốm;\nCác thông tin này đã đúng chưa?', 'assistant', 0),
(74, 5304040462, '2024-08-27 03:45:19', 'xin chào', 'user', 1),
(75, 5304040462, '2024-08-27 03:45:32', 'tôi muốn xin nghỉ phép', 'user', 1),
(76, 5304040462, '2024-08-27 03:45:43', 'bạn biết tên tôi ko', 'user', 1),
(77, 7050621296, '2024-08-27 03:49:21', 'xin chào', 'user', 1),
(78, 7050621296, '2024-08-27 03:49:28', 'bạn biết tên tôi không?', 'user', 1),
(79, 5304040462, '2024-08-27 03:49:53', 'Hi', 'user', 1),
(80, 5304040462, '2024-08-27 03:51:45', 'tôi muốn xin nghỉ vào ngày mai với lý do đi công tác', 'user', 1),
(81, 5304040462, '2024-08-27 03:51:52', 'Thông tin nghỉ phép;\nBắt đầu: 28/08/2024 08:00;\nKết thúc: 28/08/2024 17:00;\nLý do: đi công tác;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(82, 7050621296, '2024-08-27 03:51:51', 'tôi muốn xin nghỉ phép', 'user', 1),
(83, 5304040462, '2024-08-27 03:52:09', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 1),
(84, 5304040462, '2024-08-27 03:52:21', 'nghỉ ốm ngày mai', 'user', 1),
(85, 5304040462, '2024-08-27 03:52:22', 'Thông tin nghỉ phép;\nBắt đầu: 28/08/2024 08:00;\nKết thúc: 28/08/2024 17:00;\nLý do: bị ốm;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(86, 5304040462, '2024-08-27 03:52:37', 'nghỉ thai sản', 'user', 1),
(87, 5304040462, '2024-08-27 03:52:37', 'nghỉ thai sản', 'user', 1),
(88, 5304040462, '2024-08-27 03:53:49', 'Thông tin nghỉ phép;\nBắt đầu: 28/08/2024 08:00;\nKết thúc: 28/08/2024 17:00;\nLý do: nghỉ thai sản;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(89, 5304040462, '2024-08-27 03:53:52', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 1),
(90, 7050621296, '2024-08-27 03:54:48', 'xin nghỉ từ 2h đến 5h chiều mai', 'user', 1),
(91, 7050621296, '2024-08-27 03:54:50', 'Thông tin nghỉ phép;\nBắt đầu: 28/08/2024 14:00;\nKết thúc: 28/08/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 1),
(92, 5304040462, '2024-08-27 03:59:56', 'tôi xin nghỉ phép ngày kia đi du lịch', 'user', 1),
(93, 5304040462, '2024-08-27 03:59:57', 'Thông tin nghỉ phép;\nBắt đầu: 29/08/2024 08:00;\nKết thúc: 29/08/2024 17:00;\nLý do: đi du lịch;\nCác thông tin này đã đúng chưa?', 'assistant', 1),
(94, 5304040462, '2024-08-27 04:02:31', 'xin chào seo', 'user', 1),
(95, 5304040462, '2024-08-27 04:02:39', 'bạn khoẻ không', 'user', 1),
(96, 5304040462, '2024-08-27 04:03:00', 'tôi muốn hỏi về chứng khoán', 'user', 1),
(97, 5304040462, '2024-08-27 04:03:26', 'chỉ số vnindex', 'user', 1),
(98, 5580139045, '2024-08-31 02:12:33', 'Hôm nay Anh nghỉ.', 'user', 0),
(99, 5580139045, '2024-08-31 02:12:34', 'Thông tin nghỉ phép;\nBắt đầu: 31/08/2024 08:00;\nKết thúc: 31/08/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 0),
(100, 5580139045, '2024-09-03 06:47:50', 'Tớ nghỉ', 'user', 0),
(101, 5580139045, '2024-09-03 06:48:11', 'Mai Tớ mệt', 'user', 0),
(102, 5580139045, '2024-09-03 06:48:11', 'Thông tin nghỉ phép;\nBắt đầu: 04/04/2024 08:00;\nKết thúc: 04/04/2024 17:00;\nLý do: mệt;\nCác thông tin này đã đúng chưa?', 'assistant', 0),
(103, 5580139045, '2024-09-03 06:49:13', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 0),
(104, 5580139045, '2024-09-03 06:50:04', 'Nên đề là thông tin nghỉ thầy vì \"Thông tin nghỉ phép:\"', 'user', 0),
(105, 5580139045, '2024-09-03 06:50:06', 'Thông tin nghỉ phép;\nBắt đầu: 04/04/2024 08:00;\nKết thúc: 04/04/2024 17:00;\nLý do: mệt;\nCác thông tin này đã đúng chưa?', 'assistant', 0),
(106, 7050621296, '2024-09-04 12:50:35', 'Xin chào', 'user', 1),
(107, 7050621296, '2024-09-04 13:04:14', 'Xin nghỉ chiều mai', 'user', 1),
(108, 7050621296, '2024-09-04 13:04:15', 'Thông tin nghỉ phép;\nBắt đầu: 05/09/2024 08:00;\nKết thúc: 05/09/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 1),
(109, 7050621296, '2024-09-04 13:11:01', 'Bạn muốn thay đổi thông tin gì?', 'assistant', 1),
(110, 7050621296, '2024-09-04 13:11:15', 'A lô a lô', 'user', 1),
(111, 7050621296, '2024-09-05 03:16:07', 'Xin chào', 'user', 1),
(112, 7050621296, '2024-09-05 03:23:15', 'Chào', 'user', 0),
(113, 7050621296, '2024-09-05 04:03:54', 'Chào', 'user', 0),
(114, 5304040462, '2024-09-05 04:08:13', 'Hi', 'user', 1),
(115, 5304040462, '2024-09-05 04:08:41', 'bạn khoẻ không', 'user', 1),
(116, 5304040462, '2024-09-05 04:08:48', 'tôi ốm', 'user', 1),
(117, 5580139045, '2024-09-05 14:36:50', 'Khỏe không BI?', 'user', 0),
(118, 5580139045, '2024-09-05 14:37:10', 'Mình mệt nên mai nghỉ', 'user', 0),
(119, 5580139045, '2024-09-05 14:37:10', 'Thông tin nghỉ phép;\nBắt đầu: 06/09/2024 08:00;\nKết thúc: 06/09/2024 17:00;\nLý do: mệt;\nCác thông tin này đã đúng chưa?', 'assistant', 0),
(120, 5580139045, '2024-09-05 14:43:43', 'Đạt à!', 'user', 0),
(121, 5580139045, '2024-09-06 07:03:10', 'Chiều tớ nghỉ vì mệt.', 'user', 0),
(122, 5580139045, '2024-09-06 07:03:11', 'Thông tin nghỉ phép;\nBắt đầu: 06/09/2024 08:00;\nKết thúc: 06/09/2024 17:00;\nLý do: mệt;\nCác thông tin này đã đúng chưa?', 'assistant', 0),
(123, 5580139045, '2024-09-06 11:10:38', 'Anh nghỉ đây.', 'user', 0),
(124, 5580139045, '2024-09-06 11:11:06', 'Sáng mai Anh đi khám.', 'user', 0),
(125, 5580139045, '2024-09-06 11:11:07', 'Thông tin nghỉ phép;\nBắt đầu: 07/09/2024 08:00;\nKết thúc: 07/09/2024 17:00;\nLý do: đi khám;\nCác thông tin này đã đúng chưa?', 'assistant', 0),
(126, 5580139045, '2024-09-08 14:50:44', 'Sáng mai Anh nghỉ nhé', 'user', 0),
(127, 5580139045, '2024-09-08 14:50:45', 'Thông tin nghỉ phép;\nBắt đầu: 09/09/2024 08:00;\nKết thúc: 09/09/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 0),
(128, 5580139045, '2024-09-12 16:37:20', 'Tớ nghỉ đây.', 'user', 0),
(129, 5580139045, '2024-09-12 16:37:41', 'Mai tớ đi khám.', 'user', 0),
(130, 5580139045, '2024-09-12 16:37:44', 'Thông tin nghỉ phép;\nBắt đầu: 13/09/2024 08:00;\nKết thúc: 13/09/2024 17:00;\nLý do: đi khám;\nCác thông tin này đã đúng chưa?', 'assistant', 0),
(131, 5580139045, '2024-09-13 17:10:16', 'Hôm nay tớ nghỉ', 'user', 0),
(132, 5580139045, '2024-09-13 17:10:17', 'Thông tin nghỉ phép;\nBắt đầu: 13/09/2024 08:00;\nKết thúc: 13/09/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 0),
(133, 5580139045, '2024-09-14 09:41:33', 'Hôm nay Anh nghỉ', 'user', 0),
(134, 5580139045, '2024-09-14 09:41:34', 'Thông tin nghỉ phép;\nBắt đầu: 14/09/2024 08:00;\nKết thúc: 14/09/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 0),
(135, 5580139045, '2024-09-16 10:01:28', 'Chiều này tôi xin nghỉ', 'user', 0),
(136, 5580139045, '2024-09-16 10:01:29', 'Thông tin nghỉ phép;\nBắt đầu: 16/09/2024 08:00;\nKết thúc: 16/09/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 0),
(137, 5580139045, '2024-09-19 00:01:53', 'Hôm nay tôi nghỉ', 'user', 0),
(138, 5580139045, '2024-09-19 00:01:54', 'Thông tin nghỉ phép;\nBắt đầu: 19/09/2024 08:00;\nKết thúc: 19/09/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 0),
(139, 5580139045, '2024-09-21 00:28:37', 'Mai cho Anh nghỉ', 'user', 0),
(140, 5580139045, '2024-09-21 00:28:39', 'Thông tin nghỉ;\nBắt đầu: 22/09/2024 08:00;\nKết thúc: 22/09/2024 17:00;\nLý do: ;\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?', 'assistant', 0),
(141, 5304040462, '2024-11-13 06:54:16', 'xin chào', 'user', 1),
(142, 5304040462, '2024-11-14 12:06:17', 'tôi xin nghỉ phép', 'user', 0),
(143, 5304040462, '2024-11-14 12:06:36', 'ngày mai vì tôi bị ốm', 'user', 0),
(144, 5304040462, '2024-11-14 12:06:41', 'Thông tin nghỉ;\nBắt đầu: 15/11/2024 08:00;\nKết thúc: 15/11/2024 17:00;\nLý do: bị ốm;\nCác thông tin này đã đúng chưa?', 'assistant', 0),
(145, 5304040462, '2024-11-14 12:42:06', 'Xin chào', 'user', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `birth` date DEFAULT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `class_name` varchar(64) NOT NULL,
  `telegram_id` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `employee`
--

INSERT INTO `employee` (`id`, `user_id`, `name`, `birth`, `gender`, `class_name`, `telegram_id`) VALUES
(1, 1, 'Nhân viên 1', '1997-03-24', 1, 'test', ''),
(2, 3, 'Lê Diệu Thúy', NULL, 0, 'Nhân sự', '5304040462'),
(3, 4, 'Trương Tiến Tùng', '1962-02-18', 1, 'Nhân sự', '5580139045'),
(4, 5, 'Trương Tiến Đạt', '1997-03-24', 1, 'Nhân sự', '7050621296'),
(5, 6, 'Nguyễn Thị Ngọc Hoa', '1982-06-09', 0, 'Kỹ thuật', ''),
(6, 7, 'Nguyễn Thị Mai', '1986-01-14', 0, 'CSKH', ''),
(7, 8, 'Nguyễn Văn Tú', '1986-04-06', 1, 'CSKH', ''),
(8, 9, 'Nguyễn Thùy Minh', '1987-10-06', 0, 'CSKH', ''),
(9, 10, 'Hoàng Trường Minh', '1988-12-14', 1, 'CSKH', ''),
(10, 11, 'Nguyễn Thị Hồng Nhung', '1983-02-03', 0, 'Hành chính', ''),
(11, 12, 'Trần Thị Anh Thư', '1997-01-02', 0, 'Hành chính', ''),
(12, 13, 'Vũ Duy Việt', '1982-12-29', 1, 'Kỹ thuật', ''),
(13, 14, 'Lã Mạnh Hà', '1987-05-04', 1, 'Marketing', ''),
(14, 15, 'Nguyễn Thị Minh Nguyệt', '1982-06-21', 0, 'Phó Giám đốc - PT HC', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Nhân viên');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `timeoff`
--

CREATE TABLE `timeoff` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `request_date` datetime NOT NULL DEFAULT current_timestamp(),
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `type` varchar(16) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT current_timestamp(),
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `timeoff`
--

INSERT INTO `timeoff` (`id`, `employee_id`, `request_date`, `start_date`, `end_date`, `duration`, `reason`, `type`, `approved`, `approved_by`, `approved_date`, `deleted`) VALUES
(1, 1, '2024-08-04 14:27:20', '2024-08-07 12:00:00', '2024-08-07 17:00:00', NULL, 'nhà có việc bận', '', 0, NULL, '2024-08-04 14:27:20', 0),
(2, 1, '2024-08-05 09:18:16', '2024-08-13 00:00:00', '2024-08-13 23:59:59', NULL, 'nhà có việc bận', '', 0, NULL, '2024-08-05 09:18:16', 0),
(3, 1, '2024-08-05 16:05:47', '2024-08-09 00:00:00', '2024-08-09 23:59:59', NULL, 'có lịch đi khám bệnh', '', 0, NULL, '2024-08-05 16:05:47', 0),
(4, 1, '2024-08-15 13:48:58', '2024-08-16 00:00:00', NULL, NULL, 'xin nghỉ', '', 0, NULL, '2024-08-15 13:48:58', 0),
(5, 4, '2024-08-27 02:25:17', '2024-08-30 08:00:00', '2024-08-30 17:00:00', 1, 'khám bệnh', 'chedo', 0, NULL, '2024-08-27 09:25:17', 0),
(6, 4, '2024-08-27 03:19:58', '2024-08-29 08:00:00', '2024-08-30 17:00:00', 2, 'nhà có việc bận', 'luong', 0, NULL, '2024-08-27 10:19:59', 0),
(7, 4, '2024-08-27 03:24:50', '2024-09-03 08:00:00', '2024-09-04 17:00:00', 2, 'đi khám bệnh', 'chedo', 0, NULL, '2024-08-27 10:24:51', 0),
(8, 4, '2024-08-27 03:32:42', '2024-08-30 08:00:00', '2024-08-30 17:00:00', 1, 'đi khám bệnh', 'chedo', 0, NULL, '2024-08-27 10:32:42', 0),
(9, 3, '2024-08-27 03:41:55', '2024-08-27 08:00:00', '2024-08-27 17:00:00', 1, 'bị ốm', 'chedo', 0, NULL, '2024-08-27 10:41:56', 0),
(10, 2, '2024-08-27 03:53:55', '2024-08-28 08:00:00', '2024-08-28 17:00:00', 1, 'nghỉ thai sản', 'chedo', 0, NULL, '2024-08-27 10:53:56', 0),
(11, 2, '2024-08-27 04:00:08', '2024-08-29 08:00:00', '2024-08-29 17:00:00', 1, 'đi du lịch', 'luong', 0, NULL, '2024-08-27 11:00:09', 0),
(12, 1, '2024-09-05 10:48:19', '2024-09-09 00:00:00', NULL, NULL, 'ốm', '', 0, NULL, '2024-09-05 10:48:19', 0),
(13, 1, '2024-09-05 11:27:28', '2024-09-15 00:00:00', '2024-09-15 23:59:59', NULL, 'du lịch', '', 0, NULL, '2024-09-05 11:27:28', 0),
(14, 2, '2024-11-14 12:07:01', '2024-11-15 08:00:00', '2024-11-15 17:00:00', 1, 'bị ốm', 'chedo', 0, NULL, '2024-11-14 19:07:13', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tool`
--

CREATE TABLE `tool` (
  `id` int(11) NOT NULL,
  `img_name` varchar(128) DEFAULT NULL,
  `msv` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tool`
--

INSERT INTO `tool` (`id`, `img_name`, `msv`) VALUES
(1, 'file_0', '20A10010394_org'),
(2, 'file_1', '20A100_org_1'),
(3, 'file_1_bright', '20A_brigh'),
(4, 'file_1_flip', '20A1_flip'),
(5, 'file_1_noise', '24Anoise'),
(6, 'file_1_rotate', '24Arotate'),
(7, 'file_1_translate', '24translatr'),
(8, 'file_1_zoom', '24zoom');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `createdate` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `phone`, `createdate`, `last_login`, `role_id`, `deleted`) VALUES
(1, 'user', '$2y$10$uK3U.RcWI1m5Zv6HZh1vcOHww6CPqvOeu7LSfNiLqUIpfgspr61A6', 'emait123@gmail.com', '0123456', '2024-08-04 13:50:12', '2024-11-14 12:46:17', 2, 0),
(2, 'admin', '$2y$10$dKbUqy7aakcmDXf3VzX8ROYxO605Wg7ecChgUbpBiPf2hym1x7MDK', 'tetra.dragon197@gmail.com', '0123456', '2024-08-25 08:46:54', '2024-11-15 13:34:04', 1, 0),
(3, 'ledieuthuy', '$2y$10$DC.p3obB96Ssv5ivwxxBWedJB3JjMpsTvLZrs2YYrotBnYdfEUgIq', NULL, NULL, '2024-08-25 10:00:30', '2024-11-14 03:10:00', 2, 0),
(4, 'truongtientung', '$2y$10$DBNp8fJDwJndKlvY9c0vIO/xyKNArXziO4efRGhAXYL4b1hB7VvHC', NULL, NULL, '2024-08-25 10:01:13', '2024-08-25 10:08:02', 2, 0),
(5, 'truongtiendat', '$2y$10$5pNJ8CoXXjFMwFpiaXYHCu.zoGRbh8wPpGtHZ87ZpL6FRPITtpQZe', NULL, NULL, '2024-08-25 10:07:08', '2024-10-04 09:08:07', 2, 0),
(6, 'ngochoa', '$2y$10$mUTcLg4jFqTj.HmROUtMneTu25s5WheV6/F14mztcJZ3ZQoEwd7ca', NULL, NULL, '2024-11-15 13:56:20', NULL, 2, 0),
(7, 'thimai', '$2y$10$15EHhoFdoXFAT6f9.eYkXuG9hkRh/kbavwqRQ8Cr3R2yRqbCHhWYm', NULL, NULL, '2024-11-15 14:10:06', NULL, 2, 0),
(8, 'vantu', '$2y$10$8JjOAntCFwmzut4cCAoJV.GB48fRDIORzsP.fLqXToVXptA98v0Jq', NULL, NULL, '2024-11-15 14:11:35', NULL, 2, 0),
(9, 'thuyminh', '$2y$10$281pdRxKIdTlupvp/VAIQOjf3jPyNH253/QIj69pYW3wmxIJNB.qi', NULL, NULL, '2024-11-15 14:12:15', NULL, 2, 0),
(10, 'truongminh', '$2y$10$vofiP.cvoJQQViZqRl5DGOXai0ecYBgqS5fxXn6cJjMjmDZ2DX75u', NULL, NULL, '2024-11-15 14:12:51', NULL, 2, 0),
(11, 'hongnhung', '$2y$10$ZuuqAOipzYvWU7w7Ck2iQOXlHkc/4Kd7yBqU5ii9cn/pWUpzL33La', NULL, NULL, '2024-11-15 14:13:21', NULL, 2, 0),
(12, 'anhthu', '$2y$10$8TJ..Wr9rjrUk7x8aWHcDeqiz81fYmkpYPPB6/DMqouyhq52CrcZ.', NULL, NULL, '2024-11-15 14:13:50', NULL, 2, 0),
(13, 'duyviet', '$2y$10$SeA1qggsl2JPss847.GfDubplf9R9QxIqMM0leSH05iv7MuxkoxDy', NULL, NULL, '2024-11-15 14:14:20', NULL, 2, 0),
(14, 'manhha', '$2y$10$emUVTvjqysg1X3wXixb4Qe3KftoxiW3flL36Pnf.1GhBsyQ2GW6KW', NULL, NULL, '2024-11-15 14:15:10', NULL, 2, 0),
(15, 'minhnguyet', '$2y$10$BCWIOLmwEFBJ8rkv.bqIKeznYvdVIUjHwR5wEuOGbENIuakcSrzla', NULL, NULL, '2024-11-15 14:16:40', NULL, 2, 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chat_history`
--
ALTER TABLE `chat_history`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `timeoff`
--
ALTER TABLE `timeoff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Chỉ mục cho bảng `tool`
--
ALTER TABLE `tool`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chat_history`
--
ALTER TABLE `chat_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT cho bảng `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `timeoff`
--
ALTER TABLE `timeoff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `tool`
--
ALTER TABLE `tool`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Các ràng buộc cho bảng `timeoff`
--
ALTER TABLE `timeoff`
  ADD CONSTRAINT `timeoff_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`),
  ADD CONSTRAINT `timeoff_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`);

--
-- Các ràng buộc cho bảng `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
