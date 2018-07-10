/*
SQLyog Professional v12.5.1 (64 bit)
MySQL - 5.6.39 : Database - hamilton_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`hamilton_db` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `ai`;

/*Table structure for table `2_combos` */

DROP TABLE IF EXISTS `2_combos`;

CREATE TABLE `2_combos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `baseprice` decimal(6,2) NOT NULL,
  `item_ids` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `2_combos` */

/*Table structure for table `2_presets` */

DROP TABLE IF EXISTS `2_presets`;

CREATE TABLE `2_presets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `toppings` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `2_presets` */

/*Table structure for table `2_shortage` */

DROP TABLE IF EXISTS `2_shortage`;

CREATE TABLE `2_shortage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(11) NOT NULL,
  `tablename` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `2_shortage` */

/*Table structure for table `actions` */

DROP TABLE IF EXISTS `actions`;

CREATE TABLE `actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eventname` varchar(64) NOT NULL,
  `party` tinyint(4) NOT NULL COMMENT '0=user,1=admin,2=restaurant',
  `sms` tinyint(1) NOT NULL,
  `phone` tinyint(1) NOT NULL,
  `email` tinyint(1) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

/*Data for the table `actions` */

insert  into `actions`(`id`,`eventname`,`party`,`sms`,`phone`,`email`,`message`) values 
(1,'order_placed',2,0,0,1,'[sitename] - A new order was placed'),
(2,'order_placed',1,0,0,0,'[sitename] - A new order was placed [url]'),
(3,'order_placed',0,0,0,1,'[sitename] - Here is your receipt'),
(4,'order_declined',0,0,0,1,'[sitename] - Your order was cancelled: [reason]'),
(5,'order_declined',1,1,0,0,'[sitename] - An order was cancelled: [reason]'),
(6,'order_confirmed',1,1,0,0,'[sitename] - An order was approved: [reason]'),
(7,'user_registered',0,0,0,1,'[sitename] - Thank you for registering'),
(9,'user_registered',1,0,0,1,'[sitename] - Thank you for registering'),
(10,'order_placed',1,0,0,0,'[sitename] - A new order was placed [url]'),
(12,'cron_job',2,0,1,0,'Hello [name], you have [#] order[s] on [sitename] dot see eh, from [from], please press 1 to confirm, then check your email for the order details. This is the [attempt] attempt. [press9torepeat]'),
(13,'cron_job_final',1,0,1,0,'Hello [name], [restaurant] has [#] order[s] on [sitename] from [from], and never confirmed. Good bye. [press9torepeat]'),
(14,'press9torepeat',0,0,0,0,'Press 9 to repeat this message');

/*Table structure for table `additional_toppings` */

DROP TABLE IF EXISTS `additional_toppings`;

CREATE TABLE `additional_toppings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `additional_toppings` */

insert  into `additional_toppings`(`id`,`size`,`price`) values 
(1,'Small',0.9),
(2,'Medium',1),
(3,'Large',1.1),
(4,'X-Large',1.2),
(6,'Panzerotti',1),
(7,'Delivery',2),
(8,'Minimum',15),
(10,'DeliveryTime',45),
(12,'over$30',10),
(13,'over$50',15);

/*Table structure for table `hours` */

DROP TABLE IF EXISTS `hours`;

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
  `6_close` smallint(6) NOT NULL,
  PRIMARY KEY (`restaurant_id`),
  UNIQUE KEY `restaurant_id` (`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `hours` */

insert  into `hours`(`restaurant_id`,`0_open`,`0_close`,`1_open`,`1_close`,`2_open`,`2_close`,`3_open`,`3_close`,`4_open`,`4_close`,`5_open`,`5_close`,`6_open`,`6_close`) values 
(0,1100,300,1100,300,1100,300,1100,300,1100,2300,1000,2300,1100,2300);

/*Table structure for table `menu` */

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id` int(10) unsigned NOT NULL,
  `category_id` int(10) NOT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `toppings` tinyint(1) NOT NULL,
  `wings_sauce` tinyint(4) NOT NULL,
  `calories` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'for 2 items, separate with a /. For more, use a -',
  `allergens` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `menu` */

insert  into `menu`(`id`,`category_id`,`category`,`item`,`price`,`toppings`,`wings_sauce`,`calories`,`allergens`,`enabled`) values 
(1,1,'Pizza','Small Pizza',10,1,0,'','',1),
(2,1,'Pizza','Medium Pizza',12,1,0,'','',1),
(3,1,'Pizza','Large Pizza',15,1,0,'','',1),
(4,1,'Pizza','X-Large Pizza',17,1,0,'','',1),
(5,1,'Pizza','2 Small Pizzas',12,2,0,'','',1),
(6,1,'Pizza','2 Medium Pizzas',16,2,0,'','',1),
(7,1,'Pizza','2 Large Pizzas',18,2,0,'','',1),
(8,1,'Pizza','2 X-Large Pizzas',20,2,0,'','',1),
(10,3,'Dips','Garlic Dip',1,0,0,'','',1),
(11,3,'Dips','Blue Cheese Dip',1,0,0,'','',1),
(12,3,'Dips','Ranch Dip',1,0,0,'','',1),
(16,4,'Wings','1 Lb Wings',12,0,1,'','',1),
(17,4,'Wings','2 Lb Wings',20,0,2,'','',1),
(18,4,'Wings','3 Lb Wings',25,0,3,'','',1),
(19,4,'Wings','4 Lb Wings',30,0,4,'','',1),
(20,4,'Wings','5 Lb Wings',35,0,5,'','',1),
(21,5,'Sides','French Fries',4,0,0,'','',1),
(22,5,'Sides','Potato Wedges',4.5,0,0,'','',1),
(23,5,'Sides','Greek Salad',5,0,0,'','',1),
(27,5,'Sides','Garden Salad',5,0,0,'','',1),
(28,5,'Sides','Caesar Salad',5,0,0,'','',1),
(29,5,'Sides','Chicken Salad',6.5,0,0,'','',1),
(32,6,'Drinks','Coke',1.25,0,0,'','',1),
(34,6,'Drinks','Pepsi',1.25,0,0,'','',1),
(35,6,'Drinks','7 Up',1.25,0,0,'','',1),
(36,6,'Drinks','Sprite',1.25,0,0,'','',1),
(37,6,'Drinks','Orange',1.25,0,0,'','',1),
(38,6,'Drinks','Ginger Ale',1.25,0,0,'','',1),
(39,6,'Drinks','Root Beer',1.25,0,0,'','',1),
(40,6,'Drinks','Iced Tea',1.25,0,0,'','',1),
(41,6,'Drinks','Water',1.25,0,0,'','',1);

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `email` varchar(150) DEFAULT NULL,
  `attempts` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=376 DEFAULT CHARSET=latin1;

/*Data for the table `orders` */

insert  into `orders`(`id`,`user_id`,`placed_at`,`number`,`unit`,`buzzcode`,`street`,`postalcode`,`city`,`province`,`latitude`,`longitude`,`accepted_at`,`restaurant_id`,`type`,`payment_type`,`phone`,`cell`,`paid`,`stripeToken`,`deliverytime`,`cookingnotes`,`status`,`price`,`email`,`attempts`) values 
(373,187,'2018-07-08 12:58:21',18,'123','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304607999999','-79.769279400000','0000-00-00 00:00:00',26,0,0,'9055315331','',1,'ch_DBjpzmaICTZqkm','July 8 at 2345','',1,20.91,NULL,4),
(374,3,'2018-07-08 12:58:23',18,'','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304791','-79.769272699999','0000-00-00 00:00:00',81,0,0,'9055315331','',1,'ch_DC4HT4K6zItuFI','Deliver Now','',1,24.35,NULL,1),
(375,3,'2018-07-08 16:27:01',18,'','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304791','-79.769272699999','0000-00-00 00:00:00',81,0,0,'9055315331','',1,'ch_DC7eHQWhJ34cL5','July 9 at 2345','',0,16.39,NULL,4);

/*Table structure for table `restaurants` */

DROP TABLE IF EXISTS `restaurants`;

CREATE TABLE `restaurants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
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
  `address_id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `restaurants` */

insert  into `restaurants`(`id`,`name`,`slug`,`phone`,`cuisine`,`website`,`description`,`logo`,`is_delivery`,`is_pickup`,`max_delivery_distance`,`delivery_fee`,`minimum`,`is_complete`,`lastorder_id`,`franchise`,`address_id`,`email`) values 
(4,'Queens Pizza and Wings','','9055770900','','','','',1,0,0,0,0,0,0,0,4,NULL),
(8,'2 For 1 Pizza Wings and Subs','','9055495999','','','','',0,0,0,0,0,0,0,0,169,NULL),
(9,'Aberdeen Pizza & Wings','','9052965544','','','','',0,0,0,0,0,0,0,0,170,NULL),
(10,'Aceti\\\'s Pizzeria','','9055450484','','','','',0,0,0,0,0,0,0,0,171,NULL),
(11,'Andre\\\'s Pizza & Wings','','9053879292','','','','',0,0,0,0,0,0,0,0,172,NULL),
(12,'Basilique','','9055243444','','','','',0,0,0,0,0,0,0,0,173,NULL),
(13,'Bella Pizza - King St','','9055441444','','','','',0,0,0,0,0,0,0,0,174,NULL),
(14,'Bruno\\\'s Pizza & Wings (Main st)','','9057778888','','','','',0,0,0,0,0,0,0,0,175,NULL),
(15,'Bruno\\\'s Pizza and Wings','','9053854444','','','','',0,0,0,0,0,0,0,0,176,NULL),
(16,'Buona Vita Pizza Inc.','','9055771000','','','','',0,0,0,0,0,0,0,0,177,NULL),
(17,'Chicago Style Pizza','','9055758800','','','','',0,0,0,0,0,0,0,0,178,NULL),
(18,'City Pizza & Wings','','9055281111','','','','',0,0,0,0,0,0,0,0,179,NULL),
(19,'City Pizza And Shawarma','','9053181111','','','','',0,0,0,0,0,0,0,0,180,NULL),
(20,'Concession Pizza','','9053859888','','','','',0,0,0,0,0,0,0,0,181,NULL),
(21,'David\\\'s Pizza & Pastry','','2897550528','','','','',0,0,0,0,0,0,0,0,182,NULL),
(22,'Diamantino\\\'s Pizza','','9059729889','','','','',0,0,0,0,0,0,0,0,183,NULL),
(23,'Diana\\\'s Pizza & Grille','','9055497474','','','','',0,0,0,0,0,0,0,0,184,NULL),
(24,'Double Double Pizza & Chicken','','9055280000','','','','',0,0,0,0,0,0,0,0,185,NULL),
(25,'Express Pizza','','9055497171','','','','',0,0,0,0,0,0,0,0,186,NULL),
(26,'Famo Pizza And Wings','','9053870009','','','','',1,0,0,0,0,0,0,0,187,NULL),
(27,'Fire stone pizza and wings','','9055255444','','','','',0,0,0,0,0,0,0,0,188,NULL),
(28,'Firestone Pizza and Wings - Mountain','','9055751818','','','','',0,0,0,0,0,0,0,0,189,NULL),
(29,'First Choice Pizza','','9055609111','','','','',0,0,0,0,0,0,0,0,190,NULL),
(30,'Gage Pizza & Wings','','9055744343','','','','',0,0,0,0,0,0,0,0,191,NULL),
(31,'Garth Pizza & Wings','','9053883444','','','','',0,0,0,0,0,0,0,0,192,NULL),
(32,'Greenhill Pizza and Wings','','9055605051','','','','',0,0,0,0,0,0,0,0,193,NULL),
(33,'Hi-Line Centre Pizza & Wings','','9055457444','','','','',0,0,0,0,0,0,0,0,194,NULL),
(34,'Hi-Line Pizza & Wings','','9055439555','','','','',0,0,0,0,0,0,0,0,195,NULL),
(35,'Hot Stone Pizza and Shawarma','','9057778777','','','','',0,0,0,0,0,0,0,0,196,NULL),
(36,'It\\\'s Pizza Time','','9053889000','','','','',0,0,0,0,0,0,0,0,197,NULL),
(37,'Joni\\\'s Pizza','','9053871212','','','','',0,0,0,0,0,0,0,0,198,NULL),
(38,'Knead Pizza','','2893896969','','','','',0,0,0,0,0,0,0,0,199,NULL),
(39,'L&C PIZZA','','9053128585','','','','',0,0,0,0,0,0,0,0,200,NULL),
(40,'Lava Pizza','','9057770666','','','','',0,0,0,0,0,0,0,0,201,NULL),
(41,'Lava Pizza & Wings (Fennell)','','9055746666','','','','',0,0,0,0,0,0,0,0,202,NULL),
(42,'Lava Pizza & Wings (King)','','9055256606','','','','',0,0,0,0,0,0,0,0,203,NULL),
(43,'Lazio Pizza & Wings','','9055478787','','','','',0,0,0,0,0,0,0,0,204,NULL),
(44,'Le Bella Pizza','','9055297627','','','','',0,0,0,0,0,0,0,0,205,NULL),
(45,'Limeridge Pizza & Subs','','9053887700','','','','',0,0,0,0,0,0,0,0,206,NULL),
(46,'Limeridge Pizza & Wings','','9055757979','','','','',0,0,0,0,0,0,0,0,207,NULL),
(47,'Marino Pizza And Wings','','9055742020','','','','',0,0,0,0,0,0,0,0,208,NULL),
(48,'Mario\\\'s 2 For 1 Pizza','','9055444440','','','','',0,0,0,0,0,0,0,0,209,NULL),
(49,'Mario\\\'s Pizza & Wings','','9053837777','','','','',0,0,0,0,0,0,0,0,210,NULL),
(50,'Mattina Pizzeria','','9055277918','','','','',0,0,0,0,0,0,0,0,211,NULL),
(51,'NàRoma','','9055256699','','','','',0,0,0,0,0,0,0,0,212,NULL),
(52,'National Pizza & Wings','','9055754500','','','','',0,0,0,0,0,0,0,0,213,NULL),
(53,'National Pizza and Wings','','9055497734','','','','',0,0,0,0,0,0,0,0,214,NULL),
(54,'National Pizza Place','','9055280404','','','','',0,0,0,0,0,0,0,0,215,NULL),
(55,'Niva Pizza & Wings','','9055444544','','','','',0,0,0,0,0,0,0,0,216,NULL),
(56,'Papa Pauly\\\'s Pizza','','9055444224','','','','',0,0,0,0,0,0,0,0,217,NULL),
(57,'Pisa Pizza','','9055475777','','','','',0,0,0,0,0,0,0,0,218,NULL),
(58,'Pita Pizza','','9055294444','','','','',0,0,0,0,0,0,0,0,219,NULL),
(59,'Pizza Bell & Wings','','9055737333','','','','',0,0,0,0,0,0,0,0,220,NULL),
(60,'Pizza Depot','','9055259711','','','','',0,0,0,0,0,0,0,0,221,NULL),
(61,'Pizza Inferno','','9055746060','','','','',0,0,0,0,0,0,0,0,222,NULL),
(62,'Pizza Italia','','9055443333','','','','',0,0,0,0,0,0,0,0,223,NULL),
(63,'Pizza World','','9055231111','','','','',0,0,0,0,0,0,0,0,224,NULL),
(64,'Pizza Yolo','','9055478988','','','','',0,0,0,0,0,0,0,0,225,NULL),
(65,'Pizzeria Gurman','','9055784077','','','','',0,0,0,0,0,0,0,0,226,NULL),
(66,'Plaza Pizza & Wings','','9055220909','','','','',0,0,0,0,0,0,0,0,227,NULL),
(67,'Poppi\\\'s Pizzeria & Grill','','9056929779','','','','',0,0,0,0,0,0,0,0,228,NULL),
(68,'Quality Pizza & Wings','','9055738800','','','','',0,0,0,0,0,0,0,0,229,NULL),
(69,'Queens Pizza & Wings (King St E)','','9055450101','','','','',0,0,0,0,0,0,0,0,230,NULL),
(70,'Romano\\\'s Ristorante','','9053876041','','','','',0,0,0,0,0,0,0,0,231,NULL),
(71,'Royal Pizza & Wings (James)','','9055294242','','','','',0,0,0,0,0,0,0,0,232,NULL),
(72,'Royal Pizza & Wings (Queenston)','','9056648585','','','','',0,0,0,0,0,0,0,0,233,NULL),
(73,'Rymal Pizza-Wings','','9053871800','','','','',0,0,0,0,0,0,0,0,234,NULL),
(74,'Sasso','','9055264848','','','','',0,0,0,0,0,0,0,0,235,NULL),
(75,'Select Food Mart and Pizza','','9055230285','','','','',0,0,0,0,0,0,0,0,236,NULL),
(76,'Super Pizza & Wings','','9055453888','','','','',0,0,0,0,0,0,0,0,237,NULL),
(77,'Supreme Pizza','','9053181221','','','','',0,0,0,0,0,0,0,0,238,NULL),
(78,'Tasty Pizza','','9053888882','','','','',0,0,0,0,0,0,0,0,239,NULL),
(79,'The Golden Pizza','','9053851161','','','','',0,0,0,0,0,0,0,0,240,NULL),
(80,'The Pizza House','','9055610220','','','','',0,0,0,0,0,0,0,0,241,NULL),
(81,'Van\\\'s Pizza Shop','','9055315331','','','','',1,0,0,0,0,0,0,0,242,NULL),
(82,'Venice Beach Pizza & Wings','','9055389001','','','','',0,0,0,0,0,0,0,0,243,NULL),
(83,'Weston Pizza And Wings','','9055219919','','','','',0,0,0,0,0,0,0,0,244,NULL);

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyname` varchar(255) NOT NULL,
  `value` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyname` (`keyname`)
) ENGINE=InnoDB AUTO_INCREMENT=1727 DEFAULT CHARSET=latin1;

/*Data for the table `settings` */

insert  into `settings`(`id`,`keyname`,`value`) values 
(1,'lastSQL','1522895167'),
(20,'orders','1530589874'),
(24,'menucache','1531080996'),
(25,'useraddresses','1525563470'),
(37,'users','1530669300'),
(38,'additional_toppings','1530669448'),
(43,'actions','1525270887'),
(87,'restaurants','1530647192'),
(1398,'shortage','1493135529'),
(1537,'combos','1493826841'),
(1552,'debugmode','0'),
(1553,'domenucache','1'),
(1554,'settings','1530669484'),
(1560,'onlyfiftycents','1'),
(1579,'deletetopping','0'),
(1582,'localhostdialing','0'),
(1593,'maxdistance_live','9'),
(1594,'maxdistance_local','100'),
(1600,'lastupdate','1521575099782'),
(1715,'max_attempts','3');

/*Table structure for table `toppings` */

DROP TABLE IF EXISTS `toppings`;

CREATE TABLE `toppings` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `isfree` tinyint(1) NOT NULL,
  `qualifiers` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'comma delimited list of the names for 1/2,x1,x2 if applicable',
  `isall` tinyint(4) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL,
  `enabled` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `toppings` */

insert  into `toppings`(`id`,`name`,`type`,`isfree`,`qualifiers`,`isall`,`groupid`,`enabled`) values 
(1,'Anchovies','Meat',0,'',0,0,1),
(2,'Bacon','Meat',0,'',0,0,1),
(3,'Salami','Meat',0,'',0,0,1),
(4,'Chicken','Meat',0,'',0,0,1),
(5,'Ground Beef','Meat',0,'',0,0,1),
(6,'Ham','Meat',0,'',0,0,1),
(7,'Hot Italian Sausage','Meat',0,'',0,0,1),
(8,'Hot Sausage','Meat',0,'',0,0,0),
(9,'Italian Sausage','Meat',0,'',0,0,1),
(10,'Mild Sausage','Meat',0,'',0,0,0),
(11,'Pepperoni','Meat',0,'',0,0,1),
(14,'Black Olives','Vegetable',0,'',0,0,1),
(15,'Broccoli','Vegetable',0,'',0,0,1),
(16,'Green Olives','Vegetable',0,'',0,0,1),
(17,'Green Peppers','Vegetable',0,'',0,0,1),
(18,'Hot Banana Peppers','Vegetable',0,'',0,0,0),
(19,'Hot Peppers','Vegetable',0,'',0,0,1),
(21,'Mushrooms','Vegetable',0,'',0,0,1),
(22,'Onions','Vegetable',0,'',0,0,1),
(23,'Pineapple','Vegetable',0,'',0,0,1),
(25,'Red Peppers','Vegetable',0,'',0,0,1),
(26,'Spinach','Vegetable',0,'',0,0,1),
(27,'Sundried Tomatoes','Vegetable',0,'',0,0,1),
(28,'Tomatoes','Vegetable',0,'',0,0,1),
(29,'Extra Cheese','Vegetable',0,'',0,0,1),
(31,'Well Done','zPreparation',1,'',1,1,0);

/*Table structure for table `useraddresses` */

DROP TABLE IF EXISTS `useraddresses`;

CREATE TABLE `useraddresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=latin1;

/*Data for the table `useraddresses` */

insert  into `useraddresses`(`id`,`user_id`,`number`,`unit`,`buzzcode`,`street`,`postalcode`,`city`,`province`,`latitude`,`longitude`) values 
(4,4,178,'','','Queen St S','L8P 3S7','Hamilton','Ontario','43.253782','-79.881281'),
(5,5,18,'','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304791','-79.769272699999'),
(6,6,1000,'','','Upper Gage Ave','L8V 4R5','Hamilton','Ontario','43.213566','-79.845911'),
(7,7,576,'','','Concession St','L8V 1A8','Hamilton','Ontario','43.240718','-79.851417'),
(152,102,123,'','','King St','L0S 1J0','Niagara-on-the-Lake','Ontario','43.2566571','-79.069487500000'),
(153,102,18,'','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304791','-79.769272699999'),
(154,103,51,'','','Fenton Way','L6P 0P4','Brampton','Ontario','43.799186','-79.708743000000'),
(155,1,18,'','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304791','-79.769272699999'),
(156,104,725,'','','Acadia Dr','L8W 3V2','Hamilton','Ontario','43.199438','-79.8710931'),
(157,105,18,'','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304791','-79.769272699999'),
(159,103,177,'','','Queen St S','L8P 4V6','Hamilton','Ontario','43.2536279','-79.8808065'),
(160,3,18,'','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304791','-79.769272699999'),
(161,107,22,'buzzer 2','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304949','-79.769447899999'),
(162,108,2000,'','','Main St W','L8S 4M8','Hamilton','Ontario','43.2473181','-79.9435709'),
(164,109,18,'','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304791','-79.769272699999'),
(165,110,725,'','','Acadia Dr','','Hamilton','Ontario','43.2003938999999','-79.8637124'),
(168,103,575,'','','Concession St','L8V 1B2','Hamilton','Ontario','43.2408371000000','-79.850595'),
(169,111,158,'','','Kenilworth Ave N','L8H 4R9','Hamilton','ON','43.2451','-79.807621'),
(170,112,411,'','','Aberdeen Ave','L8P 2S1','Hamilton','ON','43.250612','-79.892334'),
(171,113,1491,'','','Main St E','L8K 1E1','Hamilton','ON','43.239734','-79.806014'),
(172,114,402,'','','Concession St','L9A 1B7','Hamilton','ON','43.242502','-79.858711'),
(173,115,1065,'','','King St W','L8S 1L8','Hamilton','ON','43.260939','-79.907413'),
(174,116,2145,'','','King St E','L8K 1W5','Hamilton','ON','43.231169','-79.801783'),
(175,117,718,'','','Main St E','L8M 1K9','Hamilton','ON','43.247728','-79.840791'),
(176,118,872,'','','Upper Sherman Ave','L8V 3N1','Hamilton','ON','43.220492','-79.852674'),
(177,119,823,'','','Queen St N #','L8R 2V4','Hamilton','ON','43.262188','-79.877163'),
(178,120,534,'','','Upper Sherman Ave','L8V 3M1','Hamilton','ON','43.232092','-79.847526'),
(179,121,581,'','','Main St E','L8M 1H9','Hamilton','ON','43.250039','-79.847778'),
(180,122,1575,'','','Upper Ottawa St','L8W 3E2','Hamilton','ON','43.190611','-79.842588'),
(181,123,576,'','','Concession St','L8V 1A8','Hamilton','ON','43.240718','-79.851417'),
(182,124,866,'','','Mohawk Road East','L8T 2R5','Hamilton','ON','43.216421','-79.833738'),
(183,125,341,'','','Barton St E','L8L 2X6','Hamilton','ON','43.260542','-79.851003'),
(184,126,263,'','','Kenilworth N','L8H 4S6','Hamilton','ON','43.248304','-79.806775'),
(185,127,225,'','','John St S','L8N 2C7','Hamilton','ON','43.249929','-79.868472'),
(186,128,1357,'','','Main St E','L8K 1B6','Hamilton','ON','43.241102','-79.812035'),
(187,129,1000,'','','Upper Gage Ave','L8V 4R5','Hamilton','ON','43.213636','-79.846218'),
(188,130,54,'','','Queen St S','L8P 3R5','Hamilton','ON','43.257995','-79.879748'),
(189,131,536,'','','Upper Wellington St','L9A 3P5','Hamilton','ON','43.239613','-79.866587'),
(190,132,2596,'','','King St E','L8K 1Y2','Hamilton','ON','43.223138','-79.783267'),
(191,133,804,'','','Upper Gage Ave','L8V 4K4','Hamilton','ON','43.219877','-79.842994'),
(192,134,1300,'','','Garth St','L9C 5Z4','Hamilton','ON','43.220512','-79.90729'),
(193,135,399,'','','Greenhill Ave','L8K 6N5','Hamilton','ON','43.216917','-79.796359'),
(194,136,1150,'','','King St E','L8M 1E8','Hamilton','ON','43.247723','-79.829096'),
(195,137,236,'','','Parkdale Ave N','L8H 5X5','Hamilton','ON','43.242867','-79.788833'),
(196,138,524,'','','Barton St E','L8L 2Y8','Hamilton','ON','43.258294','-79.843899'),
(197,139,5057,'','','Rymal Rd E Unit #A','L8W 1B3','Hamilton','ON','43.197387','-79.870736'),
(198,140,525,'','','Mohawk Rd E','L8V 2J5','Hamilton','ON','43.2215','-79.851407'),
(199,141,274,'','','James St N','L8R 2L3','Hamilton','ON','43.263351','-79.865974'),
(200,142,314,'','','Queenston Rd','L8K 1H5','Hamilton','ON','43.233612','-79.7906'),
(201,143,387,'','','Barton St E','L8L 2Y2','Hamilton','ON','43.26014','-79.848745'),
(202,144,550,'','','Fennell Avenue E','L8V 5S9','Hamilton','ON','43.231454','-79.856733'),
(203,145,876,'','','King St W','L8S 4S6','Hamilton','ON','43.263337','-79.900821'),
(204,146,1022,'','','Barton St E','L8L 3E1','Hamilton','ON','43.251898','-79.818925'),
(205,147,455,'','','Barton St E','L8L 2Y6','Hamilton','ON','43.259382','-79.846141'),
(206,148,310,'','','Limeridge Rd W','L9C 2V2','Hamilton','ON','43.224051','-79.898583'),
(207,149,990,'','','Upper Wentworth St','L9A 4V9','Hamilton','ON','43.217451','-79.865661'),
(208,150,601,'','','Upper James St','L9C 2Y7','Hamilton','ON','43.239667','-79.876519'),
(209,151,1016,'','','King St E','L8M 1C8','Hamilton','ON','43.250338','-79.83319'),
(210,152,171,'','','Mohawk Rd E','L9A 2H4','Hamilton','ON','43.22676','-79.872346'),
(211,153,104,'','','Cannon St E','L8L 2A3','Hamilton','ON','43.259594','-79.862903'),
(212,154,215,'','','Locke St S','L8P 4B6','Hamilton','ON','43.254386','-79.885835'),
(213,155,870,'','','Upper James St','L9C 7N1','Hamilton','ON','43.227159','-79.883025'),
(214,156,134,'','','Ottawa St N','L8H 3Z3','Hamilton','ON','43.246283','-79.817746'),
(215,157,3,'','','King St E','L8N 1A1','Hamilton','ON','43.256784','-79.86884'),
(216,158,800,'','','Barton St E','L8L 3B3','Hamilton','ON','43.254796','-79.830697'),
(217,159,997,'','','Cannon St E','L8L 6Z9','Hamilton','ON','43.25003','-79.82742'),
(218,160,55,'','','Parkdale Ave N','L8H 5W7','Hamilton','ON','43.237717','-79.791914'),
(219,161,526,'','','Main St E','L8M 1J1','Hamilton','ON','43.249844','-79.849616'),
(220,162,964,'','','Centennial Pkwy N #','L8E 1H7','Hamilton','ON','43.231442','-79.762328'),
(221,163,160,'','','Centennial Parkway North','L8E 1H9','Hamilton','ON','43.233793','-79.761065'),
(222,164,1394,'','','Upper Gage Ave','L8W 1E7','Hamilton','ON','43.201144','-79.851054'),
(223,165,1443,'','','Main St E','L8K 1C4','Hamilton','ON','43.240263','-79.808668'),
(224,166,447,'','','Main St E','L8N 1K1','Hamilton','ON','43.251274','-79.851994'),
(225,167,1229,'','','Cannon St E','L8H 1T8','Hamilton','ON','43.247442','-79.816637'),
(226,168,125,'','','Gailmont Dr','L8K 4B4','Hamilton','ON','43.22412','-79.783222'),
(227,169,90,'','','Wellington St N','L8R 1N1','Hamilton','ON','43.255938','-79.857727'),
(228,170,141812,'','','- Rymal Rd E','L8W 3N3','Hamilton','ON','43.1826','-79.819462'),
(229,171,2372,'','','Barton St E','L8E 2W7','Hamilton','ON','43.237647','-79.765102'),
(230,172,838,'','','King St E','L8M 1B4','Hamilton','ON','43.25113','-79.841235'),
(231,173,1275,'','','Rymal Rd E','L8W 3N1','Hamilton','ON','43.18941','-79.83659'),
(232,174,180,'','','James St S Hamilton','L8P 4V1','Hamilton','ON','43.251242','-79.871714'),
(233,175,826,'','','Queenston Rd','L8G 4A8','Stoney Creek','ON','43.227274','-79.76287'),
(234,176,1001,'','','Rymal Rd E','L8W 3M2','Hamilton','ON','43.192122','-79.848494'),
(235,177,1595,'','','Upper James St','L9B 1K2','Hamilton','ON','43.203119','-79.891194'),
(236,178,303,'','','York Blvd','L8R 3K5','Hamilton','ON','43.262846','-79.877807'),
(237,179,1531,'','','Barton St E','L8H 2X5','Hamilton','ON','43.247254','-79.797731'),
(238,180,1216,'','','Upper Wentworth St','L9A 4W2','Hamilton','ON','43.21037','-79.867954'),
(239,181,1119,'','','Fennell Ave E','L8T 1S2','Hamilton','ON','43.226065','-79.827836'),
(240,182,1140,'','','Fennell Ave E','L8T 1S5','Hamilton','ON','43.225078','-79.828512'),
(241,183,205,'','','Quigley Rd','L8K 5M8','Hamilton','ON','43.216221','-79.783788'),
(242,184,18,'','','Oakland Dr',' L8E3Z2','Hamilton','ON','43.230479','-79.769273'),
(243,185,770,'','','Mohawk Rd W','L9C 1X9','Hamilton','ON','43.234115','-79.920786'),
(244,186,574,'','','James St N','L8L 1J7','Hamilton','ON','43.272101','-79.862116'),
(245,187,18,'123','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304607999999','-79.769279400000'),
(246,188,18,'123','','Oakland Dr','L8E 3Z2','Hamilton','Ontario','43.2304607999999','-79.769279400000');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `stripecustid` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`password`,`remember_token`,`created_at`,`updated_at`,`phone`,`lastlogin`,`loginattempts`,`profiletype`,`authcode`,`stripecustid`) values 
(1,'Admin','roy@trinoweb.com','$2y$10$rKLl0XAZWkS1b1bxKHE6l.MitqKf2mCS5VlcxVPhTf1a7CUTJqgsO','','0000-00-00 00:00:00','2018-04-10 14:11:34','9055315331',1487608084,0,1,'','cus_CXD47wjKLO8QVm'),
(3,'Van Trinh','info@trinoweb.com','$2y$10$.0DQCK8l9YOr49mc3AcEr.8zemyiRmUa1j69p5MJO4vf6PCIAOip.','','2017-04-01 21:18:32','2018-04-08 23:22:01','9055315331',1522026647,0,0,'','cus_CWyLzacP0LTU6M'),
(4,'Queens Pizza and Wings','queenspizza905@gmail.com','$2y$10$S5SNVTZgWk9Ufe.kLdtaMOOMo2VxRqkXUpv1k/af09Bn1c32UUrcq','','2017-04-01 22:20:18','0000-00-00 00:00:00','',1494703615,0,2,'',''),
(103,'Rony','ronysgu@gmail.com','$2y$10$hGj3TyjDtLvguh4ZVmEZyub1S6XYD4ejG48jlFZEpK01KTu8EGsYy','','2018-04-09 08:49:16','0000-00-00 00:00:00','6472417339',0,0,0,'','cus_Ci6WI4lrTrEMFy'),
(104,'Tommy','tran.tommy@me.com','$2y$10$cq7UMm7u45GvDxWChGM1CuX55ryn3pWMzOiomjFUlXC9kyzozFdpK','','2018-04-14 13:18:39','0000-00-00 00:00:00','',0,0,0,'',''),
(110,'Minh','ngocminh89@hotmail.com','$2y$10$J/g962xTz9K8A.2Mqy0au.CjmdCLEmWQYOccH6u5aN3NHXYKZjiSi','','2018-05-15 14:12:44','0000-00-00 00:00:00','9055188531',0,0,0,'','cus_CrtZYEtysdYFRa'),
(111,'2 For 1 Pizza Wings and Subs','roy+2For1PizzaWingsandSubs@trinoweb.com','$2y$10$6ybzWzEdaC3jI8NcRCt/xuAw.ROO7aKkCnBuYARRsawRmTaaH1Nt6',NULL,'2018-07-03 15:46:17','0000-00-00 00:00:00','',0,0,2,'',''),
(112,'Aberdeen Pizza & Wings','roy+AberdeenPizzaWings@trinoweb.com','$2y$10$roufRsFrOmztRD4cBMvZ5OTYqwB.OcCAZCakQO./nw8MM.XbOpdTC',NULL,'2018-07-03 15:47:07','0000-00-00 00:00:00','',0,0,2,'',''),
(113,'Aceti\\\'s Pizzeria','roy+AcetisPizzeria@trinoweb.com','$2y$10$XVXTGABcSUszoZtPNnvtnu1j1shHauk3J6zDfL3hdx8MkMyCbN6UO',NULL,'2018-07-03 15:47:22','0000-00-00 00:00:00','',0,0,2,'',''),
(114,'Andre\\\'s Pizza & Wings','roy+AndresPizzaWings@trinoweb.com','$2y$10$yOVvQnX..t/yZT/JSVlwae64pi18iwI3jskZfpTRJzXXUUK2RKTUm',NULL,'2018-07-03 15:47:28','0000-00-00 00:00:00','',0,0,2,'',''),
(115,'Basilique','roy+Basilique@trinoweb.com','$2y$10$CNMQ182RtjEen2n/k7v/5eqD0Ihc2/JvKNxWK.BDxDky3n7kLX/6i',NULL,'2018-07-03 15:47:31','0000-00-00 00:00:00','',0,0,2,'',''),
(116,'Bella Pizza - King St','roy+BellaPizza-KingSt@trinoweb.com','$2y$10$piydl.TJDBSK3U20LTqGdeRZF.USmu3uvpss/g9se3eiLQCxlc93e',NULL,'2018-07-03 15:47:35','0000-00-00 00:00:00','',0,0,2,'',''),
(117,'Bruno\\\'s Pizza & Wings (Main st)','roy+BrunosPizzaWings(Mainst)@trinoweb.com','$2y$10$M/4Gp6PcO5DaWqfqQ4ECk.YuH0.iHFJgIaIht9MFBOx4hf.ngzc8i',NULL,'2018-07-03 15:47:40','0000-00-00 00:00:00','',0,0,2,'',''),
(118,'Bruno\\\'s Pizza and Wings','roy+BrunosPizzaandWings@trinoweb.com','$2y$10$ekXZr15COYttQnPgk72VYO/uStwvza6UqUr71KgmMuEfmlzo3OQyu',NULL,'2018-07-03 15:47:44','0000-00-00 00:00:00','',0,0,2,'',''),
(119,'Buona Vita Pizza Inc.','roy+BuonaVitaPizzaInc.@trinoweb.com','$2y$10$biVzVaxi/IHKPjUT1udqsuPUscO6OEtTP9K4wvJhHgcsf7vqBQM8i',NULL,'2018-07-03 15:47:57','0000-00-00 00:00:00','',0,0,2,'',''),
(120,'Chicago Style Pizza','roy+ChicagoStylePizza@trinoweb.com','$2y$10$2XLVibfuN6e2nlv04Sl3WuBRNKsb56wmGrjweYn7NICp5gY75Ydjy',NULL,'2018-07-03 15:48:00','0000-00-00 00:00:00','',0,0,2,'',''),
(121,'City Pizza & Wings','roy+CityPizzaWings@trinoweb.com','$2y$10$.RX979WgzM7bx1DMJD/9COtVyrxP/dtFPtuH6bR6A7qQVfpbu5EVi',NULL,'2018-07-03 15:48:05','0000-00-00 00:00:00','',0,0,2,'',''),
(122,'City Pizza And Shawarma','roy+CityPizzaAndShawarma@trinoweb.com','$2y$10$WSfsYdfNNXW6lFKNsSeKTuutbbxZpKIQ204r9/RlWrK6ZE1VZFtfa',NULL,'2018-07-03 15:48:12','0000-00-00 00:00:00','',0,0,2,'',''),
(123,'Concession Pizza','roy+ConcessionPizza@trinoweb.com','$2y$10$S1vYUM/VQl8GtT.QmWLSE.TkW5AUMO9jQY2OjHa0J5FgcWVvWVPSa',NULL,'2018-07-03 15:48:17','0000-00-00 00:00:00','',0,0,2,'',''),
(124,'David\\\'s Pizza & Pastry','roy+DavidsPizzaPastry@trinoweb.com','$2y$10$q9lYkJ7Qd9VCV/llJ7KNr.v54B65nsLz6jD8rwG5cUQJNxbcK4bO.',NULL,'2018-07-03 15:48:20','0000-00-00 00:00:00','',0,0,2,'',''),
(125,'Diamantino\\\'s Pizza','roy+DiamantinosPizza@trinoweb.com','$2y$10$hP34/OdSiFTTOrDmvIpfROmVVumisLZktIzocQReaAdaOdNorSyHS',NULL,'2018-07-03 15:48:24','0000-00-00 00:00:00','',0,0,2,'',''),
(126,'Diana\\\'s Pizza & Grille','roy+DianasPizzaGrille@trinoweb.com','$2y$10$1zyEsKeu7FirjwjDhbl6Q.Qe26LPB4uXnABeVqErRABkRVo.k49DG',NULL,'2018-07-03 15:48:28','0000-00-00 00:00:00','',0,0,2,'',''),
(127,'Double Double Pizza & Chicken','roy+DoubleDoublePizzaChicken@trinoweb.com','$2y$10$QGocqKsanfXCJU6zyyHj..1maiwQxxREDJxK5R6I76lCquvEcbi7W',NULL,'2018-07-03 15:48:32','0000-00-00 00:00:00','',0,0,2,'',''),
(128,'Express Pizza','roy+ExpressPizza@trinoweb.com','$2y$10$ti0IqBnA4BIRGx0N9KujvOPdYcxi1r4On1e3W8oJVCATRdeSIxzJO',NULL,'2018-07-03 15:48:39','0000-00-00 00:00:00','',0,0,2,'',''),
(129,'Famo Pizza And Wings','famopizza@gmail.com','$2y$10$unaI9A2HJkAoYddhY/IIOeYdtSg6UyxjHzg/T2mGNPL9lxoW7LhhO',NULL,'2018-07-03 15:48:43','0000-00-00 00:00:00','',0,0,2,'',''),
(130,'Fire stone pizza and wings','roy+Firestonepizzaandwings@trinoweb.com','$2y$10$cnq/h6wpCWvOC81I7RDAu.b6BnrOWNGbfIZ.ZIV9XBQmaVTHcKQFy',NULL,'2018-07-03 15:48:48','0000-00-00 00:00:00','',0,0,2,'',''),
(131,'Firestone Pizza and Wings - Mountain','roy+FirestonePizzaandWings-Mountain@trinoweb.com','$2y$10$YM8ovBxOq2EUp6g3BZDAYOg12MqfFLd4FbzgEjtVvPevgRUohGbWS',NULL,'2018-07-03 15:48:52','0000-00-00 00:00:00','',0,0,2,'',''),
(132,'First Choice Pizza','roy+FirstChoicePizza@trinoweb.com','$2y$10$4i1223tszkjcUT5r6xGgOe6KLozuzCEq0qBqGnGQUrouVAm2zSRCG',NULL,'2018-07-03 15:48:56','0000-00-00 00:00:00','',0,0,2,'',''),
(133,'Gage Pizza & Wings','roy+GagePizzaWings@trinoweb.com','$2y$10$sO3OJrCCY8qvDVZg1nOb/.6LLee6cN3va5NVmQx.wnQihIs9xNz82',NULL,'2018-07-03 15:48:59','0000-00-00 00:00:00','',0,0,2,'',''),
(134,'Garth Pizza & Wings','roy+GarthPizzaWings@trinoweb.com','$2y$10$irVn6.cDviufZrn/qvMex.2K3Xyvg6vIVOtaqQDUtLDZKfuDYvscm',NULL,'2018-07-03 15:49:03','0000-00-00 00:00:00','',0,0,2,'',''),
(135,'Greenhill Pizza and Wings','roy+GreenhillPizzaandWings@trinoweb.com','$2y$10$xtwYVoX3gtfvt2rLOx9w/OX.IrNROnY19Mj8ZyIMUuUCWtVgnNoM6',NULL,'2018-07-03 15:49:08','0000-00-00 00:00:00','',0,0,2,'',''),
(136,'Hi-Line Centre Pizza & Wings (Offical)','roy+Hi-LineCentrePizzaWings(Offical)@trinoweb.com','$2y$10$v1Q8XqB6ZYbcSOoPZcwzU.KyJLhMlxTugwA1SDC8n4XEPBwM8DzeW',NULL,'2018-07-03 15:49:12','0000-00-00 00:00:00','',0,0,2,'',''),
(137,'Hi-Line Pizza & Wings','roy+Hi-LinePizzaWings@trinoweb.com','$2y$10$p62TPDeQ6J8TSR3T7pfmn.uIgfSgqRvrpR921viEvIJjwmpBMlF/.',NULL,'2018-07-03 15:49:19','0000-00-00 00:00:00','',0,0,2,'',''),
(138,'Hot Stone Pizza and Shawarma','roy+HotStonePizzaandShawarma@trinoweb.com','$2y$10$aKu5SmtndWIja4w60dMFieQgNRxdVVaphEcoztyp66Q3nRIGG2iji',NULL,'2018-07-03 15:49:22','0000-00-00 00:00:00','',0,0,2,'',''),
(139,'It\\\'s Pizza Time','roy+ItsPizzaTime@trinoweb.com','$2y$10$k32TqSH/R15lUY7GsoF/rOe0Am0wFfBRebDjjLWj4724fFP2DhtPS',NULL,'2018-07-03 15:49:27','0000-00-00 00:00:00','',0,0,2,'',''),
(140,'Joni\\\'s Pizza','roy+JonisPizza@trinoweb.com','$2y$10$fOJPmyn9Dw5pwkye.q/Pw.zHdTZsOt6v6FWj0EDoNfvTeVigBc1dW',NULL,'2018-07-03 15:49:31','0000-00-00 00:00:00','',0,0,2,'',''),
(141,'Knead Pizza','roy+KneadPizza@trinoweb.com','$2y$10$4meQiNLyojkNwW1srXANS.Qx1YOXCFuJyHrPlPSwIZ.TM1VP.jtqa',NULL,'2018-07-03 15:49:34','0000-00-00 00:00:00','',0,0,2,'',''),
(142,'L&C PIZZA','roy+LCPIZZA@trinoweb.com','$2y$10$Hm0ZxO7Iex7QtyHHP4Her.i7UaL4x6YxerfjpxaJGYuPrzmO/qadG',NULL,'2018-07-03 15:49:38','0000-00-00 00:00:00','',0,0,2,'',''),
(143,'Lava Pizza','roy+LavaPizza@trinoweb.com','$2y$10$LwFq6/FFEIa5bl7vU2C9muZpbEkXtYvNYAJOXbtlfzCBCtkJFFm72',NULL,'2018-07-03 15:49:45','0000-00-00 00:00:00','',0,0,2,'',''),
(144,'Lava Pizza & Wings (Fennell)','roy+LavaPizzaWings(Fennell)@trinoweb.com','$2y$10$dIJnK14zUMIry5KuUKUje.b5IS5Sq2y69vRJigk9ljAC.9vWoe86G',NULL,'2018-07-03 15:49:49','0000-00-00 00:00:00','',0,0,2,'',''),
(145,'Lava Pizza & Wings (King)','roy+LavaPizzaWings(King)@trinoweb.com','$2y$10$0iRAaC4RRqaQiUdIH/8H/uNHqDOD.FIbyvtISiRmkAMq9WTlAgAHy',NULL,'2018-07-03 15:49:54','0000-00-00 00:00:00','',0,0,2,'',''),
(146,'Lazio Pizza & Wings','roy+LazioPizzaWings@trinoweb.com','$2y$10$lmZOg6VLXti6ROd/A2TIHOVSbD8ILFY4Y7jawkrntsprcuUiSByuO',NULL,'2018-07-03 15:49:59','0000-00-00 00:00:00','',0,0,2,'',''),
(147,'Le Bella Pizza','roy+LeBellaPizza@trinoweb.com','$2y$10$Mum7YuI9XhjkEtAeFVOTieB2Z5zxldnkswyX1F0l5IJFCj6wAM.Ea',NULL,'2018-07-03 15:50:03','0000-00-00 00:00:00','',0,0,2,'',''),
(148,'Limeridge Pizza & Subs','roy+LimeridgePizzaSubs@trinoweb.com','$2y$10$devOIButAjPHpapTCEJFKeSjyW5Ad4Ed/vk8XWmKw1x6jHthAZMl2',NULL,'2018-07-03 15:50:09','0000-00-00 00:00:00','',0,0,2,'',''),
(149,'Limeridge Pizza & Wings','roy+LimeridgePizzaWings@trinoweb.com','$2y$10$oYls4Dh6rawF439i.9kavevFUA6ijW2T/H.wwTj0Vj7Dk0R4Jow2.',NULL,'2018-07-03 15:50:12','0000-00-00 00:00:00','',0,0,2,'',''),
(150,'Marino Pizza And Wings','roy+MarinoPizzaAndWings@trinoweb.com','$2y$10$QAe35Qo3B9PNLkTUuHAtauxGR92nDQ0uH6Hv11I4CojZ92BFXYLRm',NULL,'2018-07-03 15:50:15','0000-00-00 00:00:00','',0,0,2,'',''),
(151,'Mario\\\'s 2 For 1 Pizza','roy+Marios2For1Pizza@trinoweb.com','$2y$10$9Puw626/pOSfyedWM/G6lea8LGtlkamxV6DCpwZ239DGEoWbEULD6',NULL,'2018-07-03 15:50:19','0000-00-00 00:00:00','',0,0,2,'',''),
(152,'Mario\\\'s Pizza & Wings','roy+MariosPizzaWings@trinoweb.com','$2y$10$OoFhlqqZmS394fbOUa5SuesKtOnuPnCiYT8rTWAOj7W2NVASNO6gK',NULL,'2018-07-03 15:50:23','0000-00-00 00:00:00','',0,0,2,'',''),
(153,'Mattina Pizzeria','roy+MattinaPizzeria@trinoweb.com','$2y$10$3jLMxVI8xRk48/Bj.X8PN.Hh0UNDYfUGt8VxnMMR8Mv403BYpag1.',NULL,'2018-07-03 15:50:39','0000-00-00 00:00:00','',0,0,2,'',''),
(154,'NàRoma','roy+NaRoma@trinoweb.com','$2y$10$EjxY3A5BX5YhGbO/uiDdY.I2QCW9RywA1HClqUhJ7K2XgKrKVsNVS',NULL,'2018-07-03 15:50:43','0000-00-00 00:00:00','',0,0,2,'',''),
(155,'National Pizza & Wings','roy+NationalPizzaWings@trinoweb.com','$2y$10$9MQkIRTl3FqOI6C6TnsTGegF.pAQOdc8utMWPZCVa7aIBkMWK1Ptq',NULL,'2018-07-03 15:50:48','0000-00-00 00:00:00','',0,0,2,'',''),
(156,'National Pizza and Wings','roy+NationalPizzaandWings@trinoweb.com','$2y$10$5KdLI7yFJoKyJQzeHR8H0.cyXaZfMGLxZxuhfZueD5gbyD.BGaZai',NULL,'2018-07-03 15:50:51','0000-00-00 00:00:00','',0,0,2,'',''),
(157,'National Pizza Place','roy+NationalPizzaPlace@trinoweb.com','$2y$10$dYW8iYa1Ko7M4AdwNkTaVOPfCaxCSAFBoOLiVWqs2R8vAL7NuZesK',NULL,'2018-07-03 15:50:55','0000-00-00 00:00:00','',0,0,2,'',''),
(158,'Niva Pizza & Wings','roy+NivaPizzaWings@trinoweb.com','$2y$10$oMRuKo/FJlbt3HecCJ.KCuhGXUdRz0zvoJiZ9W5E/VHzJT/B5p71G',NULL,'2018-07-03 15:51:03','0000-00-00 00:00:00','',0,0,2,'',''),
(159,'Papa Pauly\\\'s Pizza','roy+PapaPaulysPizza@trinoweb.com','$2y$10$cz/OBbhl6DsP5hDXcDOc4.6Xbl4RSNg17ph1IfxoAb8o7uV4ZXHcO',NULL,'2018-07-03 15:51:08','0000-00-00 00:00:00','',0,0,2,'',''),
(160,'Pisa Pizza','roy+PisaPizza@trinoweb.com','$2y$10$TtZcf7e88SRB4z4aLQx4Fu8MFA7.TnofB8uUyzMIdl4b2JGZ8bp6C',NULL,'2018-07-03 15:51:14','0000-00-00 00:00:00','',0,0,2,'',''),
(161,'Pita Pizza','roy+PitaPizza@trinoweb.com','$2y$10$pagihZfXKrPdsaJ1RTP3yeBRQ8LQGfG5iipUUuZsEpMOodKd/uCky',NULL,'2018-07-03 15:51:17','0000-00-00 00:00:00','',0,0,2,'',''),
(162,'Pizza Bell & Wings','roy+PizzaBellWings@trinoweb.com','$2y$10$L1G6witrUX.p68X3XpepQuxNn4RRLcJjC9QaYQl8xPzIjjqbj7hye',NULL,'2018-07-03 15:51:21','0000-00-00 00:00:00','',0,0,2,'',''),
(163,'Pizza Depot','roy+PizzaDepot@trinoweb.com','$2y$10$MuTOpQvrp.6qofHXBBquX.dpq.fm7VDeQhMgj6QVEmR78Tg7Dtb5u',NULL,'2018-07-03 15:51:24','0000-00-00 00:00:00','',0,0,2,'',''),
(164,'Pizza Inferno','roy+PizzaInferno@trinoweb.com','$2y$10$jNFNo292xRcndf8RffMvQuPjbnZMRyvfmGqKIN3do2cUGfrZPNCHq',NULL,'2018-07-03 15:51:28','0000-00-00 00:00:00','',0,0,2,'',''),
(165,'Pizza Italia','roy+PizzaItalia@trinoweb.com','$2y$10$klV1GTYjrOQi134XOnNVAOkR/46ppHV6VpD9.lLJX/gM2P8K2Kqvy',NULL,'2018-07-03 15:51:35','0000-00-00 00:00:00','',0,0,2,'',''),
(166,'Pizza World','roy+PizzaWorld@trinoweb.com','$2y$10$PgxXoFTDCbG.jcpDEILBpeg4ywE4mLu/HHWM5cYlHMpC4Jva2WX0S',NULL,'2018-07-03 15:51:39','0000-00-00 00:00:00','',0,0,2,'',''),
(167,'Pizza Yolo','roy+PizzaYolo@trinoweb.com','$2y$10$s5pKvmtUeRKd7hS2lwskZOa9.d1hOw6KwUyHxO0TLfu/rhzowViDy',NULL,'2018-07-03 15:51:43','0000-00-00 00:00:00','',0,0,2,'',''),
(168,'Pizzeria Gurman','roy+PizzeriaGurman@trinoweb.com','$2y$10$5yYiTJ35y4cAfLdxlj5hpOqMYfcUqhRuje4WCJFNKwZEQ/iq3Uv66',NULL,'2018-07-03 15:51:49','0000-00-00 00:00:00','',0,0,2,'',''),
(169,'Plaza Pizza & Wings','roy+PlazaPizzaWings@trinoweb.com','$2y$10$Xi558t8U0Ub8iH9Oc3QEoOCVmQdEYtVxwRkeeyMwSQUmneJRqX7da',NULL,'2018-07-03 15:51:54','0000-00-00 00:00:00','',0,0,2,'',''),
(170,'Poppi\\\'s Pizzeria & Grill','roy+PoppisPizzeriaGrill@trinoweb.com','$2y$10$ldaYIbJPicfpPLWgSXn7suuecBt93pk5jzbxLjUMOCB.QPNtWtb6q',NULL,'2018-07-03 15:51:58','0000-00-00 00:00:00','',0,0,2,'',''),
(171,'Quality Pizza & Wings','roy+QualityPizzaWings@trinoweb.com','$2y$10$ZMDZuNvYaHVT4CAc.QauxuD6IGoy541JilZITXOKR8.f.MtFTzUYC',NULL,'2018-07-03 15:52:04','0000-00-00 00:00:00','',0,0,2,'',''),
(172,'Queens Pizza & Wings (King St E)','roy+QueensPizzaWings(KingStE)@trinoweb.com','$2y$10$HxmsZM76i4ycCvA40ipNNuMY6YNBZVaDRucXovOgOZ5huVKnK1eA6',NULL,'2018-07-03 15:52:16','0000-00-00 00:00:00','',0,0,2,'',''),
(173,'Romano\\\'s Ristorante','roy+RomanosRistorante@trinoweb.com','$2y$10$KiMmj0s4.gW1oEI1ZpQ3duSZNLklSlPTsm5ScGF9s/zWNFPi3DEbC',NULL,'2018-07-03 15:52:19','0000-00-00 00:00:00','',0,0,2,'',''),
(174,'Royal Pizza & Wings (James)','roy+RoyalPizzaWings(James)@trinoweb.com','$2y$10$7Ox8jhVNLs2O1MrUYV.oG.S5I5Jtta1drSiWeSGc0bsq/9LOqLc1e',NULL,'2018-07-03 15:52:25','0000-00-00 00:00:00','',0,0,2,'',''),
(175,'Royal Pizza & Wings (Queenston)','roy+RoyalPizzaWings(Queenston)@trinoweb.com','$2y$10$pOZqh2KcVnimI9LIbqqNh.Kl2HwrvENWWWfnML9RNmZYEbPEz11vG',NULL,'2018-07-03 15:52:29','0000-00-00 00:00:00','',0,0,2,'',''),
(176,'Rymal Pizza-Wings','roy+RymalPizza-Wings@trinoweb.com','$2y$10$jgD777XX3pR5VrlEnmwQK.NQ7bKvrqnPzVIvxsABwQJZVHz.JLRk2',NULL,'2018-07-03 15:52:37','0000-00-00 00:00:00','',0,0,2,'',''),
(177,'Sasso','roy+Sasso@trinoweb.com','$2y$10$vE6CYQDyei5XhroQ0rUPt.QhvrTN0jp/f.WsBPXz/iSwJdCNp9HN2',NULL,'2018-07-03 15:52:44','0000-00-00 00:00:00','',0,0,2,'',''),
(178,'Select Food Mart and Pizza','roy+SelectFoodMartandPizza@trinoweb.com','$2y$10$dFmBmXWXRlxngoPTQfa7L.I35VEKpFOkON.mgfjMb0j98GjRiFoNe',NULL,'2018-07-03 15:52:47','0000-00-00 00:00:00','',0,0,2,'',''),
(179,'Super Pizza & Wings','roy+SuperPizzaWings@trinoweb.com','$2y$10$fRDncQeFfGhtoWuwcj1Pru7HadXUfg1LFrxzi6UVVrh0af7juntl6',NULL,'2018-07-03 15:52:55','0000-00-00 00:00:00','',0,0,2,'',''),
(180,'Supreme Pizza','roy+SupremePizza@trinoweb.com','$2y$10$.7RBBqXmXRGHMk3A2KWiiuxRzGYU5fFDE4cdk7WnbkppJyXp2rlfS',NULL,'2018-07-03 15:53:00','0000-00-00 00:00:00','',0,0,2,'',''),
(181,'Tasty Pizza','roy+TastyPizza@trinoweb.com','$2y$10$nsa49eyc0p4UMMnjCELR4uz40LJkPkOFDvuZv/AsWQXIAjEncvPMG',NULL,'2018-07-03 15:53:03','0000-00-00 00:00:00','',0,0,2,'',''),
(182,'The Golden Pizza','roy+TheGoldenPizza@trinoweb.com','$2y$10$7k6wZCGU2TpBxbqU4CXtCe0ETwmW8uyN2nMo0D5f.4uPHDBAJ4eai',NULL,'2018-07-03 15:53:06','0000-00-00 00:00:00','',0,0,2,'',''),
(183,'The Pizza House','roy+ThePizzaHouse@trinoweb.com','$2y$10$JnCrKvSiURNMISpsyiAlPO6bMuZIC0gGCzUmHR9aGrxjukN75NGuW',NULL,'2018-07-03 15:53:10','0000-00-00 00:00:00','',0,0,2,'',''),
(184,'Van\\\'s Pizza Shop','dvt1985@hotmail.com','$2y$10$ai737xYpa4iuDVQV1QG4W.QQOxGnYot/ksAkFkLnUXQ/.1.kwGUr.',NULL,'2018-07-03 15:53:15','0000-00-00 00:00:00','',0,0,2,'',''),
(185,'Venice Beach Pizza & Wings','roy+VeniceBeachPizzaWings@trinoweb.com','$2y$10$oIi1x72yqTCnIqQm2diV5.S3tr8XJyNgPMVy7ytz6QNo..Kd7m/26',NULL,'2018-07-03 15:53:18','0000-00-00 00:00:00','',0,0,2,'',''),
(186,'Weston Pizza And Wings','roy+WestonPizzaAndWings@trinoweb.com','$2y$10$9Y3DEehS/yziDtlgtGbpq.Fzu4bCyc/9Jw8TbN.7Vag6rxw0Vw9qK',NULL,'2018-07-03 15:53:22','0000-00-00 00:00:00','',0,0,2,'',''),
(187,'Van Trinh Two','info+1@trinoweb.com','$2y$10$lkbNQPu8RsZQYPTn/P6sW.wEoMXwj4D42W8tkKrKWYn5sRNB3MG9y','','2018-07-07 15:41:07','0000-00-00 00:00:00','9055315331',1531057215,1,0,'','cus_DBjp2Ttf1OOrbG'),
(188,'Van','info+2@trinoweb.com','$2y$10$zNyGITpMtfXNVXOkINLCN.raoVCP6QJy8aSUngZaSVJO4wsNnT/SC','','2018-07-07 22:09:13','0000-00-00 00:00:00','',0,0,0,'','');

/*Table structure for table `wings_sauce` */

DROP TABLE IF EXISTS `wings_sauce`;

CREATE TABLE `wings_sauce` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `isfree` tinyint(1) NOT NULL,
  `qualifiers` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'comma delimited list of the names for 1/2,x1,x2 if applicable',
  `isall` tinyint(4) NOT NULL DEFAULT '1',
  `groupid` int(11) NOT NULL,
  `enabled` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `wings_sauce` */

insert  into `wings_sauce`(`id`,`name`,`type`,`isfree`,`qualifiers`,`isall`,`groupid`,`enabled`) values 
(1,'Honey Garlic','Sauce',0,'',1,1,1),
(3,'Mild','Sauce',0,'',1,1,1),
(4,'Medium','Sauce',0,'',1,1,1),
(5,'Hot','Sauce',0,'',1,1,1),
(6,'Extra Hot','Sauce',0,'',1,1,1),
(7,'Sauce on Side','zPreparation',1,'',1,2,1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
