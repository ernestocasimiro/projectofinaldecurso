-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 10:06 PM
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
-- Database: `escolabd`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `adicionar_aluno_turma` (IN `p_estudante_id` INT, IN `p_turma_id` INT, IN `p_ano_letivo` VARCHAR(10), OUT `p_mensagem` VARCHAR(255))   BEGIN
    DECLARE v_area_turma VARCHAR(20);
    DECLARE v_area_estudante VARCHAR(20);
    
    -- Verificar se a turma pertence ao I Ciclo
    SELECT area INTO v_area_turma FROM turmas WHERE class_id = p_turma_id;
    
    IF v_area_turma != 'I Ciclo' THEN
        SET p_mensagem = 'Erro: Só é possível adicionar alunos a turmas do I Ciclo';
    ELSE
        -- Verificar se o aluno está no I Ciclo
        SELECT area INTO v_area_estudante FROM estudantes WHERE id = p_estudante_id;
        
        IF v_area_estudante = 'I Ciclo' THEN
            -- Adicionar aluno à turma
            INSERT INTO estudante_turma (estudante_id, turma_id, ano_letivo) 
            VALUES (p_estudante_id, p_turma_id, p_ano_letivo);
            
            SET p_mensagem = 'Aluno adicionado à turma com sucesso';
        ELSE
            SET p_mensagem = 'Erro: O aluno não pertence ao I Ciclo';
        END IF;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(127) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(127) NOT NULL,
  `lname` varchar(127) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `fname`, `lname`) VALUES
(3, 'ernestocasimiro', '$2y$10$NG6J0mB1AJs3RBbQYhQyyuCmKt91pQI7SHidf.scdDGNMxH.JbSwO', 'Ernesto Miguel', 'Casimiro\r\n'),
(4, 'danielsalazar', '$2y$10$NG6J0mB1AJs3RBbQYhQyyuCmKt91pQI7SHidf.scdDGNMxH.JbSwO', 'Daniel', 'Salazar'),
(5, 'steevesalvador', '$2y$10$NG6J0mB1AJs3RBbQYhQyyuCmKt91pQI7SHidf.scdDGNMxH.JbSwO', 'Steeve', 'Salvador');

-- --------------------------------------------------------

--
-- Table structure for table `atividades`
--

CREATE TABLE `atividades` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `descricao` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_student`
--

CREATE TABLE `class_student` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coordenadores`
--

CREATE TABLE `coordenadores` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `data_nascimento` date NOT NULL,
  `genero` enum('M','F','O') NOT NULL,
  `num_bi` varchar(13) NOT NULL,
  `foto_bi1` varchar(255) NOT NULL,
  `foto_bi2` varchar(255) NOT NULL,
  `endereco` text NOT NULL,
  `telefone` varchar(9) NOT NULL,
  `email` varchar(100) NOT NULL,
  `area_coordenacao` enum('Pedagógica','Administrativa','Disciplinar','Técnica') NOT NULL,
  `nivel_academico` enum('Bacharelato','Licenciatura','Mestrado','Doutorado') NOT NULL,
  `anos_experiencia` tinyint(2) NOT NULL,
  `fotoperfil` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `username` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coordenadores`
--

INSERT INTO `coordenadores` (`id`, `fname`, `lname`, `data_nascimento`, `genero`, `num_bi`, `foto_bi1`, `foto_bi2`, `endereco`, `telefone`, `email`, `area_coordenacao`, `nivel_academico`, `anos_experiencia`, `fotoperfil`, `password`, `created_at`, `updated_at`, `status`, `username`) VALUES
(3, 'Armando', 'Armando', '2025-05-14', 'M', '7654321LA321', 'uploads/coordinator/file_6830fbac614bb1.10459022.png', 'uploads/coordinator/file_6830fbac63c382.07221898.png', 'Kilamba - Bloco N', '947556362', 'armando@gmail.com', 'Pedagógica', 'Doutorado', 12, 'uploads/coordinator/file_6830fbac648ac6.73795077.png', '$2y$10$Dase8g1xkirxZSIklCXUY.69ICaaL6ZijdjGl5dMzKvyJYCoL9mta', '2025-05-23 22:50:20', '2025-05-23 22:50:20', 'active', NULL),
(4, 'Pedro', 'Bondo', '2025-05-05', 'M', '1234567LA123', 'uploads/coordinator/file_6830fc4635ca74.37179672.png', 'uploads/coordinator/file_6830fc4636c0c7.62320426.png', 'Camama', '912624122', 'pedrobondo@gmail.com', 'Técnica', 'Licenciatura', 5, 'uploads/coordinator/file_6830fc463749a5.25184804.png', '$2y$10$v9GKyvU0SQZrRbSkiMbY8e4UaTIXtbWfnTUK9UE4ojnEjZ4Wqj2.S', '2025-05-23 22:52:54', '2025-05-23 22:52:54', 'active', NULL),
(6, 'Valdemar', 'Valdemar', '2025-05-13', 'M', '3454545LA123', 'uploads/coordinator/file_6834bfb9e79c66.94285303.png', 'uploads/coordinator/file_6834bfb9e83a21.85036891.png', 'Nova Vida - Rua 190', '912234567', 'valdemar@gmail.com', 'Administrativa', 'Bacharelato', 1, 'uploads/coordinator/file_6834bfb9ea2d85.20566080.png', '$2y$10$0JZ1ga7dADlgsDuPKdCp.emfJWbzJBLLkZZ5/V3.EoeqqQLdSnk9W', '2025-05-26 19:23:37', '2025-05-26 19:23:37', 'active', 'vvaldemar910'),
(7, 'João', 'Manuel', '2025-07-01', 'M', '3343434LA123', 'uploads/coordinator/file_683f4be05f0dd4.57442588.png', 'uploads/coordinator/file_683f4be060ada4.61080128.png', 'Morro Bento - Rua Imetro', '941565656', 'joaomanuel@gmail.com', 'Técnica', 'Mestrado', 5, 'uploads/coordinator/file_683f4be0615bc9.58367628.png', '$2y$10$GnV1BdtCDDRDfgf18/DKceHfyzEU3CKr5J8QgzBaJ0CmAfb4qEMuq', '2025-06-03 19:24:16', '2025-06-03 19:24:16', 'active', 'jmanuel941'),
(8, 'Ifasso', 'Ifasso', '2025-06-18', 'M', '2232323LA123', 'uploads/coordinator/file_683f585a0b90f3.02739789.png', 'uploads/coordinator/file_683f585a0eb910.93891098.png', 'Camama - Iraque', '954555555', 'ifasso@gmail.com', 'Administrativa', 'Bacharelato', 1, 'uploads/coordinator/file_683f585a104fb6.50709495.png', '$2y$10$NJ3Qwxktk8H4ucbVCFxo2.Naqguyy.NJ5f6YZCfYLPsOX2cP3Ui7G', '2025-06-03 20:17:30', '2025-06-03 20:17:30', 'active', 'iifasso460'),
(9, 'Elizandro', 'Rodrigues', '1994-07-21', 'M', '2345141LA123', 'uploads/coordinator/file_68402550c4d7e1.21286276.png', 'uploads/coordinator/file_68402550c6e986.77051422.png', 'Camama - Jardim Éden', '922322222', 'rodrigues@gmail.com', 'Administrativa', 'Mestrado', 5, 'uploads/coordinator/file_68402550c7c440.78738643.png', '$2y$10$WwRU8F1DtHrfjJJlWgjWxesvo5v3Y/1utArQCd4Bnb4frr8/UXhLW', '2025-06-04 10:52:00', '2025-06-04 10:52:00', 'active', 'erodrigues344'),
(10, 'Casimiro', 'Casimiro', '2025-06-17', 'M', '4876234LA123', 'uploads/coordinator/file_6851e330ab23a0.44085706.jpg', 'uploads/coordinator/file_6851e330b01a98.40262430.jpg', 'Kilamba', '123456789', 'casimirocasimiro@gmail.com', 'Disciplinar', 'Doutorado', 12, 'uploads/coordinator/file_6851e330b3d140.89881038.jpg', '$2y$10$IvW5qXjBfCH/iDbO1W/jiuFGNtAvnaUmU0vHeefi/ptlMd2NtTyDu', '2025-06-17 21:50:40', '2025-06-17 21:50:40', 'active', 'ccasimiro222'),
(11, 'Nataniel', 'Costa', '1956-07-05', 'M', '2351147LA123', 'uploads/coordinator/file_68528b2514b785.21671232.jpg', 'uploads/coordinator/file_68528b252c9aa2.65887371.jpg', 'RuaV', '947556366', 'nataniel@gmail.com', 'Administrativa', 'Mestrado', 4, 'uploads/coordinator/file_68528b253b18b0.93666589.jpeg', '$2y$10$dBE5hxtqi.QDE2a7IJHVp.oSM3ISAZudxRnHszFKIvn.PgDjE7j/S', '2025-06-18 09:47:17', '2025-06-18 09:47:17', 'active', 'ncosta402');

-- --------------------------------------------------------

--
-- Table structure for table `coordenador_acessos`
--

CREATE TABLE `coordenador_acessos` (
  `id` int(11) NOT NULL,
  `coordenador_id` int(11) NOT NULL,
  `codigo_acesso` varchar(50) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `data_expiracao` datetime NOT NULL,
  `utilizado` tinyint(1) DEFAULT 0,
  `area` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coordenador_acessos`
--

INSERT INTO `coordenador_acessos` (`id`, `coordenador_id`, `codigo_acesso`, `data_criacao`, `data_expiracao`, `utilizado`, `area`) VALUES
(1, 6, '71b8fff8', '2025-06-03 19:15:49', '2025-07-03 20:15:49', 0, ''),
(2, 6, 'a0fe9eb8', '2025-06-03 19:19:44', '2025-07-03 20:19:44', 0, ''),
(3, 6, '058a5ff1', '2025-06-03 19:23:10', '2025-07-03 20:23:10', 0, ''),
(4, 6, '2d10103c', '2025-06-03 19:23:29', '2025-07-03 20:23:29', 0, ''),
(5, 3, '726be5c8', '2025-06-03 19:35:45', '2025-07-03 20:35:45', 0, 'Cursos Técnicos'),
(6, 3, '028c77cb', '2025-06-03 19:46:21', '2025-07-03 20:46:21', 0, 'Cursos Técnicos'),
(7, 3, '800ffc89', '2025-06-03 19:46:28', '2025-07-03 20:46:28', 1, 'Cursos Puniv'),
(8, 6, '78b6a05a', '2025-06-03 19:49:25', '2025-07-03 20:49:25', 1, 'Iº Ciclo'),
(9, 3, 'ae053654', '2025-06-03 20:26:10', '2025-07-03 21:26:10', 0, 'Cursos Técnicos'),
(10, 7, 'f1a3c861', '2025-06-03 20:28:54', '2025-07-03 21:28:54', 0, 'Cursos Técnicos'),
(11, 7, 'f84369f6', '2025-06-03 21:09:18', '2025-07-03 22:09:18', 0, 'Cursos Técnicos'),
(12, 7, 'ca475928', '2025-06-03 21:09:54', '2025-07-03 22:09:54', 1, 'Cursos Técnicos'),
(13, 8, 'f975f2a6', '2025-06-03 21:18:26', '2025-06-04 22:18:26', 0, 'IIº Ciclo'),
(14, 8, '9bd39933', '2025-06-03 21:18:55', '2025-07-03 22:18:55', 0, 'Iº Ciclo'),
(15, 8, '2555c8c8', '2025-06-03 21:29:52', '2025-07-03 22:29:52', 0, 'Iº Ciclo'),
(16, 3, 'f0a101ee', '2025-06-04 09:37:31', '2025-07-04 10:37:31', 0, 'Cursos Puniv'),
(17, 3, '51e2fb7c', '2025-06-04 11:53:44', '2025-07-04 12:53:44', 0, 'IIº Ciclo'),
(18, 10, 'c85ae549', '2025-06-17 23:07:52', '2025-07-18 00:07:52', 1, 'IIº Ciclo'),
(19, 11, '00c98a76', '2025-06-18 10:48:50', '2025-07-18 11:48:50', 1, 'Cursos Puniv');

-- --------------------------------------------------------

--
-- Table structure for table `coordinators`
--

CREATE TABLE `coordinators` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `gender` enum('Masculino','Feminino','Outro') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `bi_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `area` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cursos`
--

INSERT INTO `cursos` (`id`, `nome`, `descricao`) VALUES
(1, 'Ciências', 'Curso voltado para as áreas de biologia, física e química.'),
(2, 'Matemática', 'Curso com foco em lógica, álgebra, geometria e estatística.'),
(3, 'Letras', 'Curso voltado para língua portuguesa e literatura.');

-- --------------------------------------------------------

--
-- Table structure for table `disciplinas`
--

CREATE TABLE `disciplinas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disciplinas`
--

INSERT INTO `disciplinas` (`id`, `nome`, `curso_id`, `descricao`) VALUES
(1, 'Matemática', 1, 'Disciplina de cálculo, álgebra e estatística'),
(2, 'Física', 1, 'Disciplina de mecânica, óptica e termodinâmica'),
(3, 'Química', 1, 'Disciplina de química geral, orgânica e inorgânica'),
(4, 'Biologia', 1, 'Disciplina de citologia, genética e ecologia'),
(5, 'Português', 3, 'Gramática, produção textual e literatura'),
(6, 'Inglês', 3, 'Gramática e conversação em inglês'),
(7, 'Álgebra', 2, 'Matemática avançada para ensino médio'),
(8, 'Geometria', 2, 'Geometria plana e espacial');

-- --------------------------------------------------------

--
-- Table structure for table `encarregado`
--

CREATE TABLE `encarregado` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` enum('Masculino','Feminino','Outro') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `bi_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `encarregados`
--

CREATE TABLE `encarregados` (
  `id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `genero` enum('M','F','O') NOT NULL,
  `num_bi` varchar(20) NOT NULL,
  `foto_bi1` varchar(255) NOT NULL,
  `foto_bi2` varchar(255) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profissao` varchar(100) NOT NULL,
  `parentesco` enum('Pai','Mãe','Tio','Tia','Avô','Avó','Irmão','Irmã','Outro') NOT NULL,
  `fotoperfil` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `username` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `encarregados`
--

INSERT INTO `encarregados` (`id`, `fname`, `lname`, `data_nascimento`, `genero`, `num_bi`, `foto_bi1`, `foto_bi2`, `endereco`, `telefone`, `email`, `profissao`, `parentesco`, `fotoperfil`, `password`, `created_at`, `updated_at`, `username`) VALUES
(5, 'Ernes', 'Casimiro', '2006-05-25', '', '1244567LA144', 'uploads/guardians/file_6830ceef4fa5a6.60700295.png', 'uploads/guardians/file_6830ceef5158b0.29472690.png', 'RuaV', '943800930', 'ernesto@gmail.com', 'gestor', 'Pai', 'uploads/guardians/file_6830ceef51bbd6.55859891.png', '$2y$10$ENttnQyIXWxDakKRkj2BUeV.JLAJ5Oe1Q9sUUKHM99iJPQuRLwk8a', '2025-05-23 19:39:27', '2025-05-24 08:19:22', NULL),
(6, 'Steeve', 'Salvador', '2025-05-21', '', '7654321LA321', 'uploads/guardians/file_6830d970dc3234.67154586.png', 'uploads/guardians/file_6830d970dccff6.01395340.png', 'Zango', '923456789', 'steeve@gmail.com', 'governador de luanda', 'Pai', 'uploads/guardians/file_6830d970dd4132.69190212.png', '$2y$10$P.D0IsskGao9aaYnXfdGJ.UYYihLJHzY5Y6ymSQ4gYTHkXP76vxI.', '2025-05-23 20:24:16', '2025-06-12 20:13:27', 'salvadorsteeve'),
(7, 'Daniel', 'Salazar', '2025-05-29', '', '1237893LA123', 'uploads/guardians/file_6830d9c9928ee5.91755333.png', 'uploads/guardians/file_6830d9c993ade0.87595789.png', 'Viana', '923657897', 'daniel@gmail.com', 'economista', 'Pai', 'uploads/guardians/file_6830d9c9949a50.26985227.png', '$2y$10$VtfKKUnFjoy4GblECLC43uikpy4KdA6ODDECJ0SXJjblDSaaPTcCC', '2025-05-23 20:25:45', '2025-05-26 19:47:45', 'daniel.salazar'),
(9, 'Cleiton', 'Paiva', '2025-05-29', '', '8898989LA989', 'uploads/guardians/img_6834be17a591c1.62606710.png', 'uploads/guardians/img_6834be17a6e7f6.49468554.png', 'Benfica - Zona Verde', '999999999', 'cleiton@gmail.com', 'Piloto', 'Pai', 'uploads/guardians/img_6834be17a85fa4.15416981.png', '$2y$10$zQlUre/uHIAazsEPUvOyruj0763Uy7P/.XhkeC/5sLeLDiQnAbd2a', '2025-05-26 19:16:39', '2025-05-28 09:16:30', 'cleiton.paiva1');

-- --------------------------------------------------------

--
-- Table structure for table `estudantes`
--

CREATE TABLE `estudantes` (
  `id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `genero` enum('M','F','O') NOT NULL,
  `num_bi` varchar(20) NOT NULL,
  `foto_bi1` varchar(255) NOT NULL,
  `foto_bi2` varchar(255) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'pending',
  `fotoperfil` varchar(255) NOT NULL,
  `encarregado_id` int(11) NOT NULL,
  `area` enum('I Ciclo','II Ciclo','Curso PUNIV','Curso Técnico') NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `estudantes`
--

INSERT INTO `estudantes` (`id`, `fname`, `lname`, `data_nascimento`, `genero`, `num_bi`, `foto_bi1`, `foto_bi2`, `endereco`, `telefone`, `email`, `status`, `fotoperfil`, `encarregado_id`, `area`, `password`, `created_at`, `updated_at`) VALUES
(4, 'Kelton', 'Gonçalves', '2025-05-14', 'M', '1234567LA123', 'uploads/students/file_6832396c3b1283.97073285.png', 'uploads/students/file_6832396c3d0503.72444304.png', 'RuaV', '947556362', 'kelton@gmail.com', 'pending', 'uploads/students/file_6832396c3dfce5.54444088.png', 6, 'I Ciclo', '$2y$10$YjTphot16xoqRObAju7.Munh/wZ9M51vYAONw87.LSyJPCbptVgES', '2025-05-24 21:26:04', '2025-05-24 21:26:04'),
(5, 'Bruno', 'Dias', '2025-05-09', 'M', '2525252LA253', 'uploads/students/file_68323b53d53768.03093418.png', 'uploads/students/file_68323b53d70598.55726364.png', 'Camama - Iraque', '923456789', 'bruno@gmail.com', '', 'uploads/students/file_68323b53d79837.92803315.png', 7, 'I Ciclo', '$2y$10$hsztfXnkUvK23UCmcMx8DO4qyKvM0V186yjqfc2u5ZA0lsx8cC9Tq', '2025-05-24 21:34:11', '2025-05-24 21:34:11'),
(6, 'Ernesto Miguel', 'Casimiro', '2005-05-25', 'M', '5267181LA123', 'uploads/students/file_683f2c7ace9787.94786263.png', 'uploads/students/file_683f2c7ad02d65.26911713.png', 'Centralidade Do Kilamba U37', '947556362', 'miguelito@gmail.com', '', 'uploads/students/file_683f2c7ad0ab81.21847866.png', 5, 'I Ciclo', '', '2025-06-03 17:10:18', '2025-06-03 17:10:18'),
(7, 'Steeve', 'Salvador', '2025-06-17', 'M', '6372891LA123', 'uploads/students/file_6840067d429196.95645559.png', 'uploads/students/file_6840067d43df75.59702029.png', 'Kilamba', '945655556', 'salvador@gmail.com', '', 'uploads/students/file_6840067d449fb8.14435291.png', 6, 'I Ciclo', '', '2025-06-04 08:40:29', '2025-06-04 08:40:29'),
(9, 'Daniel', 'Salazar', '2000-10-10', 'M', '1234568LA123', 'uploads/students/file_68400d08f1dab6.84993649.png', 'uploads/students/file_68400d08f26ee8.41981622.png', 'Condomínio Paraíso Real', '945822765', 'daniel@gmail.com', '', 'uploads/students/file_68400d08f2eae9.25733733.png', 7, 'I Ciclo', '', '2025-06-04 09:08:24', '2025-06-04 09:08:24'),
(11, 'José', 'Oliveira', '2025-07-12', 'M', '4721434LA123', 'uploads/students/file_68507260508679.10541711.png', 'uploads/students/file_68507260526b05.94032522.png', 'RuaV', '941556362', 'joseoliveira@gmail.com', '', 'uploads/students/file_6850726052fa91.59606461.png', 5, 'Curso Técnico', '$2y$10$IfD9JXGEja4sPuieWSqSnOG1JVC8IDpBtOmtERrWRf2VI2.uJ0Zdm', '2025-06-16 19:37:04', '2025-06-16 19:37:04');

-- --------------------------------------------------------

--
-- Table structure for table `estudante_turma`
--

CREATE TABLE `estudante_turma` (
  `id` int(11) NOT NULL,
  `estudante_id` int(11) NOT NULL,
  `turma_id` int(11) NOT NULL,
  `area_estudante` enum('I Ciclo','II Ciclo','Cursos Puniv','Cursos Técnicos') NOT NULL,
  `ano_letivo` varchar(10) NOT NULL,
  `data_matricula` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `estudante_turma`
--
DELIMITER $$
CREATE TRIGGER `before_estudante_turma_insert` BEFORE INSERT ON `estudante_turma` FOR EACH ROW BEGIN
    DECLARE v_area_turma VARCHAR(20);
    DECLARE v_area_estudante VARCHAR(20);
    
    -- Obter área da turma
    SELECT area INTO v_area_turma FROM turmas WHERE class_id = NEW.turma_id;
    
    -- Obter área do estudante
    SELECT area INTO v_area_estudante FROM estudantes WHERE id = NEW.estudante_id;
    
    -- Verificar se a turma é do I Ciclo e se o aluno pertence ao I Ciclo
    IF v_area_turma != 'I Ciclo' OR v_area_estudante != 'I Ciclo' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Apenas alunos do I Ciclo podem ser adicionados a esta turma';
    END IF;
    
    -- Atualizar a área do estudante na tabela de relação
    SET NEW.area_estudante = v_area_estudante;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `guardians`
--

CREATE TABLE `guardians` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` enum('Masculino','Feminino','Outro') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `bi_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guardians`
--

INSERT INTO `guardians` (`id`, `name`, `gender`, `dob`, `bi_number`, `address`, `contact`, `email`, `created_at`, `updated_at`) VALUES
(13, 'Luís Casimiro', 'Masculino', '2025-04-08', '1234567LA123', 'Kilamba', '+244 947556362', 'luiscasimiro@gmail.com', '2025-04-16 23:19:09', '2025-04-16 23:19:09'),
(14, 'Ernesto Miguel Casimiro', 'Masculino', '2025-04-09', '7363672LA133', 'Morro Bento', '+244 943800930', 'ernesto@gmail.com', '2025-04-16 23:26:20', '2025-04-16 23:26:20'),
(15, 'Steeve Salvador', 'Masculino', '1988-10-20', '1234567LA123', 'Kilamba', '+244 923 456 789', 'steeve@gmail.com', '2025-04-28 09:02:05', '2025-04-28 09:02:05'),
(17, 'João Lourenço', 'Masculino', '1954-03-05', '1234567LA123', 'White House', '+244 923 456 789', 'joaolourenco@gmail.com', '2025-05-07 16:01:21', '2025-05-07 16:01:21');

-- --------------------------------------------------------

--
-- Table structure for table `log_cobranca`
--

CREATE TABLE `log_cobranca` (
  `id` int(11) NOT NULL,
  `tipo` enum('email','call','sms') NOT NULL,
  `data` datetime NOT NULL,
  `destinatario` varchar(255) DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `estudante_id` int(11) NOT NULL,
  `turma_id` int(11) NOT NULL,
  `ano_letivo` year(4) NOT NULL,
  `data_matricula` date NOT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('activa','transferido','cancelado') DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mensalidades`
--

CREATE TABLE `mensalidades` (
  `id` int(11) NOT NULL,
  `estudante_id` int(11) NOT NULL,
  `turma_id` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `ano` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_vencimento` date NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `pago` tinyint(1) DEFAULT 0,
  `status` enum('pago','pendente','vencido') NOT NULL DEFAULT 'pendente',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mensalidades`
--

INSERT INTO `mensalidades` (`id`, `estudante_id`, `turma_id`, `mes`, `ano`, `valor`, `data_vencimento`, `data_pagamento`, `pago`, `status`, `metodo_pagamento`, `referencia`, `observacoes`, `created_at`) VALUES
(7, 7, 2, 0, 2025, 50000.00, '2025-06-19', NULL, 0, 'vencido', NULL, NULL, NULL, '2025-06-14 17:47:11'),
(18, 9, 2, 0, 0, 100.00, '2025-07-16', '2025-06-11', 0, 'pago', NULL, NULL, NULL, '2025-06-11 19:46:00'),
(29, 4, 2, 0, 2024, 10000.00, '2024-05-10', NULL, 0, 'vencido', NULL, NULL, NULL, '2025-06-12 17:01:47'),
(30, 5, 2, 0, 0, 73.01, '2025-06-12', NULL, 0, 'vencido', NULL, NULL, NULL, '2025-06-12 17:07:20'),
(32, 6, 2, 0, 2025, 73010.00, '2025-06-19', NULL, 0, 'vencido', NULL, NULL, NULL, '2025-06-14 16:53:14');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_role` enum('guardian','teacher') NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_role` enum('guardian','teacher') NOT NULL,
  `message_text` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `attachment_type` enum('pdf','audio','none') DEFAULT 'none',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `operadores_financeiros`
--

CREATE TABLE `operadores_financeiros` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operadores_financeiros`
--

INSERT INTO `operadores_financeiros` (`id`, `fname`, `lname`, `username`, `nome`, `email`, `password`, `criado_em`) VALUES
(4, 'Carlos', 'Alberto', 'carlos.alb', '', 'carlos.alberto@escola.com', '$2y$10$B02KNnbPOSS2UM6rJhf3aO2MHPRd3AGPCvFIMK.GCkhV6gB/j.7Wq', '2025-06-19 19:17:29'),
(5, 'Marta', 'Silva', 'marta.silva', '', 'marta.silva@escola.com', '$2y$10$B02KNnbPOSS2UM6rJhf3aO2MHPRd3AGPCvFIMK.GCkhV6gB/j.7Wq', '2025-06-19 19:17:29'),
(6, 'João', 'Pedro', 'joao.p', '', 'joao.pedro@escola.com', '$2y$10$B02KNnbPOSS2UM6rJhf3aO2MHPRd3AGPCvFIMK.GCkhV6gB/j.7Wq', '2025-06-19 19:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `professores`
--

CREATE TABLE `professores` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `genero` enum('M','F','O') NOT NULL COMMENT 'M=Masculino, F=Feminino, O=Outro',
  `data_nascimento` date NOT NULL,
  `num_bi` varchar(13) NOT NULL COMMENT 'Formato: 0000000LA000',
  `foto_bi1` varchar(255) NOT NULL COMMENT 'Caminho para a foto da frente do BI',
  `foto_bi2` varchar(255) NOT NULL COMMENT 'Caminho para a foto do verso do BI',
  `endereco` text NOT NULL,
  `fotoperfil` varchar(255) NOT NULL COMMENT 'Caminho para a foto de perfil',
  `telefone` varchar(9) NOT NULL COMMENT '9 dígitos',
  `email` varchar(100) NOT NULL,
  `especializacao` varchar(50) NOT NULL,
  `nivel_academico` enum('Bacharelato','Licenciatura','Mestrado','Doutorado') NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Senha criptografada',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `username` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `professores`
--

INSERT INTO `professores` (`id`, `fname`, `lname`, `genero`, `data_nascimento`, `num_bi`, `foto_bi1`, `foto_bi2`, `endereco`, `fotoperfil`, `telefone`, `email`, `especializacao`, `nivel_academico`, `password`, `status`, `created_at`, `updated_at`, `username`) VALUES
(2, 'Eurico', 'Eurico', '', '2025-05-19', '1234567LA123', 'uploads/teacher/file_6830f1872e2a35.68970262.png', 'uploads/teacher/file_6830f18730ad05.12219135.png', 'Gamek à Direita', 'uploads/teacher/file_68587de9734830.75033546.jpg', '947556362', 'eurico@gmail.com', 'Matemática', 'Licenciatura', '$2y$10$z3TAn6QkKsej9XppJUNQXeprJ4Ly7JuXu4g1W5Pr6/9A9Srk0vrza', 'active', '2025-05-23 22:07:03', '2025-06-22 22:04:25', NULL),
(5, 'Ernesto', 'Quitanda', '', '2025-05-23', '2351456LA345', 'uploads/teacher/file_6830f2f67a6043.44182314.png', 'uploads/teacher/file_6830f2f67b7f95.52767245.png', 'Morro Bento - Kikagil', 'uploads/teacher/file_68587dd91bf5d4.16352608.jpg', '945636234', 'quitanda@gmail.com', 'Inglês', 'Licenciatura', '$2y$10$oid87cGe7H/9sCzlIHfOO.26VXC8T0aiOkUvA04HOoApiXDxPovM6', 'active', '2025-05-23 22:13:10', '2025-06-22 22:04:09', NULL),
(7, 'Maria', 'Borges', '', '2025-05-14', '7637263LA123', 'uploads/teacher/file_68587e434aeb45.88294498.png', 'uploads/teacher/file_68587e434e8647.65614080.png', 'Kilamba - Bloco U', 'uploads/teacher/file_68587e43547502.07139724.jpg', '934261614', 'mariaborges@gmail.com', 'Inglês', 'Licenciatura', '$2y$10$x0lDN5gMX5JgcBQjNadwtOA0bUnr5hrLp58JsAJ2.QP3oRGPJS6Xm', 'active', '2025-05-26 18:55:34', '2025-06-22 22:05:55', 'maria.borges'),
(8, 'Kizeye', 'Mavacala', '', '2025-05-23', '6635272LA123', 'uploads/teacher/file_68361938048361.29540773.png', 'uploads/teacher/file_6836193805b961.54186382.png', 'RuaV', 'uploads/teacher/file_68587df6e1ffe6.41379447.jpg', '947557362', 'kizeyemoco@gmail.com', 'Matemática', 'Mestrado', '$2y$10$f3CCq023JC1qsFzrDECEpewskzU1I3Y0S9TRuWsgE49HlMeqrXzGu', 'active', '2025-05-27 19:57:44', '2025-06-22 22:04:38', 'kizeyemoco.mavacala');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `genero` enum('Masculino','Feminino','Outro') NOT NULL,
  `data_nascimento` date NOT NULL,
  `bi_numero` varchar(20) NOT NULL,
  `foto_bi_frente` varchar(255) DEFAULT NULL,
  `foto_bi_verso` varchar(255) DEFAULT NULL,
  `endereco` text NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `encarregado_id` int(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `fname`, `lname`, `genero`, `data_nascimento`, `bi_numero`, `foto_bi_frente`, `foto_bi_verso`, `endereco`, `foto_perfil`, `id`, `telefone`, `email`, `encarregado_id`, `senha_hash`, `criado_em`, `atualizado_em`) VALUES
(5, 'Maria', 'Silva', 'Feminino', '2010-03-15', '1234567LA001', 'uploads/bi1_frente.jpg', 'uploads/bi1_verso.jpg', 'Luanda, Bairro Tal', 'uploads/perfil1.jpg', NULL, '+244912345678', 'maria.silva@example.com', 0, 'senha123!', '2025-04-23 15:51:35', '2025-04-23 15:51:35'),
(6, 'João', 'Ferreira', 'Masculino', '2011-07-22', '7654321LB002', 'uploads/bi2_frente.jpg', 'uploads/bi2_verso.jpg', 'Benguela, Rua 45', 'uploads/perfil2.jpg', NULL, '+244923456789', 'joao.ferreira@example.com', 0, 'senha456!', '2025-04-23 15:51:35', '2025-04-23 15:51:35');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(10) NOT NULL COMMENT 'Código curto da disciplina (ex: LP, MAT)',
  `subject_name` varchar(100) NOT NULL COMMENT 'Nome completo da disciplina',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `description`, `is_active`) VALUES
(1, 'LP', 'Língua Portuguesa', NULL, 1),
(2, 'MAT', 'Matemática', NULL, 1),
(3, 'FIS', 'Física', NULL, 1),
(4, 'TLP', 'Técnica de Linguagem de Programação', NULL, 1),
(5, 'TIC', 'Tecnologia de Informação e Comunicação', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `gender` enum('Masculino','Feminino','Outro') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `bi_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `fname`, `lname`, `username`, `gender`, `dob`, `bi_number`, `address`, `contact`, `email`, `password`, `created_at`, `updated_at`) VALUES
(6, 'Ernesto', 'Quitanda', 'ernestoquitanda', 'Masculino', '2000-06-27', '1234567LA123', 'Camama', '+244 923 456 789', 'quitanda@gmail.com', '$2y$10$n7Na5nL3WhPbfxxRrxt2Q.4gj8uvyDQNH/Yd.f9EAinJbvG/xmRva', '2025-04-26 12:43:10', '2025-04-26 12:43:10'),
(7, 'Kizeymoco', 'Mavacala', 'mavacala', 'Masculino', '1992-10-20', '1234567LA123', 'Camama', '+244 923 456 789', 'mavacala@gmail.com', '$2y$10$ca2ILNRLImKQpsmW0n7pEe.rRtpR4g0hl8BsX0QimnUqVYp1q4VKS', '2025-04-29 07:58:18', '2025-04-29 07:58:18'),
(8, 'ernesto', 'miguel', 'casimiro', 'Masculino', '2000-07-26', '1234567LA123', 'Kilamba', '+244 943 800 930', 'ernes@gmail.com', '$2y$10$F0hPxHiMQEFF2QsEZ9OcWu/GlL2S1gGNdTzW0Ba7XQVm9CZf1gTju', '2025-05-05 19:33:08', '2025-05-05 19:33:08'),
(9, 'Ernesto', 'Casimiro', 'ernes', 'Masculino', '1992-06-17', '1234567LA123', 'RuaV', '+244 923 456 789', 'ernes@gmail.com', '$2y$10$i6tp07DKB7fr4CXZqO7Dv.xL9/uUzcV/0cQYxlyYkPeC3vjnYcPUu', '2025-05-12 20:25:35', '2025-05-12 20:25:35'),
(10, 'Aurio', 'Menezes', 'auriomenezes', 'Masculino', '1995-07-13', '1234567LA123', 'RuaV', '+244 923 456 789', 'aurio@gmail.com', '$2y$10$RLpvHDAeB.9gtiWnatmEeeLZYN32uOJaGpkZ2DJz2h0hQ3wFswUY.', '2025-05-14 07:48:38', '2025-05-14 07:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `turma`
--

CREATE TABLE `turma` (
  `id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `class_grade` varchar(255) DEFAULT NULL,
  `class_course` varchar(255) DEFAULT NULL,
  `class_capacity` varchar(255) DEFAULT NULL,
  `class_room` varchar(50) DEFAULT NULL,
  `class_director_id` int(11) NOT NULL,
  `class_period` varchar(255) DEFAULT NULL,
  `class_year` varchar(255) DEFAULT NULL,
  `class_description` text DEFAULT NULL,
  `class_observations` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `turma`
--

INSERT INTO `turma` (`id`, `class_name`, `class_grade`, `class_course`, `class_capacity`, `class_room`, `class_director_id`, `class_period`, `class_year`, `class_description`, `class_observations`, `created_at`, `updated_at`) VALUES
(2, 'Turma 13ª Infomrática', '10', 'informatica', '25', 'Sala 12', 5, 'morning', '2024', 'turma', 'atenção', '2025-05-24 12:35:29', '2025-05-28 09:42:07'),
(4, 'Turma Informática', '13', 'informatica', '15', 'Sala 14', 2, 'morning', '2023', 'Turma que fará a defesa final de curso.', 'Turma a realizar estágio no ISPAJ, acompanhada pela empresa LevelSoft', '2025-05-24 13:30:13', '2025-05-24 13:30:13'),
(6, 'Turma CEJ', '12', 'Economica', '25', 'sala 15', 7, 'afternoon', '2024', 'CEJ', 'CEJ', '2025-05-27 19:53:31', '2025-05-27 19:53:31'),
(7, 'Informática', '10', '', '25', 'sala 26', 8, 'afternoon', '2024', 'turma do ensino médio', 'informática', '2025-06-06 15:01:36', '2025-06-06 15:01:36'),
(8, 'A', NULL, NULL, '25', NULL, 1, NULL, NULL, NULL, NULL, '2025-06-17 18:07:02', '2025-06-17 18:07:02'),
(9, 'A', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-06-17 18:10:13', '2025-06-17 18:10:13'),
(10, 'A', '1ª Classe', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-06-17 18:16:02', '2025-06-17 18:16:02'),
(11, 'B', '1ª Classe', NULL, NULL, NULL, 1, NULL, '2025', NULL, NULL, '2025-06-17 18:16:23', '2025-06-17 18:16:23'),
(12, 'C', '1ª Classe', NULL, NULL, NULL, 1, NULL, '2025', NULL, NULL, '2025-06-17 18:16:29', '2025-06-17 18:16:29'),
(13, 'D', '1ª Classe', NULL, NULL, NULL, 1, NULL, '2025', NULL, NULL, '2025-06-17 18:16:36', '2025-06-17 18:16:36'),
(14, '10 info', NULL, 'Informática', NULL, NULL, 1, 'Tarde', '2025', NULL, NULL, '2025-06-18 08:36:21', '2025-06-18 08:36:21');

-- --------------------------------------------------------

--
-- Table structure for table `turmas`
--

CREATE TABLE `turmas` (
  `class_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `ano` varchar(10) NOT NULL,
  `curso` varchar(100) DEFAULT NULL,
  `capacidade` int(11) NOT NULL DEFAULT 25,
  `diretor_id` int(11) DEFAULT NULL,
  `turno` enum('manha','tarde','noite') DEFAULT 'manha',
  `descricao` text DEFAULT NULL,
  `area` enum('I Ciclo','II Ciclo','Cursos Puniv','Cursos Técnicos') NOT NULL,
  `classe` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `turmas`
--

INSERT INTO `turmas` (`class_id`, `nome`, `ano`, `curso`, `capacidade`, `diretor_id`, `turno`, `descricao`, `area`, `classe`) VALUES
(16, '10 Informática', '10', NULL, 25, 6, 'manha', 'Turma da Décima classe Informática', '', ''),
(18, 'Turma 10A', '10', NULL, 25, 6, 'manha', 'Turma de exemplo', '', ''),
(20, '10 info', '10', NULL, 25, 6, 'tarde', 'ok', '', ''),
(21, '10 info', '10', NULL, 25, 6, 'tarde', 'ok', '', ''),
(27, 'A', '', NULL, 25, NULL, 'manha', NULL, 'I Ciclo', '1ª Classe'),
(28, 'B', '', NULL, 25, NULL, 'manha', NULL, 'I Ciclo', '1ª Classe');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `atividades`
--
ALTER TABLE `atividades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_student`
--
ALTER TABLE `class_student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `coordenadores`
--
ALTER TABLE `coordenadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `num_bi` (`num_bi`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telefone` (`telefone`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `coordenador_acessos`
--
ALTER TABLE `coordenador_acessos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coordenador_id` (`coordenador_id`);

--
-- Indexes for table `coordinators`
--
ALTER TABLE `coordinators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indexes for table `encarregado`
--
ALTER TABLE `encarregado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bi_number` (`bi_number`);

--
-- Indexes for table `encarregados`
--
ALTER TABLE `encarregados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `num_bi` (`num_bi`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `estudantes`
--
ALTER TABLE `estudantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `num_bi` (`num_bi`),
  ADD KEY `fk_encarregado_id` (`encarregado_id`);

--
-- Indexes for table `estudante_turma`
--
ALTER TABLE `estudante_turma`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_estudante_turma_ano` (`estudante_id`,`turma_id`,`ano_letivo`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Indexes for table `guardians`
--
ALTER TABLE `guardians`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_cobranca`
--
ALTER TABLE `log_cobranca`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudante_id` (`estudante_id`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Indexes for table `mensalidades`
--
ALTER TABLE `mensalidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudante_id` (`estudante_id`),
  ADD KEY `fk_turma_id` (`turma_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `operadores_financeiros`
--
ALTER TABLE `operadores_financeiros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `professores`
--
ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `num_bi` (`num_bi`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telefone` (`telefone`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `bi_numero` (`bi_numero`),
  ADD KEY `fk_students_guardian` (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `turma`
--
ALTER TABLE `turma`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_class_director` (`class_director_id`);

--
-- Indexes for table `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `diretor_id` (`diretor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `atividades`
--
ALTER TABLE `atividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_student`
--
ALTER TABLE `class_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coordenadores`
--
ALTER TABLE `coordenadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `coordenador_acessos`
--
ALTER TABLE `coordenador_acessos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `coordinators`
--
ALTER TABLE `coordinators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `disciplinas`
--
ALTER TABLE `disciplinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `encarregado`
--
ALTER TABLE `encarregado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `encarregados`
--
ALTER TABLE `encarregados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `estudantes`
--
ALTER TABLE `estudantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `estudante_turma`
--
ALTER TABLE `estudante_turma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `guardians`
--
ALTER TABLE `guardians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `log_cobranca`
--
ALTER TABLE `log_cobranca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mensalidades`
--
ALTER TABLE `mensalidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `operadores_financeiros`
--
ALTER TABLE `operadores_financeiros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `turma`
--
ALTER TABLE `turma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `turmas`
--
ALTER TABLE `turmas`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `class_student`
--
ALTER TABLE `class_student`
  ADD CONSTRAINT `class_student_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `turmas` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_student_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coordenador_acessos`
--
ALTER TABLE `coordenador_acessos`
  ADD CONSTRAINT `coordenador_acessos_ibfk_1` FOREIGN KEY (`coordenador_id`) REFERENCES `coordenadores` (`id`);

--
-- Constraints for table `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD CONSTRAINT `disciplinas_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `estudantes`
--
ALTER TABLE `estudantes`
  ADD CONSTRAINT `fk_encarregado_id` FOREIGN KEY (`encarregado_id`) REFERENCES `encarregados` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `estudante_turma`
--
ALTER TABLE `estudante_turma`
  ADD CONSTRAINT `estudante_turma_ibfk_1` FOREIGN KEY (`estudante_id`) REFERENCES `estudantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `estudante_turma_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turma` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`estudante_id`) REFERENCES `estudantes` (`id`),
  ADD CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turma` (`id`);

--
-- Constraints for table `mensalidades`
--
ALTER TABLE `mensalidades`
  ADD CONSTRAINT `fk_turma_id` FOREIGN KEY (`turma_id`) REFERENCES `turma` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mensalidades_ibfk_1` FOREIGN KEY (`estudante_id`) REFERENCES `estudantes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_encarregado` FOREIGN KEY (`id`) REFERENCES `guardians` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_turmas` FOREIGN KEY (`id`) REFERENCES `turmas` (`class_id`) ON DELETE SET NULL;

--
-- Constraints for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `teacher_subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `turmas`
--
ALTER TABLE `turmas`
  ADD CONSTRAINT `turmas_ibfk_1` FOREIGN KEY (`diretor_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
