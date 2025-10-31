-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2025 at 12:30 AM
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
-- Database: `erisdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblapplicants`
--

CREATE TABLE `tblapplicants` (
  `APPLICANTID` int(11) NOT NULL,
  `JOBCATEGORYID` int(11) NOT NULL,
  `JOBTITLE` varchar(100) NOT NULL,
  `EXCOMPANYNAME` varchar(200) NOT NULL,
  `EXJOBTITLE` varchar(50) NOT NULL,
  `USERID` int(11) NOT NULL,
  `FNAME` varchar(90) NOT NULL,
  `OTHERNAMES` varchar(200) NOT NULL,
  `FULLADDRESS` varchar(255) NOT NULL,
  `CITY` varchar(100) NOT NULL,
  `COUNTRY` varchar(50) NOT NULL,
  `SEX` varchar(11) NOT NULL,
  `BIRTHDATE` date NOT NULL,
  `ABOUTME` text NOT NULL,
  `USERNAME` varchar(90) NOT NULL,
  `EMAILADDRESS` varchar(90) NOT NULL,
  `CONTACTNO` varchar(90) NOT NULL,
  `DEGREE` text NOT NULL,
  `SCHOOLNAME` varchar(100) NOT NULL,
  `SKILLS` varchar(200) NOT NULL,
  `APPLICANTPHOTO` varchar(255) NOT NULL,
  `FB_link` varchar(255) NOT NULL,
  `LinkedIn_link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblapplicants`
--

INSERT INTO `tblapplicants` (`APPLICANTID`, `JOBCATEGORYID`, `JOBTITLE`, `EXCOMPANYNAME`, `EXJOBTITLE`, `USERID`, `FNAME`, `OTHERNAMES`, `FULLADDRESS`, `CITY`, `COUNTRY`, `SEX`, `BIRTHDATE`, `ABOUTME`, `USERNAME`, `EMAILADDRESS`, `CONTACTNO`, `DEGREE`, `SCHOOLNAME`, `SKILLS`, `APPLICANTPHOTO`, `FB_link`, `LinkedIn_link`) VALUES
(1, 21, 'Web Developer', 'Dinconnect ICT Services', 'Web Developer', 321, 'Dom', 'Carl', '25 Albert Street', 'St. John', 'Canada', 'Male', '2025-09-30', 'Back-end & Frond-end developer. I have extensive experience in web development and specialty in PHP. I am passionate about collaborating with you to create something truly unique.', 'emeldo', 'emehchiemerie9@gmail.com', '70957684677', 'Master Degree', 'Memorial University ', 'PHP, Software Development, Web Developer', 'profile/WhatsApp Image 2025-03-05 at 00.23.58_d8771480.jpg', 'htttps://fb.com/', 'https://lindin.com/');

-- --------------------------------------------------------

--
-- Table structure for table `tblattachmentfile`
--

CREATE TABLE `tblattachmentfile` (
  `ID` int(11) NOT NULL,
  `APPLICANTID` int(11) DEFAULT NULL,
  `JOBID` int(11) NOT NULL,
  `FILE_NAME` varchar(90) NOT NULL,
  `FILE_LOCATION` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblautonumbers`
--

CREATE TABLE `tblautonumbers` (
  `AUTOID` int(11) NOT NULL,
  `AUTOSTART` varchar(30) NOT NULL,
  `AUTOEND` int(11) NOT NULL,
  `AUTOINC` int(11) NOT NULL,
  `AUTOKEY` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblautonumbers`
--

INSERT INTO `tblautonumbers` (`AUTOID`, `AUTOSTART`, `AUTOEND`, `AUTOINC`, `AUTOKEY`) VALUES
(1, '02983', 7, 1, 'Userid'),
(2, '000', 78, 1, 'Employer'),
(3, '011', 16, 1, 'Applicant'),
(4, '69125', 29, 1, 'FILEID');

-- --------------------------------------------------------

--
-- Table structure for table `tblbookmarkjob`
--

CREATE TABLE `tblbookmarkjob` (
  `ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `DATETIME` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblbookmarkresume`
--

CREATE TABLE `tblbookmarkresume` (
  `ID` int(11) NOT NULL,
  `USERID` int(11) NOT NULL,
  `JOBAPPLICATIONID` int(11) NOT NULL,
  `JOBRESUMEID` int(11) NOT NULL,
  `DATETIME` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcompany`
--

CREATE TABLE `tblcompany` (
  `COMPANYID` int(11) NOT NULL,
  `COMPANYNAME` varchar(90) NOT NULL,
  `COMPANYADDRESS` varchar(90) NOT NULL,
  `COMPANYCONTACTNO` varchar(30) NOT NULL,
  `COMPANYSTATUS` varchar(90) NOT NULL,
  `COMPANYABOUT` text NOT NULL,
  `COMPANYEMAIL` varchar(50) NOT NULL,
  `COMPANYINDUSTRY` varchar(50) NOT NULL,
  `COMPANYSPECIALISM` varchar(50) NOT NULL,
  `COMPANYCOUNTRY` varchar(50) NOT NULL,
  `COMPANYCITY` varchar(50) NOT NULL,
  `COMPANYAWARD` varchar(20) NOT NULL,
  `COMPANYYEAR` varchar(10) NOT NULL,
  `COMPANYAWARDDESC` text NOT NULL,
  `COMPANYLOGO` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcompany`
--

INSERT INTO `tblcompany` (`COMPANYID`, `COMPANYNAME`, `COMPANYADDRESS`, `COMPANYCONTACTNO`, `COMPANYSTATUS`, `COMPANYABOUT`, `COMPANYEMAIL`, `COMPANYINDUSTRY`, `COMPANYSPECIALISM`, `COMPANYCOUNTRY`, `COMPANYCITY`, `COMPANYAWARD`, `COMPANYYEAR`, `COMPANYAWARDDESC`, `COMPANYLOGO`) VALUES
(1, 'Nasdaq', '18 Hebron Way, St. John', '8773689986', 'Active', 'Financial Crime Management Technology for Canadian Financial Institutions', 'info@verafin.com', 'Finance', 'Accounting', 'Canada', 'St. Johns', 'N/A', '2025', 'N/A', 0x636f6d70616e795f6c6f676f2f4e41534441515f4c6f676f2e7376672e706e67),
(2, 'ScotiaBank', 'St. John', '78576337485', 'Active', 'Earn up to $700 when you bundle an eligible banking package, savings account, and registered account', 'info@scotiabank.com', 'Finance', 'Accounting', 'Canada', 'St. John', 'N/A', '2025', 'N/A', 0x636f6d70616e795f6c6f676f2f73636f74696162616e6b2d6c6f676f2d7265642d6465736b746f702d32303070782e737667),
(3, 'RBC Royalbank', 'St. John', '7089574856', 'Active', 'Explore the personal banking services and products available from RBC Royal Bank to help you manage your finances, buy a home, invest and more.', 'info@rbcroyalbank.com', 'Finance', 'Public Sector', 'Canada', 'St. John', 'N/A', '2025', 'N/A', 0x636f6d70616e795f6c6f676f2f7262632d6c6f676f2d736869656c642e737667),
(4, 'ColabSoftware', '484 Torbay', '758475834', 'Active', ' Deliver better products, faster.', 'info@colabsoftware', 'IT & Software', 'Accounting', 'Select Country', 'St. John', 'N/A', '2025', 'N/A', 0x636f6d70616e795f6c6f676f2f436f4c61622d4c6f676f2d5247422d7765622d66756c6c2d636f6c6f75722e77656270);

-- --------------------------------------------------------

--
-- Table structure for table `tblemployees`
--

CREATE TABLE `tblemployees` (
  `INCID` int(11) NOT NULL,
  `EMPLOYEEID` varchar(30) NOT NULL,
  `FNAME` varchar(50) NOT NULL,
  `LNAME` varchar(50) NOT NULL,
  `MNAME` varchar(50) NOT NULL,
  `ADDRESS` varchar(90) NOT NULL,
  `BIRTHDATE` date NOT NULL,
  `BIRTHPLACE` varchar(90) NOT NULL,
  `AGE` int(11) NOT NULL,
  `SEX` varchar(30) NOT NULL,
  `CIVILSTATUS` varchar(30) NOT NULL,
  `TELNO` varchar(40) NOT NULL,
  `EMP_EMAILADDRESS` varchar(90) NOT NULL,
  `CELLNO` varchar(30) NOT NULL,
  `POSITION` varchar(50) NOT NULL,
  `WORKSTATS` varchar(90) NOT NULL,
  `EMPPHOTO` varchar(255) NOT NULL,
  `EMPUSERNAME` varchar(90) NOT NULL,
  `EMPPASSWORD` varchar(125) NOT NULL,
  `DATEHIRED` date NOT NULL,
  `COMPANYID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblfeedback`
--

CREATE TABLE `tblfeedback` (
  `FEEDBACKID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `ADMINID` int(11) NOT NULL,
  `SENTBY` int(11) NOT NULL,
  `FEEDBACK` text NOT NULL,
  `DATETIME` datetime NOT NULL,
  `STATUS` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbljob`
--

CREATE TABLE `tbljob` (
  `JOBID` int(11) NOT NULL,
  `COMPANYID` int(11) NOT NULL,
  `WORKPLACE_POLICY` varchar(50) NOT NULL,
  `JOBTITLE` varchar(90) NOT NULL,
  `JOBCATEGORYID` int(11) NOT NULL,
  `SALARY` double NOT NULL,
  `JOBTYPE` varchar(50) NOT NULL,
  `QUALIFICATION` varchar(50) NOT NULL,
  `JOBDESCRIPTION` text NOT NULL,
  `PREFEREDSEX` varchar(30) NOT NULL,
  `CAREERLEVEL` varchar(20) NOT NULL,
  `WORKEXPERIENCE` varchar(50) NOT NULL,
  `DEADLINE` date NOT NULL,
  `JOBSTATUS` varchar(90) NOT NULL,
  `DATEPOSTED` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbljob`
--

INSERT INTO `tbljob` (`JOBID`, `COMPANYID`, `WORKPLACE_POLICY`, `JOBTITLE`, `JOBCATEGORYID`, `SALARY`, `JOBTYPE`, `QUALIFICATION`, `JOBDESCRIPTION`, `PREFEREDSEX`, `CAREERLEVEL`, `WORKEXPERIENCE`, `DEADLINE`, `JOBSTATUS`, `DATEPOSTED`) VALUES
(1, 2, 'On-site', 'Account Manager', 1, 0, 'Full Time', 'Master Degree', 'Account management job', 'Male', 'Team leader', '3 Years', '2025-11-01', 'Vacancy', '2025-10-23 02:35:49'),
(2, 3, 'On-site', 'Sales Attendant', 2, 0, 'Full Time', 'B.Sc', 'Sales attendant and account keeping', 'Both', 'Manager', '4 Years', '2025-11-05', 'Vacancy', '2025-10-23 02:37:47'),
(3, 4, 'Hybrid', 'Senior Software Developer', 21, 20, 'Part Time', 'B.Sc', 'Senior software developer for the development and management of web applications', 'Both', 'Not Necessary', '2 Years', '2025-11-08', 'Vacancy', '2025-10-23 02:39:51'),
(4, 3, 'On-site', 'Account Executive', 3, 0, 'Full Time', 'Master Degree', 'Account executive for financial analysis & modeling', 'Both', 'Manager', '2 Years', '2025-11-15', 'Vacancy', '2025-10-23 02:42:12'),
(5, 1, 'On-site', 'Analyst', 3, 30, 'Full Time', 'B.Sc', 'Financial Analysis and Modeling ', 'Both', 'Team leader', '4 Years', '2025-11-07', 'Vacancy', '2025-10-23 04:44:58');

-- --------------------------------------------------------

--
-- Table structure for table `tbljobapplication`
--

CREATE TABLE `tbljobapplication` (
  `ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `RESUME_FILE` varchar(255) NOT NULL,
  `APPLICATIONSTATUS` varchar(20) NOT NULL,
  `APPLICATIONDATE` datetime NOT NULL,
  `SCORE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbljobapplication`
--

INSERT INTO `tbljobapplication` (`ID`, `APPLICANTID`, `JOBID`, `RESUME_FILE`, `APPLICATIONSTATUS`, `APPLICATIONDATE`, `SCORE`) VALUES
(1, 321, 4, 'resumes_cv/CV-linda.pdf', 'Pending', '2025-10-23 04:36:00', 0),
(2, 321, 1, 'resumes_cv/Resume_LINDA.pdf', 'Pending', '2025-10-23 17:20:44', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbljobcategory`
--

CREATE TABLE `tbljobcategory` (
  `ID` int(11) NOT NULL,
  `CATEGORY` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbljobcategory`
--

INSERT INTO `tbljobcategory` (`ID`, `CATEGORY`) VALUES
(1, 'Financial Services & Software'),
(2, 'Transmission & Distribution'),
(3, 'Switchgear/ Electrical Engineering'),
(4, 'Operational & Technical'),
(5, 'Web, Mobile, and Software Development'),
(6, 'Writing'),
(7, 'Translation'),
(9, 'Sales and Marketing'),
(11, 'Legal'),
(12, 'IT and Networking'),
(13, 'Engineering and Architecture'),
(14, 'Design and Creative'),
(15, 'Data Science and Analysis'),
(16, 'Customer Service'),
(17, 'Admin Support'),
(18, 'Accounting & Consulting');

-- --------------------------------------------------------

--
-- Table structure for table `tbljobregistration`
--

CREATE TABLE `tbljobregistration` (
  `REGISTRATIONID` int(11) NOT NULL,
  `COMPANYID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `APPLICANT` varchar(90) NOT NULL,
  `REGISTRATIONDATE` date NOT NULL,
  `REMARKS` varchar(255) NOT NULL DEFAULT 'Pending',
  `FILEID` varchar(30) DEFAULT NULL,
  `PENDINGAPPLICATION` tinyint(1) NOT NULL DEFAULT 1,
  `HVIEW` tinyint(1) NOT NULL DEFAULT 1,
  `DATETIMEAPPROVED` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbljobscreening_ques`
--

CREATE TABLE `tbljobscreening_ques` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbljobscreening_ques`
--

INSERT INTO `tbljobscreening_ques` (`id`, `job_id`, `question_id`) VALUES
(52, 4, 5),
(53, 4, 4),
(54, 4, 2),
(58, 5, 2),
(59, 5, 4),
(60, 5, 5),
(63, 3, 5),
(64, 3, 4),
(65, 6, 5),
(66, 6, 4),
(67, 6, 2),
(68, 7, 5),
(69, 7, 4),
(70, 8, 5),
(71, 8, 4),
(72, 8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbljobsubcategory`
--

CREATE TABLE `tbljobsubcategory` (
  `ID` int(11) NOT NULL,
  `CATEGORYID` int(11) NOT NULL,
  `SUBCATEGORY` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbljobsubcategory`
--

INSERT INTO `tbljobsubcategory` (`ID`, `CATEGORYID`, `SUBCATEGORY`) VALUES
(1, 18, 'Accounting'),
(2, 18, 'Bookkeeping'),
(3, 18, 'Financial Analysis & Modeling'),
(4, 17, 'Data Entry'),
(5, 17, 'Dropshipping & Order Processing'),
(6, 17, 'Transcription'),
(7, 16, 'Community Management'),
(8, 16, 'Content Moderation'),
(9, 16, 'Visual Tagging & Processing'),
(10, 15, 'Deep Learning'),
(11, 15, 'Knowledge Representation'),
(12, 15, 'Machine Learning'),
(13, 14, 'Caricatures & Portraits'),
(14, 14, 'Cartoons & Comics'),
(15, 14, 'Fine Art'),
(16, 13, '3D Modeling & Rendering'),
(17, 13, 'CAD'),
(18, 13, 'Architectural Design'),
(19, 5, 'Desktop Software Development'),
(20, 5, 'Mobile App Development'),
(21, 5, 'Web Design & Development'),
(22, 6, 'Content Writing'),
(23, 6, 'Editing & Proofreading'),
(24, 6, 'Technical Writing'),
(25, 6, 'Creative Writing');

-- --------------------------------------------------------

--
-- Table structure for table `tblnotification`
--

CREATE TABLE `tblnotification` (
  `ID` int(11) NOT NULL,
  `USERID` int(11) NOT NULL,
  `TYPE` varchar(50) NOT NULL,
  `TYPEID` int(11) NOT NULL,
  `STATUS` varchar(50) NOT NULL,
  `DATETIME` datetime NOT NULL,
  `NOTE` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblscreening`
--

CREATE TABLE `tblscreening` (
  `id` int(11) NOT NULL,
  `q_title` varchar(200) NOT NULL,
  `question` text NOT NULL,
  `opt_A` varchar(50) NOT NULL,
  `opt_B` varchar(50) NOT NULL,
  `opt_C` varchar(50) NOT NULL,
  `opt_D` varchar(50) NOT NULL,
  `opt_E` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblscreening`
--

INSERT INTO `tblscreening` (`id`, `q_title`, `question`, `opt_A`, `opt_B`, `opt_C`, `opt_D`, `opt_E`, `status`) VALUES
(6, 'Programing', 'Which of the following languages is more suited to a structured program?', 'PL/1', 'FORTRAN', 'BASIC', 'PASCAL', 'None of the above.', 'Active'),
(7, 'Computer System', 'The brain of any computer system is?', 'ALU', 'Memory', 'CPU', 'Control unit', 'None of the above', 'Active'),
(8, 'Computer System', 'A computer assisted method for the recording and analyzing of existing or hypothetical systems is?', 'Data transmission', 'Data flow', 'Data capture', 'Data processing', 'None of the above', 'Active'),
(9, 'Computer Generation', 'What difference does the 5th generation computer have from other generation computers?', 'Technological advancement', 'Scientific code', 'Object Oriented Programming', 'All of the above', 'None of the above', 'Active'),
(10, 'Programing Language', 'Which of the following computer language is used for artificial intelligence?', 'FORTRAN', 'PROLOG', 'C', 'COBOL', 'None of the above', 'Active'),
(11, 'Programing', 'A technique used by codes to convert an analog signal into a digital bit stream is known as', 'Pulse code modulation', 'Pulse stretcher', 'Query processing', 'Queue management', 'None of the above', 'Active'),
(12, 'Computer System', 'An optical input device that interprets pencil marks on paper media is?', 'O.M.R', 'Punch card reader', 'Optical scanners', 'Magnetic tape', 'None of the above', 'Active'),
(13, 'Programing', 'Most important advantage of an IC is its?', 'Easy replacement in case of Circuit failure', 'Extremely high reliability', 'Reduced cost', 'Low power consumption', 'None of the above', 'Active'),
(14, 'Programing', 'Data division is the third division of a _____ program.', 'COBOL', 'BASIC', 'PASCAL', 'FORTH', 'None of the above', 'Active'),
(15, 'Programing Language', 'Which language was devised by Dr. Seymour Aubrey Papert?', 'APL', 'COBOL', 'LOGO', 'FORTRAN', 'None of the above', 'Active'),
(16, 'Computer System', 'As compared to diskettes, the hard disks are?', 'More expensive', 'More portable', 'Less rigid', 'Slowly accessed', 'None of the above', 'Active'),
(17, 'Computer System', 'What is the name given to the molecular-scale computer?', 'Femtocomputer', 'Nanocomputer', 'Supercomputer', 'Microcomputer', 'None of the above', 'Active'),
(18, 'Computer System', 'Who is considered the FATHER of the minicomputer and one of the founder fathers of the modern computer industry world-wide?', 'George Tate', 'Kenneth H. Olsen', 'Seymour Cray', 'Basic Pascal', 'None of the above', 'Active'),
(19, 'Computer System', 'The first microprocessors produced by Intel Corpn. and Texas Instruments were used primarily to control small', 'Microwave ovens', 'Washing machines', 'Calculators', 'Personal computers', 'Robotics', 'Active'),
(20, 'Computer System', 'Which printer uses a combination of laser-beam & electro photographic techniques.', 'Laser printers', 'Dot-Matrix', 'Line printer', 'Daisy wheel', 'None of the above', 'Active'),
(21, 'Computer System', 'The access method used for cassette tape is?', 'Direct', 'Random', 'Sequential', 'All of the above', 'None of the above', 'Active'),
(22, 'Data Struture', 'The arranging of data in a logical sequence is called:?', 'Sorting', 'Classifying', 'Reproducing', 'Summarizing', 'None of the above', 'Active'),
(23, 'Programing Language', 'Who is the creator of the PASCAL language?', 'Niklaus Wirth', 'Dijkstra', 'Donald Knuth', 'Basic Pascal', 'None of the above', 'Active'),
(24, 'Computer System', 'When was punched-card equipment used for the first time to process the British census?', '1910', '1907', '1911', '1914', 'None of the above', 'Active'),
(25, 'Computer System', 'A hashing scheme is used with?', 'Sequential file organization', 'Direct file organization', 'Indexed sequential file organization', 'Partitioned file organization', 'None of the above', 'Active'),
(26, 'Computer History', 'What was the total number of UNIVAC-I sold eventually and by which company?', '30, British Tabulating Machine Co. (BTM)', '40, International Business Machines (IBM)', '48, Remington Rand', '40, International Computer Ltd. (ICL)', 'None of the above', 'Active'),
(27, 'Computer Software', 'A file containing relatively permanent data is?', 'Random file', 'Transaction file', 'Master file', 'Sequential file', 'None of the above', 'Active'),
(28, 'Computer Networking', 'Communication that involves computers, establishing a link through the telephone system is called?', 'Teleprocessing', 'Microprocessing', 'Telecommunications', 'All of the above', 'None of the above', 'Active'),
(29, 'Computer Hardware', 'Dot-matrix is a type of?', 'Tape', 'Printer', 'Disk', 'Bus', 'None of the above', 'Active'),
(30, 'Computer Hardware', 'Which kind of devices allows the user to add components and capabilities to a computer system?', 'System boards', 'Storage devices', 'Input devices', 'Output devices', 'Expansion slots', 'Active'),
(31, 'Digital Design', 'When an input electrical signal A=10100 is applied to a NOT gate, its output signal is?', '01011', '10001', '10101', '00101', 'None of the above', 'Active'),
(32, 'Computer History', 'The first practical commercial typewriter was invented in 1867 in the United States by', 'Christopher Latham Sholes', 'Carlos Glidden', 'Samuel Soule', 'All of the above', 'None of the above', 'Active'),
(33, 'Computer System', 'What is meant by quad-density (QD) diskette?', 'It is double-sided disk', 'It is double density disk', 'It has double the number of tracks per inch', 'All of the above', 'None of the above', 'Active'),
(34, 'Computer System', 'Large computer system typically uses:?', 'Line printers', 'Ink-jet printers', 'Dot-matrix printers', 'Daisy wheel printers', 'None of the above', 'Active'),
(35, 'Computer Generation', 'First generation computers are characterised by?', 'Vaccum tubes and magnetic drum', 'Minicomputers', 'Magnetic tape and transistors', 'All of the above', 'None of the above', 'Active'),
(36, 'Computer Generation', 'A typical modern computer uses?', 'LSI chips', 'Vacuum tubes', 'Valves', 'All of the above', 'None of the above', 'Active'),
(37, 'Computer Generation', 'ENIAC (Electronic Numerical Integrator and Calculator) had huge advantage over Mark I because it used electronic valves in place of the electromagnetic switches. In the beginning, ENIAC was used for calculating the path of artillery shells. For which other was weapon design was it utilized?', 'Hydrogen bomb', 'Atom bomb', 'Missile', 'Fighter aircraft', 'None of the above', 'Active'),
(38, 'Computer History', 'Who was the father of Punched Card Processing?', 'J Presper Eckert', 'Charles Babbage', 'Dr. Herman Hollerith', 'Blaise Pascal', 'None of the above', 'Active'),
(39, 'Computer History', 'When did Hewlett-Packard Inc. Introduce its first HP-110 laptop computer?', '1984', '1986', '1990', '1995', 'None of the above', 'Active'),
(40, 'Programing Language', 'The computer program language which is widely used in computer science and engineering and also in business is?', 'COBOL', 'FORTRAN', 'PASCAL', 'LISP', 'None of the above', 'Active'),
(41, 'Computer System', 'Which of the following bus types are used by the Apple Macintosh computer?', 'ISA', 'NuBus', 'EISA', 'MCA', 'PCI Bus', 'Active'),
(42, 'Data Struture', 'When the time to establish link is large and the size of data is small, the preferred mode of data transfer is?', 'Circuit switching', 'Packet switching', 'Time division multiplexing', 'All of the above', 'None of the above', 'Active'),
(43, 'Computer Programing', 'Group of instructions that directs a computer is called?', 'Storage', 'Memory', 'Logic', 'Program', 'None of the above', 'Active'),
(44, 'Computer System', 'A computer-controlled device for training exercises that duplicates the work environment is a:?', 'Simulator', 'Duplicator', 'Trainer', 'COM device', 'None of the above', 'Active'),
(45, 'Computer System', 'Which of the following is not an output device of a computer?', 'Printer', 'keyboard', 'VDU', 'CRT screen', 'All of the above', 'Active'),
(46, 'System Design', 'In negative logic, the logic state 1 corresponds to?', 'Negative voltage', 'Zero voltage', 'More negative voltage', 'Lower voltage level', 'None of the above', 'Active'),
(47, 'Computer System', 'Which of the following is a part of the Central Processing Unit?', 'Printer', 'Keyboard', 'Mouse', 'Arithmetic Logic Unit', 'None of the above', 'Active'),
(48, 'Computer System', 'The least expensive OCR units can read?', 'Hand printed numbers', 'Machine printed numbers', 'Marks', 'Handwriting', 'None of the above', 'Active'),
(49, 'Computer System', 'The input unit of a computer?', 'Feeds data to the CPU or memory', 'Retrieves data from CPU', 'Directs all other units', 'All of the above', 'None of the above', 'Active'),
(50, 'Computer Organisation', 'File specification books are created primarily for the use of?', 'Systems analysts', 'Programmers', 'Operators', 'Managers', 'None of the above', 'Active'),
(51, 'Computer Oparation', 'Which of the following is usually a special one-time operation that must be completed over a limited time period?', 'Batch', 'Patch', 'Project', 'Word', 'None of the above', 'Active'),
(52, 'Computer Architecture', 'A device or system not directly connected to the CPU is?', 'On-line', 'Keyboard', 'Memory', 'Off-line', 'None of the above', 'Active'),
(53, 'Computer System', 'The microprocessor of a computer cannot operate on any information if that information is not in its.?', 'Secondary storage', 'Main storage', 'ALU', 'Logic unit', 'None of the above', 'Active'),
(54, 'Compiler Construction', 'A device which converts human readable data into machine language is?', 'Card reader', 'Card punch', 'Punched paper tape', 'Character reader', 'None of the above', 'Active'),
(55, 'Digital Design', 'What digits are representative of all binary numbers?', '0', '1', 'Both (a) and (b)', '3', 'None of the above', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `tblscreening_answer`
--

CREATE TABLE `tblscreening_answer` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `ideal_ans_opt` varchar(11) NOT NULL,
  `ideal_ans` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblscreening_answer`
--

INSERT INTO `tblscreening_answer` (`id`, `question_id`, `ideal_ans_opt`, `ideal_ans`) VALUES
(1, 6, 'D', 'PASCAL'),
(2, 7, 'C', 'CPU'),
(3, 8, 'B', 'Data flow'),
(4, 9, 'A', 'Technological advancement'),
(5, 10, 'C', 'C'),
(6, 11, 'A', 'Pulse code modulation'),
(7, 12, 'A', 'O.M.R'),
(8, 13, 'B', 'Extremely high reliability'),
(9, 14, 'A', 'COBOL'),
(10, 15, 'C', 'LOGO'),
(11, 16, 'A', 'More expensive'),
(12, 17, 'B', 'Nanocomputer'),
(13, 18, 'B', 'Kenneth H. Olsen'),
(14, 19, 'C', 'Calculators'),
(15, 20, 'A', 'Laser printers'),
(16, 21, 'C', 'Sequential'),
(17, 22, 'A', 'Sorting'),
(18, 23, 'A', 'Niklaus Wirth'),
(19, 24, 'C', '1911'),
(20, 25, 'B', 'Direct file organization'),
(21, 26, 'C', '48, Remington Rand'),
(22, 27, 'C', 'Master file'),
(23, 28, 'C', 'Telecommunications'),
(24, 29, 'B', 'Printer'),
(25, 30, 'E', 'Expansion slots'),
(26, 31, 'A', '01011'),
(27, 32, 'D', 'All of the above'),
(28, 33, 'D', 'All of the above'),
(29, 34, 'A', 'Line printers'),
(30, 35, 'A', 'Vaccum tubes and magnetic drum'),
(31, 36, 'A', 'LSI chips'),
(32, 37, 'A', 'Hydrogen bomb'),
(33, 38, 'C', 'Dr. Herman Hollerith'),
(34, 39, 'A', '1984'),
(35, 40, 'C', 'PASCAL'),
(36, 41, 'B', 'NuBus'),
(37, 42, 'B', 'Packet switching'),
(38, 43, 'D', 'Program'),
(39, 44, 'A', 'Simulator'),
(40, 45, 'B', 'keyboard'),
(41, 46, 'D', 'Lower voltage level'),
(42, 47, 'D', 'Arithmetic Logic Unit'),
(43, 48, 'C', 'Marks'),
(44, 49, 'A', 'Feeds data to the CPU or memory'),
(45, 50, 'B', 'Programmers'),
(46, 51, 'C', 'Project'),
(47, 52, 'D', 'Off-line'),
(48, 53, 'B', 'Main storage'),
(49, 54, 'D', 'Character reader'),
(50, 55, 'C', 'Both (a) and (b)');

-- --------------------------------------------------------

--
-- Table structure for table `tblscreening_qa`
--

CREATE TABLE `tblscreening_qa` (
  `ID` int(11) NOT NULL,
  `JOBAPPLICATION_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `QUESTION_ID` int(11) NOT NULL,
  `APPLICANT_ANSWER` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblscreening_qa`
--

INSERT INTO `tblscreening_qa` (`ID`, `JOBAPPLICATION_ID`, `APPLICANTID`, `JOBID`, `QUESTION_ID`, `APPLICANT_ANSWER`) VALUES
(1, 15, 2745, 4, 21, 'C'),
(2, 15, 2745, 4, 17, 'C'),
(3, 15, 2745, 4, 45, 'B'),
(4, 15, 2745, 4, 9, 'A'),
(5, 15, 2745, 4, 31, 'A'),
(6, 15, 2745, 4, 32, 'A'),
(7, 15, 2745, 4, 30, 'E'),
(8, 15, 2745, 4, 13, 'B'),
(9, 15, 2745, 4, 35, 'A'),
(10, 15, 2745, 4, 14, 'D'),
(11, 16, 2745, 3, 38, 'B'),
(12, 16, 2745, 3, 15, 'C'),
(13, 16, 2745, 3, 26, 'C'),
(14, 16, 2745, 3, 19, 'E'),
(15, 16, 2745, 3, 40, 'C'),
(16, 16, 2745, 3, 9, 'B'),
(17, 16, 2745, 3, 41, 'C'),
(18, 16, 2745, 3, 21, 'B'),
(19, 16, 2745, 3, 24, 'A'),
(20, 16, 2745, 3, 29, 'D'),
(21, 17, 321, 6, 21, 'C'),
(22, 17, 321, 6, 29, 'B'),
(23, 17, 321, 6, 38, 'E'),
(24, 17, 321, 6, 22, 'C'),
(25, 17, 321, 6, 36, 'D'),
(26, 17, 321, 6, 45, 'D'),
(27, 17, 321, 6, 52, 'D'),
(28, 17, 321, 6, 33, 'C'),
(29, 17, 321, 6, 11, 'C'),
(30, 17, 321, 6, 30, 'C');

-- --------------------------------------------------------

--
-- Table structure for table `tblscreening_score`
--

CREATE TABLE `tblscreening_score` (
  `id` int(11) NOT NULL,
  `jobapp_id` int(11) NOT NULL,
  `score` varchar(10) NOT NULL,
  `total_ques` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblscreening_score`
--

INSERT INTO `tblscreening_score` (`id`, `jobapp_id`, `score`, `total_ques`) VALUES
(1, 15, '7', '10'),
(2, 16, '3', '10'),
(3, 17, '3', '10');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `USERID` int(30) NOT NULL,
  `IDN` varchar(30) NOT NULL,
  `FNAME` varchar(40) NOT NULL,
  `ONAME` varchar(200) NOT NULL,
  `EMAIL` varchar(50) NOT NULL,
  `USERNAME` varchar(90) NOT NULL,
  `PASS` varchar(90) NOT NULL,
  `ROLE` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`USERID`, `IDN`, `FNAME`, `ONAME`, `EMAIL`, `USERNAME`, `PASS`, `ROLE`) VALUES
(228, '', 'Admin', 'Manager', 'admin@gmail.com', 'admin', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Administrator'),
(321, '', 'Dom', 'Carl', 'emehchiemerie9@gmail.com', 'emeldo', '$2y$10$6u.ZUBDOBuQZ4K2GzFjD3.Z8e.StDKKCpeuoodzOFeyXX74A.NJ92', 'Applicant'),
(2749, '11', 'James', 'Cletus', 'emelcarl2023@gmail.com', 'emelcarl2023', '$2y$10$4r2eYwzVBwTSnGM4B8MJ5u/.Wrjxqk2oYpCn30cmWIMM/r5cRpUnC', 'Applicant'),
(2757, '16', 'demo', 'testing', 'demo@gmail.com', 'demo', '$2y$10$QFbUsQ//d3vKO2SFFR3LGeUAmj99MAYS6x2w73NeUDPL3ISMN.Gg6', 'Applicant');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblapplicants`
--
ALTER TABLE `tblapplicants`
  ADD PRIMARY KEY (`APPLICANTID`);

--
-- Indexes for table `tblattachmentfile`
--
ALTER TABLE `tblattachmentfile`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblautonumbers`
--
ALTER TABLE `tblautonumbers`
  ADD PRIMARY KEY (`AUTOID`);

--
-- Indexes for table `tblbookmarkjob`
--
ALTER TABLE `tblbookmarkjob`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblbookmarkresume`
--
ALTER TABLE `tblbookmarkresume`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblcompany`
--
ALTER TABLE `tblcompany`
  ADD PRIMARY KEY (`COMPANYID`);

--
-- Indexes for table `tblemployees`
--
ALTER TABLE `tblemployees`
  ADD PRIMARY KEY (`INCID`),
  ADD UNIQUE KEY `EMPLOYEEID` (`EMPLOYEEID`);

--
-- Indexes for table `tblfeedback`
--
ALTER TABLE `tblfeedback`
  ADD PRIMARY KEY (`FEEDBACKID`);

--
-- Indexes for table `tbljob`
--
ALTER TABLE `tbljob`
  ADD PRIMARY KEY (`JOBID`);

--
-- Indexes for table `tbljobapplication`
--
ALTER TABLE `tbljobapplication`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbljobcategory`
--
ALTER TABLE `tbljobcategory`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbljobregistration`
--
ALTER TABLE `tbljobregistration`
  ADD PRIMARY KEY (`REGISTRATIONID`);

--
-- Indexes for table `tbljobscreening_ques`
--
ALTER TABLE `tbljobscreening_ques`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbljobsubcategory`
--
ALTER TABLE `tbljobsubcategory`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblnotification`
--
ALTER TABLE `tblnotification`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblscreening`
--
ALTER TABLE `tblscreening`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblscreening_answer`
--
ALTER TABLE `tblscreening_answer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblscreening_qa`
--
ALTER TABLE `tblscreening_qa`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblscreening_score`
--
ALTER TABLE `tblscreening_score`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`USERID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblapplicants`
--
ALTER TABLE `tblapplicants`
  MODIFY `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblattachmentfile`
--
ALTER TABLE `tblattachmentfile`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblautonumbers`
--
ALTER TABLE `tblautonumbers`
  MODIFY `AUTOID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblbookmarkjob`
--
ALTER TABLE `tblbookmarkjob`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblbookmarkresume`
--
ALTER TABLE `tblbookmarkresume`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcompany`
--
ALTER TABLE `tblcompany`
  MODIFY `COMPANYID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblemployees`
--
ALTER TABLE `tblemployees`
  MODIFY `INCID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblfeedback`
--
ALTER TABLE `tblfeedback`
  MODIFY `FEEDBACKID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbljob`
--
ALTER TABLE `tbljob`
  MODIFY `JOBID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbljobapplication`
--
ALTER TABLE `tbljobapplication`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbljobcategory`
--
ALTER TABLE `tbljobcategory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tbljobregistration`
--
ALTER TABLE `tbljobregistration`
  MODIFY `REGISTRATIONID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbljobscreening_ques`
--
ALTER TABLE `tbljobscreening_ques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `tbljobsubcategory`
--
ALTER TABLE `tbljobsubcategory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tblnotification`
--
ALTER TABLE `tblnotification`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblscreening`
--
ALTER TABLE `tblscreening`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `tblscreening_answer`
--
ALTER TABLE `tblscreening_answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `tblscreening_qa`
--
ALTER TABLE `tblscreening_qa`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tblscreening_score`
--
ALTER TABLE `tblscreening_score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `USERID` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2758;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
