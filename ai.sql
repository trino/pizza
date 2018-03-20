-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2018 at 08:47 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ai`
--

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE `actions` (
  `id` int(11) NOT NULL,
  `eventname` varchar(64) NOT NULL,
  `party` tinyint(4) NOT NULL COMMENT '0=user,1=admin,2=restaurant',
  `sms` tinyint(1) NOT NULL,
  `phone` tinyint(1) NOT NULL,
  `email` tinyint(1) NOT NULL,
  `message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `actions`
--

INSERT INTO `actions` (`id`, `eventname`, `party`, `sms`, `phone`, `email`, `message`) VALUES
(1, 'order_placed', 2, 0, 0, 1, '[sitename] - A new order was placed'),
(2, 'order_placed', 1, 0, 0, 0, '[sitename] - A new order was placed [url]'),
(3, 'order_placed', 0, 0, 0, 1, '[sitename] - Here is your receipt'),
(4, 'order_declined', 0, 0, 0, 1, '[sitename] - Your order was cancelled: [reason]'),
(5, 'order_declined', 1, 1, 0, 0, '[sitename] - An order was cancelled: [reason]'),
(6, 'order_confirmed', 1, 1, 0, 0, '[sitename] - An order was approved: [reason]'),
(7, 'user_registered', 0, 0, 0, 1, '[sitename] - Thank you for registering'),
(9, 'user_registered', 1, 0, 0, 1, '[sitename] - Thank you for registering'),
(10, 'order_placed', 1, 0, 0, 0, '[sitename] - A new order was placed [url]'),
(11, 'order_placed', 2, 0, 1, 0, 'Hello [name], you have an order on [sitename]');

-- --------------------------------------------------------

--
-- Table structure for table `additional_toppings`
--

CREATE TABLE `additional_toppings` (
  `id` int(10) UNSIGNED NOT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `additional_toppings`
--

INSERT INTO `additional_toppings` (`id`, `size`, `price`) VALUES
(1, 'Small', 0.95),
(2, 'Medium', 1.2),
(3, 'Large', 1.5),
(4, 'X-Large', 1.7),
(6, 'Panzerotti', 0.95),
(7, 'Delivery', 4),
(8, 'Minimum', 15),
(10, 'DeliveryTime', 45),
(11, 'over$20', 10),
(12, 'over$30', 20),
(13, 'over$40', 30);

-- --------------------------------------------------------

--
-- Table structure for table `combos`
--

CREATE TABLE `combos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `baseprice` decimal(6,2) NOT NULL,
  `item_ids` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `combos`
--

INSERT INTO `combos` (`id`, `name`, `baseprice`, `item_ids`) VALUES
(1, 'Testing', '9.90', '1,1');

-- --------------------------------------------------------

--
-- Table structure for table `hours`
--

CREATE TABLE `hours` (
  `restaurant_id` int(11) NOT NULL,
  `0_open` smallint(6) NOT NULL,
  `0_close` smallint(6) NOT NULL,
  `1_open` smallint(6) NOT NULL,
  `1_close` smallint(6) NOT NULL,
  `2_open` smallint(6) NOT NULL,
  `2_close` smallint(6) NOT NULL,
  `3_open` smallint(6) NOT NULL,
  `3_close` smallint(6) NOT NULL,
  `4_open` smallint(6) NOT NULL,
  `4_close` smallint(6) NOT NULL,
  `5_open` smallint(6) NOT NULL,
  `5_close` smallint(6) NOT NULL,
  `6_open` smallint(6) NOT NULL,
  `6_close` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `hours`
--

INSERT INTO `hours` (`restaurant_id`, `0_open`, `0_close`, `1_open`, `1_close`, `2_open`, `2_close`, `3_open`, `3_close`, `4_open`, `4_close`, `5_open`, `5_close`, `6_open`, `6_close`) VALUES
(0, -1, -1, 1100, 2225, 1100, 2225, 1100, 2225, 1100, 2225, 1000, 2350, 1100, 2350);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) NOT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `toppings` tinyint(1) NOT NULL,
  `wings_sauce` tinyint(4) NOT NULL,
  `calories` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'for 2 items, separate with a /. For more, use a -',
  `allergens` varchar(1024) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `category_id`, `category`, `item`, `price`, `toppings`, `wings_sauce`, `calories`, `allergens`) VALUES
(1, 1, 'Pizza', 'Small Pizza', 4.95, 1, 0, '', ''),
(2, 1, 'Pizza', 'Medium Pizza', 5.75, 1, 0, '', ''),
(3, 1, 'Pizza', 'Large Pizza', 6.95, 1, 0, '', ''),
(4, 1, 'Pizza', 'X-Large Pizza', 9.95, 1, 0, '', ''),
(5, 1, 'Pizza', '2 Small Pizzas', 9.95, 2, 0, '', ''),
(6, 1, 'Pizza', '2 Medium Pizzas', 14.95, 2, 0, '', ''),
(7, 1, 'Pizza', '2 Large Pizzas', 16.95, 2, 0, '', ''),
(8, 1, 'Pizza', '2 X-Large Pizzas', 18.95, 2, 0, '', ''),
(9, 3, 'Dips', 'Tomato Dip', 0.7, 0, 0, '', ''),
(10, 3, 'Dips', 'Hot Dip', 0.7, 0, 0, '', ''),
(11, 3, 'Dips', 'Cheddar Dip', 0.7, 0, 0, '', ''),
(12, 3, 'Dips', 'Marinara Dip', 0.7, 0, 0, '', ''),
(13, 3, 'Dips', 'Ranch Dip', 0.7, 0, 0, '', ''),
(14, 3, 'Dips', 'Blue Cheese Dip', 0.7, 0, 0, '', ''),
(15, 4, 'Wings', '1 lb Wings', 6.99, 0, 1, '', ''),
(16, 4, 'Wings', '2 lb Wings', 12.99, 0, 2, '', ''),
(17, 4, 'Wings', '3 lb Wings', 17.99, 0, 3, '', ''),
(18, 4, 'Wings', '4 lb Wings', 24.99, 0, 4, '', ''),
(19, 4, 'Wings', '5 lb Wings', 28.99, 0, 5, '', ''),
(20, 5, 'Sides', 'Panzerotti', 5.99, 1, 0, '', ''),
(21, 5, 'Sides', 'Garlic Bread', 2.25, 0, 0, '', ''),
(22, 5, 'Sides', 'French Fries', 3.99, 0, 0, '', ''),
(23, 5, 'Sides', 'Potato Wedges', 3.99, 0, 0, '', ''),
(27, 5, 'Sides', 'Chicken Salad ', 5.99, 0, 0, '', ''),
(28, 5, 'Sides', 'Caesar Salad', 3.99, 0, 0, '', ''),
(29, 5, 'Sides', 'Garden Salad', 3.99, 0, 0, '', ''),
(32, 6, 'Drinks', 'Coca-Cola', 0.95, 0, 0, '', ''),
(33, 6, 'Drinks', 'Diet Coca-Cola', 0.95, 0, 0, '', ''),
(34, 6, 'Drinks', 'Pepsi', 0.95, 0, 0, '', ''),
(35, 6, 'Drinks', 'Diet Pepsi', 0.95, 0, 0, '', ''),
(36, 6, 'Drinks', 'Sprite', 0.95, 0, 0, '', ''),
(37, 6, 'Drinks', 'Crush Orange', 0.95, 0, 0, '', ''),
(38, 6, 'Drinks', 'Dr. Pepper', 0.95, 0, 0, '', ''),
(39, 6, 'Drinks', 'Ginger Ale', 0.95, 0, 0, '', ''),
(40, 6, 'Drinks', 'Nestea', 0.95, 0, 0, '', ''),
(41, 6, 'Drinks', 'Water Bottle', 0.95, 0, 0, '', ''),
(45, 6, 'Drinks', '2L Coca-Cola', 2.99, 0, 0, '', ''),
(46, 6, 'Drinks', '2L Sprite', 2.99, 0, 0, '', ''),
(47, 6, 'Drinks', '2L Brisk Iced Tea', 2.99, 0, 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `placed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `number` int(11) NOT NULL,
  `unit` varchar(16) NOT NULL,
  `buzzcode` varchar(32) NOT NULL,
  `street` varchar(255) NOT NULL,
  `postalcode` varchar(16) NOT NULL,
  `city` varchar(64) NOT NULL,
  `province` varchar(32) NOT NULL,
  `latitude` varchar(16) NOT NULL,
  `longitude` varchar(16) NOT NULL,
  `accepted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `restaurant_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `payment_type` tinyint(4) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `cell` varchar(16) NOT NULL,
  `paid` tinyint(4) NOT NULL,
  `stripeToken` varchar(64) NOT NULL,
  `deliverytime` varchar(64) NOT NULL,
  `cookingnotes` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `placed_at`, `number`, `unit`, `buzzcode`, `street`, `postalcode`, `city`, `province`, `latitude`, `longitude`, `accepted_at`, `restaurant_id`, `type`, `payment_type`, `phone`, `cell`, `paid`, `stripeToken`, `deliverytime`, `cookingnotes`, `status`, `price`, `email`) VALUES
(272, 1, '2018-03-20 18:30:49', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055123067', '', 1, '', 'Deliver Now', '', 0, '70.60', NULL),
(273, 1, '2018-03-20 18:39:37', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055123067', '', 1, '', 'Deliver Now', '', 0, '28.28', NULL),
(274, 74, '2018-03-06 18:57:11', 9554, '', '', 'Gold Creek Dr', 'N0L 1R0', 'Komoka', 'Ontario', '42.9601505', '-81.4849633', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, 'tok_CReGYyb2udYLeZ', 'Deliver Now', '', 0, '32.82', NULL),
(275, 74, '2018-03-06 19:00:56', 9554, '', '', 'Gold Creek Dr', 'N0L 1R0', 'Komoka', 'Ontario', '42.9601505', '-81.4849633', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, 'tok_CReKIcxKdnl3ep', 'Deliver Now', '', 0, '34.35', NULL),
(276, 1, '2018-03-20 18:31:13', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 3, 0, 0, '9055315332', '', 1, '', 'Deliver Now', '', 0, '25.93', NULL),
(277, 1, '2018-03-20 18:41:27', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 3, 0, 0, '9055315332', '', 1, '', 'Deliver Now', '', 0, '25.93', NULL),
(278, 1, '2018-03-20 18:41:41', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 3, 0, 0, '9055315331', '', 1, 'tok_CUcHyeI7umScRE', 'Deliver Now', '', 0, '25.93', NULL),
(279, 1, '2018-03-14 17:07:18', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 3, 0, 0, '9055315331', '', 1, 'tok_CUcIsWVursyMHW', 'Deliver Now', '', 0, '24.80', NULL),
(280, 1, '2018-03-20 14:59:29', 2396, '', '', 'Bloor St W', 'M6S 1P5', 'Toronto', 'Ontario', '43.6498421999999', '-79.482770599999', '0000-00-00 00:00:00', 3, 0, 0, '9055315331', '', 1, '', 'March 20 at 1145', '', 0, '144.78', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `presets`
--

CREATE TABLE `presets` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `toppings` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `presets`
--

INSERT INTO `presets` (`id`, `name`, `toppings`) VALUES
(1, 'hawaiian', 'pineapple bacon ham'),
(2, 'canadian', 'pepperoni mushrooms bacon'),
(3, 'deluxe', 'pepperoni mushrooms green peppers'),
(4, 'vegetarian', 'mushrooms tomatoes green peppers'),
(5, 'meat', 'sausage salami bacon pepperoni'),
(6, 'super', 'pepperoni mushrooms green peppers'),
(7, 'supreme', 'pepperoni mushrooms green peppers');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `cuisine` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_delivery` int(11) NOT NULL,
  `is_pickup` int(11) NOT NULL,
  `max_delivery_distance` int(11) NOT NULL,
  `delivery_fee` int(11) NOT NULL,
  `minimum` int(11) NOT NULL,
  `is_complete` int(11) NOT NULL,
  `lastorder_id` int(11) NOT NULL,
  `franchise` int(11) NOT NULL,
  `address_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `slug`, `email`, `phone`, `cuisine`, `website`, `description`, `logo`, `is_delivery`, `is_pickup`, `max_delivery_distance`, `delivery_fee`, `minimum`, `is_complete`, `lastorder_id`, `franchise`, `address_id`) VALUES
(2, 'INACTIVE Marvellous Pizza', '', '(519) 452-1044', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 2),
(3, 'Test Pizza Store', 'Quality Pizza & Wings', '(905) 573-8800', '9055315331', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 97),
(4, 'King Pizza', '', '(905) 662-6672', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 108),
(5, 'Royal Pizza & Wings', '', '(905) 664-8075', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 109),
(6, 'Pizza Inferno Eatery', '', '(905) 662-2210', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 110),
(7, 'Star Pizza', '', '(905) 664-4444 ', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 111),
(8, 'Super Torino\'s Pizza & Wings', '', '(905) 662-4636', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 112),
(9, 'Fruitland Pizza & Grill', '', '(289) 656-1666', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 113),
(10, 'Pizza Bell & Wings', '', '(905) 573-7333', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 114),
(11, 'Venice Beach Pizzeria\'s', '', '(905) 560-7888', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 115),
(12, 'Carlo\'s Pizza & Grill', '', '(905) 692-2756', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 116),
(13, 'D & A Pizza', '', '(905) 643-2255', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 117);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `keyname` varchar(255) NOT NULL,
  `value` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `keyname`, `value`) VALUES
(1, 'lastSQL', '1519159029'),
(20, 'orders', '1519239848'),
(24, 'menucache', '1521573702'),
(25, 'useraddresses', '1495910443'),
(37, 'users', '1495489938'),
(38, 'additional_toppings', '1521567842'),
(43, 'actions', '1519230780'),
(87, 'restaurants', '1489588141'),
(1398, 'shortage', '1493135529'),
(1537, 'combos', '1493826841'),
(1552, 'debugmode', '0'),
(1553, 'domenucache', '1'),
(1554, 'settings', '1521569174'),
(1560, 'onlyfiftycents', '1'),
(1579, 'deletetopping', '0'),
(1582, 'localhostdialing', '0'),
(1593, 'maxdistance_live', '5'),
(1594, 'maxdistance_local', '20'),
(1600, 'lastupdate', '1521575099782'),
(2316, 'headercolor', '#DC3545'),
(2454, 'myaddress', 'My Saved Address List'),
(2461, 'noaddresses', 'No Addresses Saved'),
(2467, 'mycreditcard', 'My Saved Credit Card List'),
(2470, 'nocreditcards', 'No Credit Cards'),
(2478, 'aboutus', 'Our Story');

-- --------------------------------------------------------

--
-- Table structure for table `shortage`
--

CREATE TABLE `shortage` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `tablename` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `toppings`
--

CREATE TABLE `toppings` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `isfree` tinyint(1) NOT NULL,
  `qualifiers` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'comma delimited list of the names for 1/2,x1,x2 if applicable',
  `isall` tinyint(4) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `toppings`
--

INSERT INTO `toppings` (`id`, `name`, `type`, `isfree`, `qualifiers`, `isall`, `groupid`) VALUES
(1, 'Anchovies', 'Meat', 0, '', 0, 0),
(2, 'Bacon', 'Meat', 0, '', 0, 0),
(3, 'Beef Salami', 'Meat', 0, '', 0, 0),
(4, 'Chicken', 'Meat', 0, '', 0, 0),
(5, 'Ground Beef', 'Meat', 0, '', 0, 0),
(6, 'Ham', 'Meat', 0, '', 0, 0),
(7, 'Hot Italian Sausage', 'Meat', 0, '', 0, 0),
(8, 'Hot Sausage', 'Meat', 0, '', 0, 0),
(9, 'Italian Sausage', 'Meat', 0, '', 0, 0),
(10, 'Mild Sausage', 'Meat', 0, '', 0, 0),
(11, 'Pepperoni', 'Meat', 0, '', 0, 0),
(12, 'Salami', 'Meat', 0, '', 0, 0),
(13, 'Artichoke Heart', 'Vegetable', 0, '', 0, 0),
(14, 'Black Olives', 'Vegetable', 0, '', 0, 0),
(15, 'Broccoli', 'Vegetable', 0, '', 0, 0),
(16, 'Green Olives', 'Vegetable', 0, '', 0, 0),
(17, 'Green Peppers', 'Vegetable', 0, '', 0, 0),
(18, 'Hot Banana Peppers', 'Vegetable', 0, '', 0, 0),
(19, 'Hot Peppers', 'Vegetable', 0, '', 0, 0),
(20, 'Jalapeno Peppers', 'Vegetable', 0, '', 0, 0),
(21, 'Mushrooms', 'Vegetable', 0, '', 0, 0),
(22, 'Onions', 'Vegetable', 0, '', 0, 0),
(23, 'Pineapple', 'Vegetable', 0, '', 0, 0),
(24, 'Red Onions', 'Vegetable', 0, '', 0, 0),
(25, 'Red Peppers', 'Vegetable', 0, '', 0, 0),
(26, 'Spinach', 'Vegetable', 0, '', 0, 0),
(27, 'Sundried Tomatoes', 'Vegetable', 0, '', 0, 0),
(28, 'Tomatoes', 'Vegetable', 0, '', 0, 0),
(29, 'Extra Cheese', 'Vegetable', 0, '', 0, 0),
(31, 'Well Done', 'zPreparation', 1, '', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `useraddresses`
--

CREATE TABLE `useraddresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `unit` varchar(32) NOT NULL,
  `buzzcode` varchar(16) NOT NULL,
  `street` varchar(255) NOT NULL,
  `postalcode` varchar(16) NOT NULL,
  `city` varchar(64) NOT NULL,
  `province` varchar(32) NOT NULL,
  `latitude` varchar(16) NOT NULL,
  `longitude` varchar(16) NOT NULL,
  `phone` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `useraddresses`
--

INSERT INTO `useraddresses` (`id`, `user_id`, `number`, `unit`, `buzzcode`, `street`, `postalcode`, `city`, `province`, `latitude`, `longitude`, `phone`) VALUES
(1, 3, 483, '', '', 'Dundas Street', 'N6B 1W4', 'London', 'Ontario', '42.9871816', '-81.2386115', ''),
(2, 4, 1565, '', '', 'Western Rd', 'N6G 1H5', 'London', 'Ontario', '43.0187', '-81.2812887', ''),
(3, 5, 483, '', '', 'Dundas Street', 'N6B 1W4', 'London', 'Ontario', '42.9871816', '-81.2386115', ''),
(4, 6, 1569, '', '', 'Oxford Street East', 'N5V 1W5', 'London', 'Ontario', '43.0109195', '-81.198983600000', ''),
(95, 48, 300, '', '', 'Dundas St', 'N6B 1T6', 'London', 'Ontario', '42.9854177', '-81.244139099999', ''),
(96, 49, 300, '', '', 'Dundas St', 'N6B 1T6', 'London', 'Ontario', '42.9854177', '-81.244139099999', ''),
(97, 50, 2372, '', '', 'Barton St E', 'L8E 2W7', 'Hamilton', 'Ontario', '43.2376467', '-79.765102399999', ''),
(103, 54, 18, 'side door', '', 'Oakland Dr', 'L8E 3Z2', 'Hamilton', 'Ontario', '43.2304400000000', '-79.7693198', ''),
(104, 55, 1001, '', '', 'Fanshawe College Blvd', 'N5V 2A5', 'London', 'Ontario', '43.013414', '-81.199466000000', ''),
(105, -1, 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', ''),
(106, 56, 18, '', '', 'Oakland Dr', 'L8E 3Z2', 'Hamilton', 'Ontario', '43.2304400000000', '-79.7693198', ''),
(107, 57, 2227, '', '', 'Wharncliffe Rd S', 'N6P 1K9', 'London', 'Ontario', '42.9137999999999', '-81.289999999999', ''),
(108, 60, 0, '', '', '44 King St E, Stoney Creek, ON L8G 1K1\r\n', '', '', '', '43.2162632', '-79.755773899999', ''),
(109, 61, 0, '', '', '826 Queenston Rd, Stoney Creek, ON L8G 4A8\r\n', '', '', '', '43.227685', '-79.762686399999', ''),
(110, 62, 0, '', '', '140 Highway No 8, Stoney Creek, ON L8G 1C2', '', '', '', '43.2236353', '-79.745122299999', ''),
(111, 63, 0, '', '', '450 Hamilton Regional Rd 8, Stoney Creek, ON L8G 1G6', '', '', '', '43.217317', '-79.7196697', ''),
(112, 64, 0, '', '', '521 Hamilton Regional Rd 8, Stoney Creek, ON L8G 1G4', '', '', '', '43.2160074', '-79.7132455', ''),
(113, 65, 0, '', '', '301 Fruitland Rd, Stoney Creek, ON L8E 5M1', '', '', '', '43.2222872', '-79.700723500000', ''),
(114, 66, 0, '', '', '96 Centennial Pkwy N #4, Hamilton, ON L8E 1H7', '', '', '', '43.231442', '-79.762328', ''),
(115, 67, 0, '', '', '1050 Paramount Dr, Stoney Creek, ON L8J 1P8', '', '', '', '43.1991887999999', '-79.7939896', ''),
(116, 68, 0, '', '', '48 Leckie Ave, Stoney Creek, ON L8J 2S7', '', '', '', '43.1827074', '-79.7818812', ''),
(117, 69, 0, '', '', '6-15 Lockport Way, Stoney Creek, ON L8E 0H8 ', '', '', '', '43.2180072', '-79.633158799999', ''),
(118, 70, 400, '123', '', 'Richmond St', 'N6A 3C7', 'London', 'Ontario', '42.9834744', '-81.249403499999', ''),
(120, 71, 300, '', '', 'Dundas St', 'N6B 1T6', 'London', 'Ontario', '42.9854177', '-81.244139099999', ''),
(121, 0, 2396, '', '', 'Sinclair Cir', 'L7P 3C3', 'Burlington', 'Ontario', '43.3658326', '-79.836305199999', ''),
(122, 72, 2396, '', '', 'Sinclair Cir', 'L7P 3C3', 'Burlington', 'Ontario', '43.3658326', '-79.836305199999', ''),
(123, 73, 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', ''),
(124, 74, 9554, '', '', 'Gold Creek Dr', 'N0L 1R0', 'Komoka', 'Ontario', '42.9601505', '-81.4849633', ''),
(125, 75, 2396, '', '', 'Bloor St W', 'M6S 1P5', 'Toronto', 'Ontario', '43.6498421999999', '-79.482770599999', ''),
(126, 1, 2396, '', '', 'Bloor St W', 'M6S 1P5', 'Toronto', 'Ontario', '43.6498421999999', '-79.482770599999', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `lastlogin` bigint(20) NOT NULL,
  `loginattempts` int(11) NOT NULL,
  `profiletype` tinyint(4) NOT NULL,
  `authcode` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `stripecustid` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `phone`, `lastlogin`, `loginattempts`, `profiletype`, `authcode`, `stripecustid`) VALUES
(1, 'Roy Wall', 'roy@trinoweb.com', '$2y$10$rKLl0XAZWkS1b1bxKHE6l.MitqKf2mCS5VlcxVPhTf1a7CUTJqgsO', '', '0000-00-00 00:00:00', '2018-03-20 19:19:33', '9055315331', 1487608084, 0, 1, '', 'cus_CMP8XVe641qT0g'),
(2, 'Roy Test', 'roy+test@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2016-11-16 20:20:28', '0000-00-00 00:00:00', '', 0, 0, 0, '', ''),
(4, 'Marvellous', 'info+mar@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-02-14 20:28:50', '0000-00-00 00:00:00', '', 0, 0, 2, '', ''),
(48, 'Van Trinh', 'info@trinoweb.com', '$2y$10$.0DQCK8l9YOr49mc3AcEr.8zemyiRmUa1j69p5MJO4vf6PCIAOip.', '', '2017-04-01 21:18:32', '2017-04-22 17:15:53', '', 1516924073, 1, 0, '', ''),
(50, 'Quality Pizza & Wings', 'odealyonline@gmail.com', '$2y$10$S5SNVTZgWk9Ufe.kLdtaMOOMo2VxRqkXUpv1k/af09Bn1c32UUrcq', '', '2017-04-01 22:20:18', '0000-00-00 00:00:00', '', 1494703615, 1, 2, '', ''),
(54, 'Van T.', 'dvt1985@hotmail.com', '$2y$10$.QT//bWNHJolITvSCTQnjuioE9U3tOyPIa030/vIfwduBXUXs8AkG', '', '2017-04-23 20:16:17', '2017-10-08 22:54:26', '(905) 531-5331', 1517449276, 7, 0, '', 'cus_AWrH95lAblBCVy'),
(60, 'King Pizza', '1', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(61, 'Royal Pizza & Wings', '2', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(62, 'Pizza Inferno Eatery', '3', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(63, 'Star Pizza', '4', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(64, 'Super Torino\'s Pizza & Wings', '5', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(65, 'Fruitland Pizza & Grill', '6', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(66, 'Pizza Bell & Wings', '7', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(67, 'Venice Beach Pizzeria\'s', '8', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(68, 'Carlo\'s Pizza & Grill', '9', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(69, 'D & A Pizza', '10', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, NULL, NULL, '', 0, 0, 2, '', ''),
(75, 'Tanya Myoko', 'roy+test23@trinoweb.com', '$2y$10$WzkzhZG5z0ZAJPxPZMfjj.ecNFwsgzrjQGONy187W/aDeC3OpbA/i', '', '2018-03-14 17:31:48', '0000-00-00 00:00:00', '', 0, 0, 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `wings_sauce`
--

CREATE TABLE `wings_sauce` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `isfree` tinyint(1) NOT NULL,
  `qualifiers` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'comma delimited list of the names for 1/2,x1,x2 if applicable',
  `isall` tinyint(4) NOT NULL DEFAULT '1',
  `groupid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wings_sauce`
--

INSERT INTO `wings_sauce` (`id`, `name`, `type`, `isfree`, `qualifiers`, `isall`, `groupid`) VALUES
(1, 'Honey Garlic', 'Sauce', 0, '', 1, 1),
(3, 'BBQ', 'Sauce', 0, '', 1, 1),
(4, 'Hot', 'Sauce', 0, '', 1, 1),
(5, 'Suicide', 'Sauce', 0, '', 1, 1),
(6, 'Sauce on Side', 'zPreparation', 1, '', 1, 2),
(7, 'Well Done', 'zPreparation', 1, '', 1, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `additional_toppings`
--
ALTER TABLE `additional_toppings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `combos`
--
ALTER TABLE `combos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hours`
--
ALTER TABLE `hours`
  ADD PRIMARY KEY (`restaurant_id`),
  ADD UNIQUE KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presets`
--
ALTER TABLE `presets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `keyname` (`keyname`);

--
-- Indexes for table `shortage`
--
ALTER TABLE `shortage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `toppings`
--
ALTER TABLE `toppings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `useraddresses`
--
ALTER TABLE `useraddresses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `wings_sauce`
--
ALTER TABLE `wings_sauce`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actions`
--
ALTER TABLE `actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `additional_toppings`
--
ALTER TABLE `additional_toppings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `combos`
--
ALTER TABLE `combos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=281;

--
-- AUTO_INCREMENT for table `presets`
--
ALTER TABLE `presets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2542;

--
-- AUTO_INCREMENT for table `shortage`
--
ALTER TABLE `shortage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `useraddresses`
--
ALTER TABLE `useraddresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
