-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 18, 2024 at 10:28 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistempembelianpenjualan`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ID` int NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Level` enum('admin','operator','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ID`, `Username`, `Password`, `Level`) VALUES
(1, 'admin', 'admin', 'admin'),
(2, 'operator', 'operator', 'operator'),
(3, 'pelanggan', 'pelanggan', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `KodeBarang` int NOT NULL,
  `NamaBarang` varchar(30) NOT NULL,
  `JenisBarang` varchar(15) NOT NULL,
  `Stock` int NOT NULL,
  `HargaBeli` int NOT NULL,
  `TotalHarga` int NOT NULL,
  `Qty` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`KodeBarang`, `NamaBarang`, `JenisBarang`, `Stock`, `HargaBeli`, `TotalHarga`, `Qty`) VALUES
(1, 'Jeruk', 'Buah', 0, 1000, 10000, 10),
(2, 'Salak', 'Buah', 10, 1000, 10000, 10);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `KodePelanggan` int NOT NULL,
  `NamaPelanggan` varchar(30) NOT NULL,
  `AlamatPelanggan` varchar(50) DEFAULT NULL,
  `NoTelpPelanggan` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`KodePelanggan`, `NamaPelanggan`, `AlamatPelanggan`, `NoTelpPelanggan`) VALUES
(2, 'pelanggan', 'Disana', '08123456789');

-- --------------------------------------------------------

--
-- Table structure for table `pemasok`
--

CREATE TABLE `pemasok` (
  `KodePemasok` int NOT NULL,
  `NamaPemasok` varchar(30) NOT NULL,
  `Alamat` varchar(50) DEFAULT NULL,
  `NoTelp` varchar(20) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemasok`
--

INSERT INTO `pemasok` (`KodePemasok`, `NamaPemasok`, `Alamat`, `NoTelp`, `Email`) VALUES
(1, 'Buah Salak', 'DISANAAAAA', '02389371098319', 'salah@gmail.com'),
(2, 'Buah Duku', 'YUHU DISANA', '92398217398179', 'salak@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `NomorOrder` int NOT NULL,
  `TanggalOrder` varchar(30) NOT NULL,
  `KodePelanggan` int DEFAULT NULL,
  `KodeSupplier` int DEFAULT NULL,
  `NomorPO` int DEFAULT NULL,
  `TanggalPO` date DEFAULT NULL,
  `KodeBarang` int DEFAULT NULL,
  `JumlahBeli` int NOT NULL DEFAULT '1',
  `TotalHarga` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`NomorOrder`, `TanggalOrder`, `KodePelanggan`, `KodeSupplier`, `NomorPO`, `TanggalPO`, `KodeBarang`, `JumlahBeli`, `TotalHarga`) VALUES
(7, '2024-11-18 10:17:46', 2, NULL, 4937, '2024-12-02', 1, 10, 10000),
(8, '2024-11-18 10:17:56', 2, NULL, 1774, '2024-11-18', 2, 10, 10000),
(9, '2024-11-18 10:25:18', 2, NULL, 5420, '2024-11-18', 1, 10, 10000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`KodeBarang`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`KodePelanggan`);

--
-- Indexes for table `pemasok`
--
ALTER TABLE `pemasok`
  ADD PRIMARY KEY (`KodePemasok`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`NomorOrder`),
  ADD KEY `KodeBarang` (`KodeBarang`),
  ADD KEY `transaksi_ibfk_1` (`KodePelanggan`),
  ADD KEY `transaksi_ibfk_2` (`KodeSupplier`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `KodePelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pemasok`
--
ALTER TABLE `pemasok`
  MODIFY `KodePemasok` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `NomorOrder` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`KodePelanggan`) REFERENCES `pelanggan` (`KodePelanggan`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`KodeSupplier`) REFERENCES `pemasok` (`KodePemasok`),
  ADD CONSTRAINT `transaksi_ibfk_3` FOREIGN KEY (`KodeBarang`) REFERENCES `barang` (`KodeBarang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
