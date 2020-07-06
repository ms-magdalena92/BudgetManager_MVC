-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 05 Lip 2020, 15:17
-- Wersja serwera: 10.4.11-MariaDB
-- Wersja PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `my_budget`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `default_expense_categories`
--

CREATE TABLE `default_expense_categories` (
  `category_id` int(11) NOT NULL,
  `expense_category` varchar(50) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `default_expense_categories`
--

INSERT INTO `default_expense_categories` (`category_id`, `expense_category`) VALUES
(1, 'food'),
(2, 'house'),
(3, 'transport'),
(4, 'telecom'),
(5, 'healthcare'),
(6, 'clothing'),
(7, 'hygiene'),
(8, 'kids'),
(9, 'entertainment'),
(10, 'trip'),
(11, 'trainings'),
(12, 'books'),
(13, 'savings'),
(14, 'old age pension'),
(15, 'debt repayment'),
(16, 'donation'),
(17, 'other');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `default_income_categories`
--

CREATE TABLE `default_income_categories` (
  `category_id` int(11) NOT NULL,
  `income_category` varchar(50) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `default_income_categories`
--

INSERT INTO `default_income_categories` (`category_id`, `income_category`) VALUES
(1, 'salary'),
(2, 'bank interest'),
(3, 'vending'),
(4, 'other');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `default_payment_methods`
--

CREATE TABLE `default_payment_methods` (
  `payment_method_id` int(11) NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `default_payment_methods`
--

INSERT INTO `default_payment_methods` (`payment_method_id`, `payment_method`) VALUES
(1, 'cash'),
(2, 'debit card'),
(3, 'credit card');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expense_amount` decimal(8,2) NOT NULL,
  `expense_date` date NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `expense_comment` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `expense_categories`
--

CREATE TABLE `expense_categories` (
  `category_id` int(11) NOT NULL,
  `expense_category` varchar(50) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `expense_categories`
--

INSERT INTO `expense_categories` (`category_id`, `expense_category`) VALUES
(1, 'food'),
(2, 'house'),
(3, 'transport'),
(4, 'telecom'),
(5, 'healthcare'),
(6, 'clothing'),
(7, 'hygiene'),
(8, 'kids'),
(9, 'entertainment'),
(10, 'trip'),
(11, 'trainings'),
(12, 'books'),
(13, 'savings'),
(14, 'old age pension'),
(15, 'debt repayment'),
(16, 'donation'),
(17, 'other');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `incomes`
--

CREATE TABLE `incomes` (
  `income_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `income_amount` decimal(8,2) NOT NULL,
  `income_date` date NOT NULL,
  `category_id` int(11) NOT NULL,
  `income_comment` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `income_categories`
--

CREATE TABLE `income_categories` (
  `category_id` int(11) NOT NULL,
  `income_category` varchar(50) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `income_categories`
--

INSERT INTO `income_categories` (`category_id`, `income_category`) VALUES
(1, 'salary'),
(2, 'bank interest'),
(3, 'vending'),
(4, 'other');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payment_methods`
--

CREATE TABLE `payment_methods` (
  `payment_method_id` int(11) NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `payment_methods`
--

INSERT INTO `payment_methods` (`payment_method_id`, `payment_method`) VALUES
(1, 'cash'),
(2, 'debit card'),
(3, 'credit card');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `remembered_logins`
--

CREATE TABLE `remembered_logins` (
  `token_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) NOT NULL,
  `expiry_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `password_reset_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `password_reset_expirity` datetime DEFAULT NULL,
  `activation_hash_token` varchar(64) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_expense_category`
--

CREATE TABLE `user_expense_category` (
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_income_category`
--

CREATE TABLE `user_income_category` (
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_payment_method`
--

CREATE TABLE `user_payment_method` (
  `user_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `default_expense_categories`
--
ALTER TABLE `default_expense_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeksy dla tabeli `default_income_categories`
--
ALTER TABLE `default_income_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeksy dla tabeli `default_payment_methods`
--
ALTER TABLE `default_payment_methods`
  ADD PRIMARY KEY (`payment_method_id`);

--
-- Indeksy dla tabeli `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payment_method_id` (`payment_method_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeksy dla tabeli `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeksy dla tabeli `incomes`
--
ALTER TABLE `incomes`
  ADD PRIMARY KEY (`income_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `income_categories`
--
ALTER TABLE `income_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeksy dla tabeli `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_method_id`);

--
-- Indeksy dla tabeli `remembered_logins`
--
ALTER TABLE `remembered_logins`
  ADD PRIMARY KEY (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `password_reset_hash` (`password_reset_hash`),
  ADD UNIQUE KEY `activation_hash_token` (`activation_hash_token`);

--
-- Indeksy dla tabeli `user_expense_category`
--
ALTER TABLE `user_expense_category`
  ADD PRIMARY KEY (`user_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeksy dla tabeli `user_income_category`
--
ALTER TABLE `user_income_category`
  ADD PRIMARY KEY (`user_id`,`category_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `user_payment_method`
--
ALTER TABLE `user_payment_method`
  ADD PRIMARY KEY (`user_id`,`payment_method_id`),
  ADD KEY `payment_method_id` (`payment_method_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT dla tabeli `incomes`
--
ALTER TABLE `incomes`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `income_categories`
--
ALTER TABLE `income_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `default_payment_methods` (`payment_method_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `default_expense_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `incomes`
--
ALTER TABLE `incomes`
  ADD CONSTRAINT `incomes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `default_income_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `incomes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `remembered_logins`
--
ALTER TABLE `remembered_logins`
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `user_expense_category`
--
ALTER TABLE `user_expense_category`
  ADD CONSTRAINT `user_expense_category_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_expense_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`category_id`);

--
-- Ograniczenia dla tabeli `user_income_category`
--
ALTER TABLE `user_income_category`
  ADD CONSTRAINT `user_income_category_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_income_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `income_categories` (`category_id`);

--
-- Ograniczenia dla tabeli `user_payment_method`
--
ALTER TABLE `user_payment_method`
  ADD CONSTRAINT `user_payment_method_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_payment_method_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`payment_method_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
