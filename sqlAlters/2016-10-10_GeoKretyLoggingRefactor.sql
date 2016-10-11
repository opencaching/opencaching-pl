-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas generowania: 09 Paź 2016, 16:21
-- Wersja serwera: 10.1.9-MariaDB
-- Wersja PHP: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Baza danych: `ocpl`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `geokret_log`
--

CREATE TABLE `geokret_log` (
  `id` int(11) NOT NULL,
  `log_date_time` datetime NOT NULL,
  `enqueue_date_time` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `geocache_id` int(11) NOT NULL,
  `log_type` int(11) NOT NULL,
  `comment` varchar(160) NOT NULL,
  `tracking_code` varchar(10) NOT NULL,
  `geokret_id` int(11) NOT NULL,
  `geokret_name` varchar(160) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `geokret_log`
--
ALTER TABLE `geokret_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enqueue_date_time` (`enqueue_date_time`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `geokret_log`
--
ALTER TABLE `geokret_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;