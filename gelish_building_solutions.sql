-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2024 at 09:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gelish_building_solutions`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `user_id` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `product_selling_price` decimal(10,2) NOT NULL,
  `product_total_selling_price` decimal(10,2) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_on_delivery`
--

CREATE TABLE `cash_on_delivery` (
  `user_id` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `product_selling_price` decimal(10,2) NOT NULL,
  `product_total_selling_price` decimal(10,2) NOT NULL,
  `date` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_details`
--

CREATE TABLE `company_details` (
  `company_name` longtext NOT NULL,
  `company_logo` longtext NOT NULL,
  `theme_color` varchar(255) NOT NULL,
  `company_address` longtext NOT NULL,
  `system_version` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_details`
--

INSERT INTO `company_details` (`company_name`, `company_logo`, `theme_color`, `company_address`, `system_version`) VALUES
('Gelish Building Solutions', 'Logo/Gelish Building Solutions.png', '#343a40', 'Africa/Nairobi', 'v1.0.0');

-- --------------------------------------------------------

--
-- Table structure for table `company_timezone`
--

CREATE TABLE `company_timezone` (
  `timezone_id` varchar(255) NOT NULL,
  `timezone_name` varchar(255) NOT NULL,
  `selected_company_timezone` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_timezone`
--

INSERT INTO `company_timezone` (`timezone_id`, `timezone_name`, `selected_company_timezone`) VALUES
('01', 'Africa/Abidjan', '5'),
('02', 'Africa/Accra', '5'),
('03', 'Africa/Addis_Ababa', '5'),
('04', 'Africa/Algiers', '5'),
('05', 'Africa/Asmara', '5'),
('06', 'Africa/Asmera', '5'),
('07', 'Africa/Bamako', '5'),
('08', 'Africa/Bangui', '5'),
('09', 'Africa/Banjul', '5'),
('10', 'Africa/Bissau', '5'),
('100', 'America/Cuiaba', '5'),
('101', 'America/Curacao', '5'),
('102', 'America/Danmarkshavn', '5'),
('103', 'America/Dawson', '5'),
('104', 'America/Dawson_Creek', '5'),
('105', 'America/Denver', '5'),
('106', 'America/Detroit', '5'),
('107', 'America/Dominica', '5'),
('108', 'America/Edmonton', '5'),
('109', 'America/Eirunepe', '5'),
('11', 'Africa/Blantyre', '5'),
('110', 'America/El_Salvador', '5'),
('111', 'America/Ensenada', '5'),
('112', 'America/Fort_Wayne', '5'),
('113', 'America/Fortaleza', '5'),
('114', 'America/Glace_Bay', '5'),
('115', 'America/Godthab', '5'),
('116', 'America/Goose_Bay', '5'),
('117', 'America/Grand_Turk', '5'),
('118', 'America/Grenada', '5'),
('119', 'America/Guadeloupe', '5'),
('12', 'Africa/Brazzaville', '5'),
('120', 'America/Guatemala', '5'),
('121', 'America/Guayaquil', '5'),
('122', 'America/Guyana', '5'),
('123', 'America/Halifax', '5'),
('124', 'America/Havana', '5'),
('125', 'America/Hermosillo', '5'),
('126', 'America/Indiana/Indianapolis', '5'),
('127', 'America/Indiana/Knox', '5'),
('128', 'America/Indiana/Marengo', '5'),
('129', 'America/Indiana/Petersburg', '5'),
('13', 'Africa/Bujumbura', '5'),
('130', 'America/Indiana/Tell_City', '5'),
('131', 'America/Indiana/Vevay', '5'),
('132', 'America/Indiana/Vincennes', '5'),
('133', 'America/Indiana/Winamac', '5'),
('134', 'America/Indianapolis', '5'),
('135', 'America/Inuvik', '5'),
('136', 'America/Iqaluit', '5'),
('137', 'America/Jamaica', '5'),
('138', 'America/Jujuy', '5'),
('139', 'America/Juneau', '5'),
('14', 'Africa/Cairo', '5'),
('140', 'America/Kentucky/Louisville', '5'),
('141', 'America/Kentucky/Monticello', '5'),
('142', 'America/Knox_IN', '5'),
('143', 'America/Kralendijk', '5'),
('144', 'America/La_Paz', '5'),
('145', 'America/Lima', '5'),
('146', 'America/Los_Angeles', '5'),
('147', 'America/Louisville', '5'),
('148', 'America/Lower_Princes', '5'),
('149', 'America/Maceio', '5'),
('15', 'Africa/Casablanca', '5'),
('150', 'America/Managua', '5'),
('151', 'America/Manaus', '5'),
('152', 'America/Marigot', '5'),
('153', 'America/Martinique', '5'),
('154', 'America/Matamoros', '5'),
('155', 'America/Mazatlan', '5'),
('156', 'America/Mendoza', '5'),
('157', 'America/Menominee', '5'),
('158', 'America/Merida', '5'),
('159', 'America/Metlakatla', '5'),
('16', 'Africa/Ceuta', '5'),
('160', 'America/Mexico_City', '5'),
('161', 'America/Miquelon', '5'),
('162', 'America/Moncton', '5'),
('163', 'America/Monterrey', '5'),
('164', 'America/Montevideo', '5'),
('165', 'America/Montreal', '5'),
('166', 'America/Montserrat', '5'),
('167', 'America/Nassau', '5'),
('168', 'America/New_York', '5'),
('169', 'America/Nipigon', '5'),
('17', 'Africa/Conakry', '5'),
('170', 'America/Nome', '5'),
('171', 'America/Noronha', '5'),
('172', 'America/North_Dakota/Beulah', '5'),
('173', 'America/North_Dakota/Center', '5'),
('174', 'America/North_Dakota/New_Salem', '5'),
('175', 'America/Ojinaga', '5'),
('176', 'America/Panama', '5'),
('177', 'America/Pangnirtung', '5'),
('178', 'America/Paramaribo', '5'),
('179', 'America/Phoenix', '5'),
('18', 'Africa/Dakar', '5'),
('180', 'America/Port-au-Prince', '5'),
('181', 'America/Port_of_Spain', '5'),
('182', 'America/Porto_Acre', '5'),
('183', 'America/Porto_Velho', '5'),
('184', 'America/Puerto_Rico', '5'),
('185', 'America/Rainy_River', '5'),
('186', 'America/Rankin_Inlet', '5'),
('187', 'America/Recife', '5'),
('188', 'America/Regina', '5'),
('189', 'America/Resolute', '5'),
('19', 'Africa/Dar_es_Salaam', '5'),
('190', 'America/Rio_Branco', '5'),
('191', 'America/Rosario', '5'),
('192', 'America/Santa_Isabel', '5'),
('193', 'America/Santarem', '5'),
('194', 'America/Santiago', '5'),
('195', 'America/Santo_Domingo', '5'),
('196', 'America/Sao_Paulo', '5'),
('197', 'America/Scoresbysund', '5'),
('198', 'America/Shiprock', '5'),
('199', 'America/Sitka', '5'),
('20', 'Africa/Djibouti', '5'),
('200', 'America/St_Barthelemy', '5'),
('201', 'America/St_Johns', '5'),
('202', 'America/St_Kitts', '5'),
('203', 'America/St_Lucia', '5'),
('204', 'America/St_Thomas', '5'),
('205', 'America/St_Vincent', '5'),
('206', 'America/Swift_Current', '5'),
('207', 'America/Tegucigalpa', '5'),
('208', 'America/Thule', '5'),
('209', 'America/Thunder_Bay', '5'),
('21', 'Africa/Douala', '5'),
('210', 'America/Tijuana', '5'),
('211', 'America/Toronto', '5'),
('212', 'America/Tortola', '5'),
('213', 'America/Vancouver', '5'),
('214', 'America/Virgin', '5'),
('215', 'America/Whitehorse', '5'),
('216', 'America/Winnipeg', '5'),
('217', 'America/Yakutat', '5'),
('218', 'America/Yellowknife', '5'),
('219', 'Antarctica/Casey', '5'),
('22', 'Africa/El_Aaiun', '5'),
('220', 'Antarctica/Davis', '5'),
('221', 'Antarctica/DumontDUrville', '5'),
('222', 'Antarctica/Macquarie', '5'),
('223', 'Antarctica/Mawson', '5'),
('224', 'Antarctica/McMurdo', '5'),
('225', 'Antarctica/Palmer', '5'),
('226', 'Antarctica/Rothera', '5'),
('227', 'Antarctica/South_Pole', '5'),
('228', 'Antarctica/Syowa', '5'),
('229', 'Antarctica/Vostok', '5'),
('23', 'Africa/Freetown', '5'),
('230', 'Arctic/Longyearbyen', '5'),
('231', 'Asia/Aden', '5'),
('232', 'Asia/Almaty', '5'),
('233', 'Asia/Amman', '5'),
('234', 'Asia/Anadyr', '5'),
('235', 'Asia/Aqtau', '5'),
('236', 'Asia/Aqtobe', '5'),
('237', 'Asia/Ashgabat', '5'),
('238', 'Asia/Ashkhabad', '5'),
('239', 'Asia/Baghdad', '5'),
('24', 'Africa/Gaborone', '5'),
('240', 'Asia/Bahrain', '5'),
('241', 'Asia/Baku', '5'),
('242', 'Asia/Bangkok', '5'),
('243', 'Asia/Beirut', '5'),
('244', 'Asia/Bishkek', '5'),
('245', 'Asia/Brunei', '5'),
('246', 'Asia/Calcutta', '5'),
('247', 'Asia/Choibalsan', '5'),
('248', 'Asia/Chongqing', '5'),
('249', 'Asia/Chungking', '5'),
('25', 'Africa/Harare', '5'),
('250', 'Asia/Colombo', '5'),
('251', 'Asia/Dacca', '5'),
('252', 'Asia/Damascus', '5'),
('253', 'Asia/Dhaka', '5'),
('254', 'Asia/Dili', '5'),
('255', 'Asia/Dubai', '5'),
('256', 'Asia/Dushanbe', '5'),
('257', 'Asia/Gaza', '5'),
('258', 'Asia/Harbin', '5'),
('259', 'Asia/Hebron', '5'),
('26', 'Africa/Johannesburg', '5'),
('260', 'Asia/Ho_Chi_Minh', '5'),
('261', 'Asia/Hong_Kong', '5'),
('262', 'Asia/Hovd', '5'),
('263', 'Asia/Irkutsk', '5'),
('264', 'Asia/Istanbul', '5'),
('265', 'Asia/Jakarta', '5'),
('266', 'Asia/Jayapura', '5'),
('267', 'Asia/Jerusalem', '5'),
('268', 'Asia/Kabul', '5'),
('269', 'Asia/Kamchatka', '5'),
('27', 'Africa/Juba', '5'),
('270', 'Asia/Karachi', '5'),
('271', 'Asia/Kashgar', '5'),
('272', 'Asia/Kathmandu', '5'),
('273', 'Asia/Katmandu', '5'),
('274', 'Asia/Khandyga', '5'),
('275', 'Asia/Kolkata', '5'),
('276', 'Asia/Krasnoyarsk', '5'),
('277', 'Asia/Kuala_Lumpur', '5'),
('278', 'Asia/Kuching', '5'),
('279', 'Asia/Kuwait', '5'),
('28', 'Africa/Kampala', '5'),
('280', 'Asia/Macao', '5'),
('281', 'Asia/Macau', '5'),
('282', 'Asia/Magadan', '5'),
('283', 'Asia/Makassar', '5'),
('284', 'Asia/Manila', '5'),
('285', 'Asia/Muscat', '5'),
('286', 'Asia/Nicosia', '5'),
('287', 'Asia/Novokuznetsk', '5'),
('288', 'Asia/Novosibirsk', '5'),
('289', 'Asia/Omsk', '5'),
('29', 'Africa/Khartoum', '5'),
('290', 'Asia/Oral', '5'),
('291', 'Asia/Phnom_Penh', '5'),
('292', 'Asia/Pontianak', '5'),
('293', 'Asia/Pyongyang', '5'),
('294', 'Asia/Qatar', '5'),
('295', 'Asia/Qyzylorda', '5'),
('296', 'Asia/Rangoon', '5'),
('297', 'Asia/Riyadh', '5'),
('298', 'Asia/Saigon', '5'),
('299', 'Asia/Sakhalin', '5'),
('30', 'Africa/Kigali', '5'),
('300', 'Asia/Samarkand', '5'),
('301', 'Asia/Seoul', '5'),
('302', 'Asia/Shanghai', '5'),
('303', 'Asia/Singapore', '5'),
('304', 'Asia/Taipei', '5'),
('305', 'Asia/Tashkent', '5'),
('306', 'Asia/Tbilisi', '5'),
('307', 'Asia/Tehran', '5'),
('308', 'Asia/Tel_Aviv', '5'),
('309', 'Asia/Thimbu', '5'),
('31', 'Africa/Kinshasa', '5'),
('310', 'Asia/Thimphu', '5'),
('311', 'Asia/Tokyo', '5'),
('312', 'Asia/Ujung_Pandang', '5'),
('313', 'Asia/Ulaanbaatar', '5'),
('314', 'Asia/Ulan_Bator', '5'),
('315', 'Asia/Urumqi', '5'),
('316', 'Asia/Ust-Nera', '5'),
('317', 'Asia/Vientiane', '5'),
('318', 'Asia/Vladivostok', '5'),
('319', 'Asia/Yakutsk', '5'),
('32', 'Africa/Lagos', '5'),
('320', 'Asia/Yekaterinburg', '5'),
('321', 'Asia/Yerevan', '5'),
('322', 'Atlantic/Azores', '5'),
('323', 'Atlantic/Bermuda', '5'),
('324', 'Atlantic/Canary', '5'),
('325', 'Atlantic/Cape_Verde', '5'),
('326', 'Atlantic/Faeroe', '5'),
('327', 'Atlantic/Faroe', '5'),
('328', 'Atlantic/Jan_Mayen', '5'),
('329', 'Atlantic/Madeira', '5'),
('33', 'Africa/Libreville', '5'),
('330', 'Atlantic/Reykjavik', '5'),
('331', 'Atlantic/South_Georgia', '5'),
('332', 'Atlantic/St_Helena', '5'),
('333', 'Atlantic/Stanley', '5'),
('334', 'Australia/ACT', '5'),
('335', 'Australia/Adelaide', '5'),
('336', 'Australia/Brisbane', '5'),
('337', 'Australia/Broken_Hill', '5'),
('338', 'Australia/Canberra', '5'),
('339', 'Australia/Currie', '5'),
('34', 'Africa/Lome', '5'),
('340', 'Australia/Darwin', '5'),
('341', 'Australia/Eucla', '5'),
('342', 'Australia/Hobart', '5'),
('343', 'Australia/LHI', '5'),
('344', 'Australia/Lindeman', '5'),
('345', 'Australia/Lord_Howe', '5'),
('346', 'Australia/Melbourne', '5'),
('347', 'Australia/North', '5'),
('348', 'Australia/NSW', '5'),
('349', 'Australia/Perth', '5'),
('35', 'Africa/Luanda', '5'),
('350', 'Australia/Queensland', '5'),
('351', 'Australia/South', '5'),
('352', 'Australia/Sydney', '5'),
('353', 'Australia/Tasmania', '5'),
('354', 'Australia/Victoria', '5'),
('355', 'Australia/West', '5'),
('356', 'Australia/Yancowinna', '5'),
('357', 'Europe/Amsterdam', '5'),
('358', 'Europe/Andorra', '5'),
('359', 'Europe/Athens', '5'),
('36', 'Africa/Lubumbashi', '5'),
('360', 'Europe/Belfast', '5'),
('361', 'Europe/Belgrade', '5'),
('362', 'Europe/Berlin', '5'),
('363', 'Europe/Bratislava', '5'),
('364', 'Europe/Brussels', '5'),
('365', 'Europe/Bucharest', '5'),
('366', 'Europe/Budapest', '5'),
('367', 'Europe/Busingen', '5'),
('368', 'Europe/Chisinau', '5'),
('369', 'Europe/Copenhagen', '5'),
('37', 'Africa/Lusaka', '5'),
('370', 'Europe/Dublin', '5'),
('371', 'Europe/Gibraltar', '5'),
('372', 'Europe/Guernsey', '5'),
('373', 'Europe/Helsinki', '5'),
('374', 'Europe/Isle_of_Man', '5'),
('375', 'Europe/Istanbul', '5'),
('376', 'Europe/Jersey', '5'),
('377', 'Europe/Kaliningrad', '5'),
('378', 'Europe/Kiev', '5'),
('379', 'Europe/Lisbon', '5'),
('38', 'Africa/Malabo', '5'),
('380', 'Europe/Ljubljana', '5'),
('381', 'Europe/London', '5'),
('382', 'Europe/Luxembourg', '5'),
('383', 'Europe/Madrid', '5'),
('384', 'Europe/Malta', '5'),
('385', 'Europe/Mariehamn', '5'),
('386', 'Europe/Minsk', '5'),
('387', 'Europe/Monaco', '5'),
('388', 'Europe/Moscow', '5'),
('389', 'Europe/Nicosia', '5'),
('39', 'Africa/Maputo', '5'),
('390', 'Europe/Oslo', '5'),
('391', 'Europe/Paris', '5'),
('392', 'Europe/Podgorica', '5'),
('393', 'Europe/Prague', '5'),
('394', 'Europe/Riga', '5'),
('395', 'Europe/Rome', '5'),
('396', 'Europe/Samara', '5'),
('397', 'Europe/San_Marino', '5'),
('398', 'Europe/Sarajevo', '5'),
('399', 'Europe/Simferopol', '5'),
('40', 'Africa/Maseru', '5'),
('400', 'Europe/Skopje', '5'),
('401', 'Europe/Sofia', '5'),
('402', 'Europe/Stockholm', '5'),
('403', 'Europe/Tallinn', '5'),
('404', 'Europe/Tirane', '5'),
('405', 'Europe/Tiraspol', '5'),
('406', 'Europe/Uzhgorod', '5'),
('407', 'Europe/Vaduz', '5'),
('408', 'Europe/Vatican', '5'),
('409', 'Europe/Vienna', '5'),
('41', 'Africa/Mbabane', '5'),
('410', 'Europe/Vilnius', '5'),
('411', 'Europe/Volgograd', '5'),
('412', 'Europe/Warsaw', '5'),
('413', 'Europe/Zagreb', '5'),
('414', 'Europe/Zaporozhye', '5'),
('415', 'Europe/Zurich', '5'),
('416', 'Indian/Antananarivo', '5'),
('417', 'Indian/Chagos', '5'),
('418', 'Indian/Christmas', '5'),
('419', 'Indian/Cocos', '5'),
('42', 'Africa/Mogadishu', '5'),
('420', 'Indian/Comoro', '5'),
('421', 'Indian/Kerguelen', '5'),
('422', 'Indian/Mahe', '5'),
('423', 'Indian/Maldives', '5'),
('424', 'Indian/Mauritius', '5'),
('425', 'Indian/Mayotte', '5'),
('426', 'Indian/Reunion', '5'),
('427', 'Pacific/Apia', '5'),
('428', 'Pacific/Auckland', '5'),
('429', 'Pacific/Chatham', '5'),
('43', 'Africa/Monrovia', '5'),
('430', 'Pacific/Chuuk', '5'),
('431', 'Pacific/Easter', '5'),
('432', 'Pacific/Efate', '5'),
('433', 'Pacific/Enderbury', '5'),
('434', 'Pacific/Fakaofo', '5'),
('435', 'Pacific/Fiji', '5'),
('436', 'Pacific/Funafuti', '5'),
('437', 'Pacific/Galapagos', '5'),
('438', 'Pacific/Gambier', '5'),
('439', 'Pacific/Guadalcanal', '5'),
('44', 'Africa/Nairobi', '6'),
('440', 'Pacific/Guam', '5'),
('441', 'Pacific/Honolulu', '5'),
('442', 'Pacific/Johnston', '5'),
('443', 'Pacific/Kiritimati', '5'),
('444', 'Pacific/Kosrae', '5'),
('445', 'Pacific/Kwajalein', '5'),
('446', 'Pacific/Majuro', '5'),
('447', 'Pacific/Marquesas', '5'),
('448', 'Pacific/Midway', '5'),
('449', 'Pacific/Nauru', '5'),
('45', 'Africa/Ndjamena', '5'),
('450', 'Pacific/Niue', '5'),
('451', 'Pacific/Norfolk', '5'),
('452', 'Pacific/Noumea', '5'),
('453', 'Pacific/Pago_Pago', '5'),
('454', 'Pacific/Palau', '5'),
('455', 'Pacific/Pitcairn', '5'),
('456', 'Pacific/Pohnpei', '5'),
('457', 'Pacific/Ponape', '5'),
('458', 'Pacific/Port_Moresby', '5'),
('459', 'Pacific/Rarotonga', '5'),
('46', 'Africa/Niamey', '5'),
('460', 'Pacific/Saipan', '5'),
('461', 'Pacific/Samoa', '5'),
('462', 'Pacific/Tahiti', '5'),
('463', 'Pacific/Tarawa', '5'),
('464', 'Pacific/Tongatapu', '5'),
('465', 'Pacific/Truk', '5'),
('466', 'Pacific/Wake', '5'),
('467', 'Pacific/Wallis', '5'),
('468', 'Pacific/Yap', '5'),
('47', 'Africa/Nouakchott', '5'),
('48', 'Africa/Ouagadougou', '5'),
('49', 'Africa/Porto-Novo', '5'),
('50', 'Africa/Sao_Tome', '5'),
('51', 'Africa/Timbuktu', '5'),
('52', 'Africa/Tripoli', '5'),
('53', 'Africa/Tunis', '5'),
('54', 'Africa/Windhoek', '5'),
('55', 'America/Adak', '5'),
('56', 'America/Anchorage', '5'),
('57', 'America/Anguilla', '5'),
('58', 'America/Antigua', '5'),
('59', 'America/Araguaina', '5'),
('60', 'America/Argentina/Buenos_Aires', '5'),
('61', 'America/Argentina/Catamarca', '5'),
('62', 'America/Argentina/ComodRivadavia', '5'),
('63', 'America/Argentina/Cordoba', '5'),
('64', 'America/Argentina/Jujuy', '5'),
('65', 'America/Argentina/La_Rioja', '5'),
('66', 'America/Argentina/Mendoza', '5'),
('67', 'America/Argentina/Rio_Gallegos', '5'),
('68', 'America/Argentina/Salta', '5'),
('69', 'America/Argentina/San_Juan', '5'),
('70', 'America/Argentina/San_Luis', '5'),
('71', 'America/Argentina/Tucuman', '5'),
('72', 'America/Argentina/Ushuaia', '5'),
('73', 'America/Aruba', '5'),
('74', 'America/Asuncion', '5'),
('75', 'America/Atikokan', '5'),
('76', 'America/Atka', '5'),
('77', 'America/Bahia', '5'),
('78', 'America/Bahia_Banderas', '5'),
('79', 'America/Barbados', '5'),
('80', 'America/Belem', '5'),
('81', 'America/Belize', '5'),
('82', 'America/Blanc-Sablon', '5'),
('83', 'America/Boa_Vista', '5'),
('84', 'America/Bogota', '5'),
('85', 'America/Boise', '5'),
('86', 'America/Buenos_Aires', '5'),
('87', 'America/Cambridge_Bay', '5'),
('88', 'America/Campo_Grande', '5'),
('89', 'America/Cancun', '5'),
('90', 'America/Caracas', '5'),
('91', 'America/Catamarca', '5'),
('92', 'America/Cayenne', '5'),
('93', 'America/Cayman', '5'),
('94', 'America/Chicago', '5'),
('95', 'America/Chihuahua', '5'),
('96', 'America/Coral_Harbour', '5'),
('97', 'America/Cordoba', '5'),
('98', 'America/Costa_Rica', '5'),
('99', 'America/Creston', '5');

-- --------------------------------------------------------

--
-- Table structure for table `currency_prefix`
--

CREATE TABLE `currency_prefix` (
  `one` varchar(5) NOT NULL,
  `many` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `currency_prefix`
--

INSERT INTO `currency_prefix` (`one`, `many`) VALUES
('Ksh', 'Kshs');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` varchar(255) NOT NULL,
  `expense_amount` decimal(10,2) NOT NULL,
  `paid_to` varchar(255) NOT NULL,
  `paid_by` varchar(255) NOT NULL,
  `expense_description` varchar(255) NOT NULL,
  `expense_date` varchar(255) NOT NULL,
  `expense_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `expense_amount`, `paid_to`, `paid_by`, `expense_description`, `expense_date`, `expense_status`) VALUES
('476FXPNSE972', 120000.00, '9BBCGBSCD03', 'AEB1GBSE236', 'Invoice number: 56498383', '2024-05-27', 'pending'),
('DE27XPNS9061', 1000.00, 'FE52GBSD7E7', 'AEB1GBSE236', 'Invoice number: 895564', '2024-06-24', 'pending'),
('7CFFXPNS24E4', 278000.00, 'FE52GBSD7E7', 'AEB1GBSE236', 'Invoice number: POIJU8', '2024-07-15', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `forgot_password_accounts`
--

CREATE TABLE `forgot_password_accounts` (
  `email` varchar(255) NOT NULL,
  `opt` longtext NOT NULL,
  `date_and_time` varchar(255) NOT NULL,
  `token` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_reception_details`
--

CREATE TABLE `invoice_reception_details` (
  `reception_invoice_number` varchar(255) NOT NULL,
  `reception_invoice_date` varchar(255) NOT NULL,
  `reception_invoice_supplier` varchar(255) NOT NULL,
  `reception_invoice_recipient` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_reception_products`
--

CREATE TABLE `invoice_reception_products` (
  `reception_invoice_supplier` varchar(255) NOT NULL,
  `reception_invoice_number` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_quantity` decimal(10,2) NOT NULL,
  `product_buying_price` decimal(10,2) NOT NULL,
  `product_total_buying_price` decimal(10,2) NOT NULL,
  `time_added` varchar(255) NOT NULL,
  `reception_invoice_recipient` varchar(255) NOT NULL,
  `expense_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paid_cash_on_delivery`
--

CREATE TABLE `paid_cash_on_delivery` (
  `user_id` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `product_selling_price` decimal(10,2) NOT NULL,
  `product_total_selling_price` decimal(10,2) NOT NULL,
  `order_date` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL,
  `pay_date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_code` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` longtext NOT NULL,
  `product_buying_price` decimal(10,2) NOT NULL,
  `product_selling_price` decimal(10,2) NOT NULL,
  `product_date_created` varchar(255) NOT NULL,
  `product_count` decimal(10,2) NOT NULL,
  `product_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_code`, `product_name`, `product_description`, `product_buying_price`, `product_selling_price`, `product_date_created`, `product_count`, `product_status`) VALUES
('1000', 'SIMBA CEMENT', '50 KGS simba Cement', 600.00, 800.00, '2024-05-21 17:33:59', 200.00, 6),
('1001', 'TILES', 'Tiles', 1300.00, 1500.00, '2024-05-22 08:52:13', 200.00, 6),
('1002', 'D10', 'D10', 520.00, 720.00, '2024-05-22 09:17:26', 0.00, 6),
('1003', 'SAVANNAH CEMENT', '50 KGs Savannah Cement', 500.00, 650.00, '2024-05-22 09:35:52', 50.00, 6),
('1004', 'BLACK BITUMINOUS PAINT', 'Crown Black Bituminous Paint', 900.00, 800.00, '2024-05-22 09:54:42', 20.00, 6),
('1005', 'BLIND REVET', 'Blind Revet', 5.00, 5.00, '2024-06-24 16:12:45', 200.00, 6);

-- --------------------------------------------------------

--
-- Table structure for table `products_images`
--

CREATE TABLE `products_images` (
  `product_code` varchar(255) NOT NULL,
  `product_image_name` varchar(255) NOT NULL,
  `product_image_date_uploaded` varchar(255) NOT NULL,
  `product_image_uploaded_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products_images`
--

INSERT INTO `products_images` (`product_code`, `product_image_name`, `product_image_date_uploaded`, `product_image_uploaded_by`) VALUES
('1000', 'BDB07200_1000.jpg', '2024-05-23 14:34:04', 'AEB1GBSE236'),
('1000', 'CC4D7883_1000.jpg', '2024-05-23 14:36:28', 'AEB1GBSE236'),
('1000', '1426D382_1000.jpg', '2024-05-23 14:36:41', 'AEB1GBSE236'),
('1003', '0C1755F1_1003.jpeg', '2024-05-23 14:37:44', 'AEB1GBSE236'),
('1005', 'FA233DE6_1005.jpg', '2024-06-24 18:57:45', 'AEB1GBSE236'),
('1005', '07F0F076_1005.jpg', '2024-06-24 18:57:52', 'AEB1GBSE236'),
('1005', '4C44E5A3_1005.jpg', '2024-06-24 18:57:58', 'AEB1GBSE236');

-- --------------------------------------------------------

--
-- Table structure for table `products_reviews`
--

CREATE TABLE `products_reviews` (
  `user_id` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `review` longtext NOT NULL,
  `date_and_time` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products_reviews`
--

INSERT INTO `products_reviews` (`user_id`, `product_code`, `review`, `date_and_time`) VALUES
('62F0GBSDDC1', '1002', 'okay', 2024);

-- --------------------------------------------------------

--
-- Table structure for table `received_invoice_products`
--

CREATE TABLE `received_invoice_products` (
  `invoice_supplier` varchar(255) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_quantity` decimal(10,2) NOT NULL,
  `product_buying_price` decimal(10,2) NOT NULL,
  `product_total_buying_price` decimal(10,2) NOT NULL,
  `time_added` varchar(255) NOT NULL,
  `invoice_recipient` varchar(255) NOT NULL,
  `expense_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `received_invoice_products`
--

INSERT INTO `received_invoice_products` (`invoice_supplier`, `invoice_number`, `product_code`, `product_name`, `product_quantity`, `product_buying_price`, `product_total_buying_price`, `time_added`, `invoice_recipient`, `expense_id`) VALUES
('9BBCGBSCD03', '56498383', '1000', 'SIMBA CEMENT', 200.00, 600.00, 120000.00, '2024-05-27 20:47:16', 'AEB1GBSE236', '476FXPNSE972'),
('FE52GBSD7E7', '895564', '1005', 'BLIND REVET', 200.00, 5.00, 1000.00, '2024-06-24 16:14:37', 'AEB1GBSE236', 'DE27XPNS9061'),
('FE52GBSD7E7', 'POIJU8', '1004', 'BLACK BITUMINOUS PAINT', 20.00, 900.00, 18000.00, '2024-07-15 10:48:38', 'AEB1GBSE236', '7CFFXPNS24E4'),
('FE52GBSD7E7', 'POIJU8', '1001', 'TILES', 200.00, 1300.00, 260000.00, '2024-07-15 10:49:15', 'AEB1GBSE236', '7CFFXPNS24E4');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_addresses`
--

CREATE TABLE `shipping_addresses` (
  `address_id` varchar(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `State/Province` varchar(255) NOT NULL,
  `City` varchar(255) NOT NULL,
  `District` varchar(255) NOT NULL,
  `StreetAddress` longtext NOT NULL,
  `PhoneNumber` int(12) NOT NULL,
  `WhatsappNumber` int(12) NOT NULL,
  `dateAdded` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_addresses`
--

INSERT INTO `shipping_addresses` (`address_id`, `user_id`, `FirstName`, `LastName`, `State/Province`, `City`, `District`, `StreetAddress`, `PhoneNumber`, `WhatsappNumber`, `dateAdded`) VALUES
('BDAFAdrs', 4058, 'Victor', 'Munandi', 'Kenya', 'Machakos', 'Masinga', 'Masinga', 113147674, 113147674, '2024-06-03 13:40:36'),
('41DFAdrs', 4058, 'Victor', 'Munandi', 'Kenya', 'Nyeri', 'Kamakwa', 'Kamakwa', 113147674, 113147674, '2024-06-03 13:44:02');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` varchar(255) NOT NULL,
  `supplier_status` varchar(255) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_phone_number` varchar(255) NOT NULL,
  `date_created` varchar(255) NOT NULL,
  `supplier_email_address` varchar(255) NOT NULL,
  `supplier_location` varchar(255) NOT NULL,
  `supplier_address` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_status`, `supplier_name`, `supplier_phone_number`, `date_created`, `supplier_email_address`, `supplier_location`, `supplier_address`, `created_by`) VALUES
('FE52GBSD7E7', '5', 'Neema Suppliers Kenya', '0112458995', '2024-05-20 21:44:19', 'neema@yahoo.com', 'Embu', 'Manyatta', 'AEB1GBSE236'),
('9BBCGBSCD03', '5', 'MAISHA MABATI', '0756020169', '2024-05-23 15:53:10', 'sales@maishamabati.com', 'NAIROBI', 'MAISHA MABATI MILLS LTD. HEAD OFFICE, RUIRU KAMITI ROAD, RUIRU P.O.BOX 33319-00600 NAIROBI, KENYA', 'AEB1GBSE236'),
('EB9FGBSFF0B', '5', 'Simba cement', '0769080103', '2024-05-23 15:54:50', 'info@ncc-ke.com', 'Athi River', 'Athi River Off Mombasa Rd, Lukenya, Rd', 'AEB1GBSE236');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(255) NOT NULL,
  `user_type` varchar(1) NOT NULL,
  `user_first_name` varchar(255) NOT NULL,
  `user_middle_name` varchar(255) NOT NULL,
  `user_last_name` varchar(255) NOT NULL,
  `user_phone_number` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_avatar` varchar(255) NOT NULL,
  `date_joined` varchar(255) NOT NULL,
  `system_access` varchar(255) NOT NULL,
  `last_login` varchar(255) NOT NULL,
  `last_login_type` varchar(255) NOT NULL,
  `last_seen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_type`, `user_first_name`, `user_middle_name`, `user_last_name`, `user_phone_number`, `email_address`, `username`, `user_password`, `user_avatar`, `date_joined`, `system_access`, `last_login`, `last_login_type`, `last_seen`) VALUES
('AEB1GBSE236', '9', 'Faith', '', 'Kioko', '', 'fkioko790@gmail.com', 'faith_kioko', '$2y$10$miu6xlUPg45qeFt66AOor.OeSrBfKmqCchIeFaNH6Yz2.bdqVLXv2', '', '2024-05-14 14:45:52', '5', '2024-07-16 20:06:39', 'password', '2024-07-16 22:43:55'),
('62F0GBSDDC1', '7', 'Victor', '', 'Munandi', '', 'victormunandi4@gmail.com', 'victor_munandi', '$2y$10$ijHcAqLcKiNnG9ZXo.s6L.D1mq9NmJHTWBMSYsegox5jQXQzw5E1i', '', '2024-07-13 21:33:41', '5', '2024-07-16 17:50:45', 'password', '2024-07-16 22:40:49');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `user_id` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`user_id`, `product_code`, `date`) VALUES
('62F0GBSDDC1', '1005', '2024-07-16 18:43:35');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
