-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 02, 2019 at 01:34 PM
-- Server version: 5.6.41
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ronal720_freephotos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(3) NOT NULL,
  `cat_title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_title`) VALUES
(1, 'Commercial'),
(2, 'Landscape'),
(3, 'Street');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_id` int(11) NOT NULL,
  `image_cat_title` varchar(255) NOT NULL,
  `standard_name` varchar(255) NOT NULL,
  `thumbnail_name` varchar(255) NOT NULL,
  `image_type` varchar(255) NOT NULL,
  `image_post_date` date NOT NULL,
  `image_tags` varchar(255) NOT NULL,
  `image_likes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`image_id`, `image_cat_title`, `standard_name`, `thumbnail_name`, `image_type`, `image_post_date`, `image_tags`, `image_likes`) VALUES
(1, 'Commercial', 'standard_mojito.jpg', 'thumbnail_mojito.jpg', 'image/jpeg', '2018-06-16', 'mojito, refreshment, cold drink', 0),
(2, 'Commercial', 'standard_finger_football.jpg', 'thumbnail_finger_football.jpg', 'image/jpeg', '2018-06-16', 'finger football, table game, board game', 0),
(3, 'Landscape', 'standard_seaside.jpg', 'thumbnail_seaside.jpg', 'image/jpeg', '2018-06-16', 'seaside, beach, black and white, seascape', 0),
(4, 'Street', 'standard_street_performers.jpg', 'thumbnail_street_performers.jpg', 'image/jpeg', '2018-06-16', 'street performer, Grafton street , Dublin', 0),
(5, 'Commercial', 'standard_model_house.jpg', 'thumbnail_model_house.jpg', 'image/jpeg', '2018-06-16', 'house model, colored pencils', 0),
(6, 'Landscape', 'standard_beach.jpg', 'thumbnail_beach.jpg', 'image/jpeg', '2018-06-16', 'seascape, beach, black and white', 0),
(7, 'Street', 'standard_playing_with_bubbles.jpg', 'thumbnail_playing_with_bubbles.jpg', 'image/jpeg', '2018-06-16', 'street performer, Grafton street, Dublin', 0),
(8, 'Commercial', 'standard_guitar.jpg', 'thumbnail_guitar.jpg', 'image/jpeg', '2018-06-16', 'guitar, acoustic guitar', 1),
(9, 'Landscape', 'standard_steps.jpg', 'thumbnail_steps.jpg', 'image/jpeg', '2018-06-16', 'seascape, beach, steps, black and white', 0),
(10, 'Street', 'standard_statue_men.jpg', 'thumbnail_statue_men.jpg', 'image/jpeg', '2018-06-16', 'street performer, Grafton street, Dublin', 0),
(11, 'Commercial', 'standard_perfume.jpg', 'thumbnail_perfume.jpg', 'image/jpeg', '2018-06-16', 'flower, perfume', 0),
(12, 'Landscape', 'standard_seaview1.jpg', 'thumbnail_seaview1.jpg', 'image/jpeg', '2018-06-16', 'seascape, beach, black and white', 1),
(13, 'Street', 'standard_st.stephens_park.jpg', 'thumbnail_st.stephens_park.jpg', 'image/jpeg', '2018-06-16', 'park, summer, St. Stephen Green, Dublin', 0),
(14, 'Commercial', 'standard_colored_pencils.jpg', 'thumbnail_colored_pencils.jpg', 'image/jpeg', '2018-06-16', 'shape, abstract, colors, pencils', 0),
(15, 'Landscape', 'standard_sand_pattern.jpg', 'thumbnail_sand_pattern.jpg', 'image/jpeg', '2018-06-16', 'beach, sand pattern, black and white', 1),
(16, 'Street', 'standard_bear_hug.jpg', 'thumbnail_bear_hug.jpg', 'image/jpeg', '2018-06-16', 'hug, bear, bear hug', 0),
(17, 'Commercial', 'standard_tablet_and_headphone.jpg', 'thumbnail_tablet_and_headphone.jpg', 'image/jpeg', '2018-06-16', 'bedroom, headset, tablet,', 1),
(18, 'Landscape', 'standard_seaview.jpg', 'thumbnail_seaview.jpg', 'image/jpeg', '2018-06-16', 'seascape, beach, Howth, black and white', 0),
(19, 'Commercial', 'standard_bowling_ball_and_pin.jpg', 'thumbnail_bowling_ball_and_pin.jpg', 'image/jpeg', '2018-06-16', 'bowling, bowling pin', 1),
(20, 'Landscape', 'standard_beach1.jpg', 'thumbnail_beach1.jpg', 'image/jpeg', '2018-06-16', 'seaside, beach, seascape, black and white', 1),
(21, 'Commercial', 'standard_beer.jpg', 'thumbnail_beer.jpg', 'image/jpeg', '2018-06-16', 'beer, bottle, cold drink', 2),
(22, 'Landscape', 'standard_seascape.jpg', 'thumbnail_seascape.jpg', 'image/jpeg', '2018-06-16', 'seascape, black and white', 1),
(23, 'Commercial', 'standard_orange_juice.jpg', 'thumbnail_orange_juice.jpg', 'image/jpeg', '2018-06-16', 'orange juice, cold drink, refreshment', 3),
(24, 'Landscape', 'standard_seaview2.jpg', 'thumbnail_seaview2.jpg', 'image/jpeg', '2018-06-16', 'seascape, seaside, black and white', 1),
(25, 'Landscape', 'standard_howth_lighthouse.jpg', 'thumbnail_howth_lighthouse.jpg', 'image/jpeg', '2018-06-16', 'seascape, Howth, seaside, light house', 1),
(26, 'Commercial', 'standard_donuts.jpg', 'thumbnail_donuts.jpg', 'image/jpeg', '2018-06-16', 'doughnuts, cake, pastry', 1),
(27, 'Landscape', 'standard_seaview3.jpg', 'thumbnail_seaview3.jpg', 'image/jpeg', '2018-06-16', 'seascape, black and white', 1),
(28, 'Commercial', 'standard_strawberries.jpg', 'thumbnail_strawberries.jpg', 'image/jpeg', '2018-06-16', 'strawberry, fruits, berries', 2),
(29, 'Landscape', 'standard_lighthouse.jpg', 'thumbnail_lighthouse.jpg', 'image/jpeg', '2018-06-16', 'light house, seascape, black and white', 2),
(30, 'Landscape', 'standard_foggy_lake.jpg', 'thumbnail_foggy_lake.jpg', 'image/jpeg', '2018-06-16', 'landscape, lake, fog, black and white', 1);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`like_id`, `user_id`, `image_id`) VALUES
(34, 71, 29),
(35, 71, 28),
(36, 72, 15),
(37, 72, 20),
(38, 71, 24),
(39, 71, 21),
(40, 71, 17),
(41, 71, 8),
(42, 73, 29),
(43, 73, 28),
(44, 73, 25),
(45, 73, 23);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_lastname` varchar(255) NOT NULL,
  `user_image` text NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` text NOT NULL,
  `user_role` varchar(255) NOT NULL,
  `time_online` int(11) NOT NULL,
  `code_one` text NOT NULL,
  `code_two` text NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_firstname`, `user_lastname`, `user_image`, `user_email`, `user_password`, `user_role`, `time_online`, `code_one`, `code_two`, `active`) VALUES
(71, 'Ronalyn', 'Rieza', '', 'rrieza88@gmail.com', '$2y$12$M93rdmFbRW9I8QebB3oGh.MEJdqkPTJwHKOZy7ReuBYqa1Bvm0Xd.', 'Admin', 0, '6de8557b0700db0a4fb18f036581aa41', '0', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
