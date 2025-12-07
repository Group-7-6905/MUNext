-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 09:57 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE PROCEDURE `cleanup_expired_tokens` ()   
BEGIN
    DELETE FROM tblpasswordreset WHERE EXPIRY < NOW();
END$$

DELIMITER ;

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
(101, 5, 'Full Stack Developer', 'WebTech Solutions', 'Junior Developer', 5001, 'Emma', 'Johnson', '45 Patrick Street', 'St. Johns', 'Canada', 'Female', '1998-03-15', 'Passionate full-stack developer with 2 years of experience in React and Node.js. Love building scalable web applications.', 'ejohnson', 'emma.johnson@email.com', '7095552001', 'Bachelor Degree', 'Memorial University', 'React, Node.js, JavaScript, TypeScript, MongoDB, PostgreSQL, Git', '', 'https://fb.com/ejohnson', 'https://linkedin.com/in/emmajohnson'),
(102, 15, 'Data Analyst', 'Analytics Corp', 'Data Intern', 5002, 'Noah', 'Williams', '78 Forest Road', 'St. Johns', 'Canada', 'Male', '1999-07-22', 'Detail-oriented data analyst skilled in Python, SQL, and data visualization. Experienced with Tableau and Power BI.', 'nwilliams', 'noah.williams@email.com', '7095552002', 'Bachelor Degree', 'Memorial University', 'Python, SQL, Tableau, Power BI, Excel, Statistics, Machine Learning', '', 'https://fb.com/nwilliams', 'https://linkedin.com/in/noahwilliams'),
(103, 9, 'Marketing Coordinator', 'BrandWorks', 'Marketing Assistant', 5003, 'Olivia', 'Martinez', '92 Churchill Avenue', 'St. Johns', 'Canada', 'Female', '1997-11-08', 'Creative marketing professional with expertise in digital marketing, content creation, and social media management.', 'omartinez', 'olivia.martinez@email.com', '7095552003', 'Bachelor Degree', 'Memorial University', 'Digital Marketing, SEO, Content Writing, Social Media, Google Analytics, Canva', '', 'https://fb.com/omartinez', 'https://linkedin.com/in/oliviamartinez'),
(104, 14, 'UX/UI Designer', 'PixelPerfect Studio', 'Junior Designer', 5004, 'Liam', 'Anderson', '156 Empire Avenue', 'St. Johns', 'Canada', 'Male', '1998-05-19', 'Creative UX/UI designer focused on creating intuitive and beautiful user experiences. Proficient in Figma and Adobe Creative Suite.', 'landerson', 'liam.anderson@email.com', '7095552004', 'Bachelor Degree', 'Memorial University', 'Figma, Adobe XD, Sketch, Photoshop, Illustrator, User Research, Prototyping', '', 'https://fb.com/landerson', 'https://linkedin.com/in/liamanderson'),
(105, 18, 'Financial Analyst', 'Capital Investments', 'Financial Intern', 5005, 'Ava', 'Taylor', '234 Topsail Road', 'St. Johns', 'Canada', 'Female', '1999-01-25', 'Analytical finance professional with strong skills in financial modeling, forecasting, and investment analysis.', 'ataylor', 'ava.taylor@email.com', '7095552005', 'Bachelor Degree', 'Memorial University', 'Financial Modeling, Excel, Bloomberg, Financial Analysis, Risk Management', '', 'https://fb.com/ataylor', 'https://linkedin.com/in/avataylor'),
(106, 12, 'Network Administrator', 'NetSolutions Ltd', 'IT Support', 5006, 'Ethan', 'Thomas', '567 Blackmarsh Road', 'St. Johns', 'Canada', 'Male', '1997-09-12', 'Experienced network administrator with expertise in Cisco, network security, and system administration.', 'ethomas', 'ethan.thomas@email.com', '7095552006', 'Bachelor Degree', 'Memorial University', 'Cisco, Network Security, Windows Server, Linux, TCP/IP, Firewall Configuration', '', 'https://fb.com/ethomas', 'https://linkedin.com/in/ethanthomas'),
(107, 6, 'Content Writer', 'WordSmith Agency', 'Junior Writer', 5007, 'Sophia', 'Jackson', '890 Merrymeeting Road', 'St. Johns', 'Canada', 'Female', '1998-12-03', 'Creative content writer specializing in blog posts, website copy, and technical documentation. Published 200+ articles.', 'sjackson', 'sophia.jackson@email.com', '7095552007', 'Bachelor Degree', 'Memorial University', 'Content Writing, Copywriting, SEO Writing, WordPress, Editing, Research', '', 'https://fb.com/sjackson', 'https://linkedin.com/in/sophiajackson'),
(108, 13, 'Civil Engineer', 'BuildRight Construction', 'Junior Engineer', 5008, 'Mason', 'White', '123 Old Placentia Road', 'St. Johns', 'Canada', 'Male', '1997-04-28', 'Civil engineering graduate with experience in structural design, project management, and AutoCAD.', 'mwhite', 'mason.white@email.com', '7095552008', 'Bachelor Degree', 'Memorial University', 'AutoCAD, Civil 3D, Structural Analysis, Project Management, Construction Management', '', 'https://fb.com/mwhite', 'https://linkedin.com/in/masonwhite'),
(109, 16, 'Customer Service Representative', 'ServicePro', 'Customer Support', 5009, 'Isabella', 'Harris', '456 Logy Bay Road', 'St. Johns', 'Canada', 'Female', '1999-06-14', 'Customer-focused professional with excellent communication skills and 1 year of experience in customer service.', 'iharris', 'isabella.harris@email.com', '7095552009', 'Associate Degree', 'College of North Atlantic', 'Customer Service, Communication, Problem Solving, CRM Software, Conflict Resolution', '', 'https://fb.com/iharris', 'https://linkedin.com/in/isabellaharris'),
(110, 17, 'Administrative Assistant', 'Office Solutions', 'Office Clerk', 5010, 'Lucas', 'Martin', '789 Columbus Drive', 'St. Johns', 'Canada', 'Male', '1998-08-30', 'Organized administrative professional with strong organizational and multitasking skills. Proficient in Microsoft Office Suite.', 'lmartin', 'lucas.martin@email.com', '7095552010', 'Associate Degree', 'College of North Atlantic', 'Microsoft Office, Data Entry, Scheduling, Office Management, Communication', '', 'https://fb.com/lmartin', 'https://linkedin.com/in/lucasmartin'),
(118, 3, 'Associate', 'MacDonald', 'Web Developer', 321, 'Dominic', 'Carl', '549 Newfoundland Drive', 'St. John\'s', 'Canada', 'Male', '2004-12-04', 'About me\'sdgh', 'emeldo', 'emehchiemerie9@gmail.com', '1234567890', 'P.H.D.', 'Memorial University', 'PHP, Software Development, Web Developer, MSQL, HTML, WordPress, TailWind', 'profile/applicant_321_1764823564.png', '', '');

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

--
-- Dumping data for table `tblattachmentfile`
--

INSERT INTO `tblattachmentfile` (`ID`, `APPLICANTID`, `JOBID`, `FILE_NAME`, `FILE_LOCATION`) VALUES
(1, 4, 12, 'Alex_CV_latest.pdf', 'uploads/resumes/4_12_cv.pdf'),
(2, 5, 13, 'Betty_Resume.docx', 'uploads/resumes/5_13_resume.docx'),
(3, 4, 14, 'Alex_Portfolio.zip', 'uploads/portfolios/4_14_port.zip');

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

--
-- Dumping data for table `tblbookmarkjob`
--

INSERT INTO `tblbookmarkjob` (`ID`, `APPLICANTID`, `JOBID`, `DATETIME`) VALUES
(401, 101, 214, '2025-02-15 10:00:00'),
(402, 101, 203, '2025-02-15 10:30:00'),
(403, 102, 201, '2025-02-16 09:00:00'),
(404, 102, 205, '2025-02-16 09:30:00'),
(405, 103, 209, '2025-02-17 14:00:00'),
(406, 104, 207, '2025-02-18 11:00:00'),
(407, 104, 201, '2025-02-18 11:30:00'),
(408, 105, 214, '2025-02-19 10:00:00'),
(409, 106, 214, '2025-02-20 13:00:00'),
(410, 107, 206, '2025-02-21 09:00:00'),
(411, 107, 215, '2025-02-21 09:30:00'),
(412, 108, 201, '2025-02-22 10:00:00'),
(413, 109, 210, '2025-02-23 14:00:00'),
(414, 110, 215, '2025-02-24 11:00:00'),
(415, 101, 215, '2025-02-25 10:00:00'),
(417, 5013, 214, '2025-12-02 19:55:28');

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

--
-- Dumping data for table `tblbookmarkresume`
--

INSERT INTO `tblbookmarkresume` (`ID`, `USERID`, `JOBAPPLICATIONID`, `JOBRESUMEID`, `DATETIME`) VALUES
(501, 4001, 301, 0, '2025-02-17 10:00:00'),
(502, 4001, 316, 0, '2025-02-27 11:00:00'),
(503, 4002, 317, 0, '2025-02-28 10:00:00'),
(504, 4003, 303, 0, '2025-02-18 10:00:00'),
(505, 4003, 320, 0, '2025-03-03 12:00:00'),
(506, 4003, 325, 0, '2025-03-08 11:00:00'),
(507, 4004, 310, 0, '2025-02-22 14:00:00'),
(508, 4004, 326, 0, '2025-03-09 11:00:00'),
(509, 4005, 309, 0, '2025-02-21 11:00:00'),
(510, 4005, 324, 0, '2025-03-07 10:00:00'),
(511, 4006, 305, 0, '2025-02-19 15:00:00'),
(512, 4006, 311, 0, '2025-02-23 10:00:00'),
(513, 4007, 307, 0, '2025-02-20 12:00:00'),
(514, 4007, 322, 0, '2025-03-05 11:00:00'),
(515, 4008, 316, 0, '2025-02-27 11:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `tblcompany`
--

CREATE TABLE `tblcompany` (
  `COMPANYID` int(11) NOT NULL,
  `USERID` int(11) NOT NULL,
  `COMPANYNAME` varchar(90) NOT NULL,
  `COMPANYADDRESS` varchar(90) NOT NULL,
  `COMPANYCONTACTNO` varchar(30) NOT NULL,
  `COMPANYSTATUS` varchar(90) NOT NULL,
  `COMPANYABOUT` text NOT NULL,
  `COMPANYEMAIL` varchar(50) NOT NULL,
  `COMPANYWEBSITE` text NOT NULL,
  `COMPANYINDUSTRY` varchar(50) NOT NULL,
  `COMPANYSPECIALISM` varchar(50) NOT NULL,
  `COMPANYCOUNTRY` varchar(50) NOT NULL,
  `COMPANYCITY` varchar(50) NOT NULL,
  `COMPANYAWARD` varchar(20) NOT NULL,
  `COMPANYYEAR` varchar(10) NOT NULL,
  `COMPANYAWARDDESC` text NOT NULL,
  `COMPANYLOGO` varchar(225) NOT NULL,
  `DATEREGISTERED` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcompany`
--

INSERT INTO `tblcompany` (`COMPANYID`, `USERID`, `COMPANYNAME`, `COMPANYADDRESS`, `COMPANYCONTACTNO`, `COMPANYSTATUS`, `COMPANYABOUT`, `COMPANYEMAIL`, `COMPANYWEBSITE`, `COMPANYINDUSTRY`, `COMPANYSPECIALISM`, `COMPANYCOUNTRY`, `COMPANYCITY`, `COMPANYAWARD`, `COMPANYYEAR`, `COMPANYAWARDDESC`, `COMPANYLOGO`, `DATEREGISTERED`) VALUES
(6, 2749, 'Ms. Brooke Ledner LTD', 'Newfoundland Drive', '', 'Active', 'Checcccckkkkkkiiinnnnngggggggg', 'admin@BrookeLedner.com', 'http://localhost/munext', 'IT & Software', 'Accounting', 'Canada', 'St. John\'s', '', '', '', 'company_logo/Adobe Express - file (7).png', NULL),
(101, 4001, 'TechCorp Solutions', '123 Water Street', '7095551001', 'Active', 'Leading software development company specializing in cloud solutions and enterprise applications. We help businesses transform digitally.', 'info@techcorp.ca', 'http://www.techcorp.ca', 'IT & Software', 'Cloud Services', 'Canada', 'St. Johns', '', '', '', '', '2025-01-15 10:00:00'),
(102, 4002, 'InnovateSoft Inc', '456 Duckworth Street', '7095551002', 'Active', 'Innovative software solutions for modern businesses. Specializing in AI and machine learning applications.', 'contact@innovatesoft.ca', 'http://www.innovatesoft.ca', 'IT & Software', 'Software Development', 'Canada', 'St. Johns', '', '', '', 'company_logo/InnovateSoft Inc.jpeg', '2025-01-18 11:30:00'),
(103, 4003, 'DataDynamics Ltd', '789 George Street', '7095551003', 'Active', 'Data analytics and business intelligence firm helping companies make data-driven decisions.', 'hello@datadynamics.ca', 'http://www.datadynamics.ca', 'IT & Software', 'Data Science', 'Canada', 'St. Johns', '', '', '', 'company_logo/Data-Dynamics-Logo.png', '2025-01-20 14:15:00'),
(104, 4004, 'CloudNine Technologies', '321 Harbour Drive', '7095551004', 'Active', 'Cloud infrastructure and DevOps solutions provider. Making cloud migration seamless and secure.', 'support@cloudnine.ca', 'http://www.cloudnine.ca', 'IT & Software', 'Cloud Infrastructure', 'Canada', 'St. Johns', '', '', '', 'company_logo/CloudNine Technologies.jpg', '2025-01-22 09:45:00'),
(105, 4005, 'FinancePro Solutions', '654 Elizabeth Avenue', '7095551005', 'Active', 'Financial technology solutions for modern banking and investment management. Trusted by 500+ institutions.', 'info@financepro.ca', 'http://www.financepro.ca', 'Financial Services & Software', 'FinTech', 'Canada', 'St. Johns', '', '', '', 'company_logo/FinancePro Solutions.png', '2025-01-25 16:20:00'),
(106, 4006, 'MarketMaster Agency', '987 Torbay Road', '7095551006', 'Active', 'Digital marketing agency specializing in social media, SEO, and content marketing strategies.', 'hello@marketmaster.ca', 'http://www.marketmaster.ca', 'Sales and Marketing', 'Digital Marketing', 'Canada', 'St. Johns', '', '', '', '', '2025-01-28 10:30:00'),
(107, 4007, 'DesignHub Creative', '147 Military Road', '7095551007', 'Active', 'Award-winning design studio creating beautiful brands and digital experiences.', 'creative@designhub.ca', 'http://www.designhub.ca', 'Design and Creative', 'Graphic Design', 'Canada', 'St. Johns', '', '', '', 'company_logo/DesignHub.jpeg', '2025-02-01 13:00:00'),
(108, 4008, 'HealthTech Innovations', '258 Newfoundland Drive', '7095551008', 'Active', 'Healthcare technology company developing innovative patient care solutions and medical software.', 'info@healthtech.ca', 'http://www.healthtech.ca', 'IT & Software', 'Healthcare Technology', 'Canada', 'St. Johns', '', '', '', 'company_logo/6751_Healthtech Innovations.jpg', '2025-02-03 15:45:00'),
(109, 4009, 'EduLearn Platform', '369 Portugal Cove Road', '7095551009', 'Pending', 'E-learning platform providing quality online courses and educational resources.', 'contact@edulearn.ca', 'http://www.edulearn.ca', 'IT & Software', 'EdTech', 'Canada', 'St. Johns', '', '', '', '', '2025-02-05 11:20:00'),
(110, 4010, 'GreenEnergy Systems', '741 Kenmount Road', '7095551010', 'Active', 'Renewable energy solutions and sustainable power systems for residential and commercial properties.', 'info@greenenergy.ca', 'http://www.greenenergy.ca', 'Engineering and Architecture', 'Renewable Energy', 'Canada', 'St. Johns', '', '', '', 'company_logo/Green-Energy-Solutions-Logo.jpg', '2025-02-08 09:00:00'),
(111, 5012, 'NexMentor', '44 rotary dr', '4168222760', 'Active', 'AI Engineer & AWS Solutions Architect with hands-on experience building, deploying, and troubleshooting cloud-native applications as a student and early-career engineer. Work spans AWS services, Python, and AI Engineering, with exposure to DevOps practices.?', 'waynettomas@gmail.com', 'https:/www.12nets.com', 'Cybersecurity', 'Cloud Computing & DevOps', 'Canada', 'st johns', '', '', '', 'company_logo/mentor.jpg', '2025-12-04 03:29:21'),
(117, 5014, 'Dimconnect ICT', 'Newfoundland Drive', '1234567890', 'Active', 'Digital marketing agency specializing in social media, SEO, and content marketing strategies.', 'Dim@gmail.com', 'https://dimconnect.com', 'Computer Hardware & Networking', 'Software Engineering', 'Canada', 'St. John\'s, Newfoundland & Labrador', '', '', '', 'company_logo/company_5014_1764831685.png', '2025-12-04 03:31:25');

-- --------------------------------------------------------

--
-- Table structure for table `tbljob`
--

CREATE TABLE `tbljob` (
  `JOBID` int(11) NOT NULL,
  `COMPANYID` int(11) NOT NULL,
  `EMPLOYERID` int(11) NOT NULL,
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

INSERT INTO `tbljob` (`JOBID`, `COMPANYID`, `EMPLOYERID`, `WORKPLACE_POLICY`, `JOBTITLE`, `JOBCATEGORYID`, `SALARY`, `JOBTYPE`, `QUALIFICATION`, `JOBDESCRIPTION`, `PREFEREDSEX`, `CAREERLEVEL`, `WORKEXPERIENCE`, `DEADLINE`, `JOBSTATUS`, `DATEPOSTED`) VALUES
(201, 101, 4001, 'Hybrid', 'Senior Full Stack Developer', 5, 85000, 'Full Time', 'Bachelor Degree', 'We are seeking an experienced Full Stack Developer to join our growing team. You will work on building scalable web applications using React, Node.js, and cloud technologies. Strong problem-solving skills and experience with modern development practices are essential.', 'Both', 'Mid-Senior Level', '3-5 Years', '2025-03-15', 'Active', '2025-02-01 09:00:00'),
(202, 101, 4001, 'Remote', 'Junior Backend Developer', 5, 55000, 'Full Time', 'Bachelor Degree', 'Looking for a motivated Junior Backend Developer to work with our development team. You will be working with Node.js, Express, and databases. Great learning opportunity with mentorship provided.', 'Both', 'Entry Level', '0-1 Years', '2026-01-01', 'Active', '2025-02-03 10:30:00'),
(203, 102, 4002, 'On-site', 'Machine Learning Engineer', 15, 95000, 'Full Time', 'Master Degree', 'Join our AI team to develop cutting-edge machine learning solutions. Experience with Python, TensorFlow, and deep learning is required. You will work on exciting projects in computer vision and NLP.', 'Both', 'Mid-Senior Level', '3-5 Years', '2025-12-25', 'Active', '2025-02-02 11:00:00'),
(204, 103, 4003, 'Hybrid', 'Data Analyst', 15, 65000, 'Full Time', 'Bachelor Degree', 'We need a Data Analyst to help us analyze business data and create insightful reports. Strong SQL skills and experience with Tableau or Power BI required. You will work closely with stakeholders to drive data-driven decisions.', 'Both', 'Entry Level', '1-2 Years', '2025-12-20', 'Active', '2025-02-04 14:00:00'),
(205, 104, 4004, 'Remote', 'DevOps Engineer', 5, 80000, 'Full Time', 'Bachelor Degree', 'Experienced DevOps Engineer needed to manage our cloud infrastructure. Must have experience with AWS, Docker, Kubernetes, and CI/CD pipelines. Automation and monitoring skills are essential.', 'Both', 'Mid-Senior Level', '3-5 Years', '2025-12-22', 'Active', '2025-02-05 09:30:00'),
(206, 105, 4005, 'On-site', 'Financial Analyst', 18, 70000, 'Full Time', 'Bachelor Degree', 'Looking for a Financial Analyst to join our finance team. You will be responsible for financial modeling, forecasting, and analysis. CFA or relevant certification is a plus.', 'Both', 'Entry Level', '1-3 Years', '2025-03-28', 'Active', '2025-02-06 10:00:00'),
(207, 106, 4006, 'Hybrid', 'Digital Marketing Specialist', 9, 58000, 'Full Time', 'Bachelor Degree', 'We are hiring a Digital Marketing Specialist to manage our social media, SEO, and content marketing campaigns. Experience with Google Analytics and social media platforms required.', 'Both', 'Entry Level', '1-2 Years', '2025-12-05', 'Active', '2025-02-07 11:30:00'),
(208, 106, 4006, 'Remote', 'Content Marketing Manager', 9, 75000, 'Full Time', 'Bachelor Degree', 'Senior Content Marketing Manager needed to lead our content strategy. You will manage a team of writers and create engaging content across multiple channels. 5+ years of experience required.', 'Both', 'Manager', '5+ Years', '2025-12-07', 'Active', '2025-02-08 13:00:00'),
(209, 107, 4007, 'On-site', 'Senior UX/UI Designer', 14, 72000, 'Full Time', 'Bachelor Degree', 'Seeking a talented UX/UI Designer to create beautiful and intuitive user experiences. Must be proficient in Figma, have a strong portfolio, and understand user-centered design principles.', 'Both', 'Mid-Senior Level', '3-5 Years', '2025-12-15', 'Active', '2025-02-09 10:00:00'),
(210, 107, 4007, 'Hybrid', 'Graphic Designer', 14, 50000, 'Full Time', 'Bachelor Degree', 'Junior Graphic Designer needed to create visual content for clients. Experience with Adobe Creative Suite required. Fresh graduates with strong portfolios are encouraged to apply.', 'Both', 'Entry Level', '0-2 Years', '2025-11-28', 'Active', '2025-02-10 14:30:00'),
(211, 108, 4008, 'Hybrid', 'Software Developer (Healthcare)', 5, 78000, 'Full Time', 'Bachelor Degree', 'Join our healthcare technology team to build innovative patient care solutions. Experience with HIPAA compliance and healthcare systems is a plus. Strong programming skills in Java or C# required.', 'Both', 'Mid-Senior Level', '2-4 Years', '2025-12-27', 'Active', '2025-02-11 09:00:00'),
(212, 109, 4009, 'Remote', 'Instructional Designer', 6, 60000, 'Full Time', 'Bachelor Degree', 'Looking for an Instructional Designer to create engaging online courses. Experience with e-learning platforms and content creation tools required. Background in education preferred.', 'Both', 'Entry Level', '1-3 Years', '2026-12-22', 'Active', '2025-02-12 11:00:00'),
(213, 110, 4010, 'On-site', 'Renewable Energy Engineer', 13, 82000, 'Full Time', 'Bachelor Degree', 'Civil/Electrical Engineer needed for renewable energy projects. Experience with solar or wind energy systems preferred. You will work on sustainable energy solutions.', 'Both', 'Mid-Senior Level', '3-5 Years', '2025-12-12', 'Active', '2025-02-13 10:30:00'),
(214, 104, 4004, 'Hybrid', 'Cloud Solutions Architect', 5, 105000, 'Full Time', 'Master Degree', 'Senior Cloud Architect needed to design and implement enterprise cloud solutions. AWS/Azure certifications required. You will lead cloud migration projects.', 'Both', 'Senior Manager', '5+ Years', '2025-12-20', 'Active', '2025-02-14 15:00:00'),
(215, 103, 4003, 'Remote', 'Business Intelligence Analyst', 15, 68000, 'Full Time', 'Bachelor Degree', 'BI Analyst needed to develop dashboards and reports. Experience with SQL, Python, and visualization tools required. You will work with stakeholders to identify business insights.', 'Both', 'Entry Level', '1-3 Years', '2025-11-18', 'Active', '2025-02-15 13:30:00'),
(216, 111, 5012, 'On-site', 'Associate', 10, 0, 'Full Time or Part Time', 'BPharma & Above', 'mkirewapgmr', 'Both', 'Beginner', '2 Years', '2026-04-12', 'Active', '2025-12-02 18:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `tbljobapplication`
--

CREATE TABLE `tbljobapplication` (
  `ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `APPLICATIONSTATUS` varchar(20) NOT NULL,
  `DATEAPPLIED` datetime NOT NULL,
  `SCREENINGSCORE` int(11) DEFAULT 0,
  `SCREENPASSED` tinyint(1) DEFAULT NULL,
  `COVERLETTER` text DEFAULT NULL,
  `RESUME` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbljobapplication`
--

INSERT INTO `tbljobapplication` (`ID`, `APPLICANTID`, `JOBID`, `APPLICATIONSTATUS`, `DATEAPPLIED`, `SCREENINGSCORE`, `SCREENPASSED`, `COVERLETTER`, `RESUME`) VALUES
(301, 101, 201, 'Shortlisted', '2025-02-16 10:00:00', 20, 1, 'Dear Hiring Manager, I am excited to apply for the Senior Full Stack Developer position. With 3 years of experience in React and Node.js, I have developed multiple scalable web applications. I am passionate about clean code and best practices.', 'resume_5001_201_1708081200.pdf'),
(302, 101, 202, 'Pending', '2025-02-16 11:00:00', 0, NULL, 'I am interested in the Junior Backend Developer role as I am looking to grow my backend development skills.', 'resume_5001_202_1708084800.pdf'),
(303, 102, 204, 'Interview', '2025-02-17 09:30:00', 15, 1, 'Dear Team, I am applying for the Data Analyst position. My experience with SQL, Python, and Tableau makes me a strong candidate. I have successfully delivered insights that drove business growth.', 'resume_5002_204_1708164600.pdf'),
(304, 102, 215, 'Pending', '2025-02-17 10:00:00', 10, 1, 'I am very interested in the Business Intelligence Analyst role. My analytical skills and experience with data visualization tools would be valuable to your team.', 'resume_5002_215_1708166400.pdf'),
(305, 103, 207, 'Shortlisted', '2025-02-18 14:30:00', 15, 1, 'Dear MarketMaster Team, I am excited to apply for the Digital Marketing Specialist position. I have 2 years of experience managing social media campaigns and SEO optimization. I increased engagement by 150% in my previous role.', 'resume_5003_207_1708269000.pdf'),
(306, 103, 208, 'Rejected', '2025-02-18 15:00:00', 0, 0, 'I am applying for the Content Marketing Manager position. While I have 2 years of experience, I am eager to take on a leadership role.', 'resume_5003_208_1708270800.pdf'),
(307, 104, 209, 'Interview', '2025-02-19 11:00:00', 20, 1, 'Dear DesignHub, I am thrilled to apply for the Senior UX/UI Designer position. My portfolio showcases 4 years of experience creating user-centered designs. I specialize in Figma and have led multiple successful design projects.', 'resume_5004_209_1708344000.pdf'),
(308, 104, 210, 'Pending', '2025-02-19 11:30:00', 0, NULL, 'I am interested in the Graphic Designer position. As a recent graduate, I have a strong portfolio and am proficient in Adobe Creative Suite.', 'resume_5004_210_1708345800.pdf'),
(309, 105, 206, 'Shortlisted', '2025-02-20 10:00:00', 10, 1, 'Dear FinancePro Team, I am applying for the Financial Analyst position. My background in financial modeling and forecasting, combined with my analytical skills, makes me an ideal candidate. I am pursuing my CFA Level 1.', 'resume_5005_206_1708426800.pdf'),
(310, 106, 205, 'Pending', '2025-02-21 13:00:00', 25, 1, 'Dear CloudNine, I am excited to apply for the DevOps Engineer position. I have 3 years of experience with AWS, Docker, and Kubernetes. I have successfully managed CI/CD pipelines and cloud infrastructure.', 'resume_5006_205_1708524000.pdf'),
(311, 107, 207, 'Pending', '2025-02-22 09:00:00', 15, 1, 'Dear MarketMaster, I am a passionate content writer with 2 years of experience. I would love to contribute to your digital marketing campaigns with engaging content.', 'resume_5007_207_1708596000.pdf'),
(312, 107, 208, 'Pending', '2025-02-22 09:30:00', 0, 0, 'I am applying for the Content Marketing Manager position. While my experience is in content writing, I am eager to grow into a management role.', 'resume_5007_208_1708597800.pdf'),
(313, 108, 213, 'Shortlisted', '2025-02-23 10:30:00', 10, 1, 'Dear GreenEnergy, I am excited to apply for the Renewable Energy Engineer position. As a civil engineer with experience in sustainable design, I am passionate about green energy solutions.', 'resume_5008_213_1708686600.pdf'),
(314, 109, 207, 'Pending', '2025-02-24 14:00:00', 15, 1, 'Dear Hiring Team, I am applying for the Digital Marketing Specialist role. I have strong communication skills and 1 year of experience in social media management.', 'resume_5009_207_1708786800.pdf'),
(315, 110, 202, 'Pending', '2025-02-25 11:00:00', 0, NULL, 'I am interested in the Junior Backend Developer position. As a recent graduate, I am eager to learn and contribute to your development team.', 'resume_5010_202_1708862400.pdf'),
(316, 101, 211, 'Interview', '2025-02-26 10:00:00', 5, 1, 'Dear HealthTech, I am applying for the Software Developer position. While I do not have healthcare experience, I am a fast learner and excited about healthcare technology.', 'resume_5001_211_1708945200.pdf'),
(317, 102, 203, 'Rejected', '2025-02-27 09:00:00', 10, 0, 'Dear InnovateSoft, I am interested in the Machine Learning Engineer position. I have a Bachelor degree and 1 year of experience with Python and machine learning basics.', 'resume_5002_203_1709028000.pdf'),
(318, 103, 206, 'Pending', '2025-02-28 13:30:00', 10, 1, 'I am applying for the Financial Analyst role. My marketing background has given me strong analytical skills that translate well to finance.', 'resume_5003_206_1709131800.pdf'),
(319, 104, 211, 'Pending', '2025-03-01 10:00:00', 5, 1, 'Dear HealthTech, I am interested in the Software Developer position. My design background helps me create user-friendly healthcare applications.', 'resume_5004_211_1709204400.pdf'),
(320, 105, 204, 'Pending', '2025-03-02 11:30:00', 15, 1, 'Dear DataDynamics, I am applying for the Data Analyst position. My finance background and SQL skills make me well-suited for this role.', 'resume_5005_204_1709294400.pdf'),
(321, 106, 201, 'Pending', '2025-03-03 14:00:00', 0, 0, 'I am applying for the Senior Full Stack Developer position. While I have network administration experience, I have been learning web development.', 'resume_5006_201_1709388000.pdf'),
(322, 107, 210, 'Shortlisted', '2025-03-04 10:30:00', 0, NULL, 'Dear DesignHub, I am excited to apply for the Graphic Designer position. My writing background helps me create compelling visual stories.', 'resume_5007_210_1709464200.pdf'),
(323, 108, 214, 'Rejected', '2025-03-05 13:00:00', 0, 0, 'Dear CloudNine, I am interested in the Cloud Solutions Architect position. While I have civil engineering background, I have been studying cloud technologies.', 'resume_5008_214_1709559600.pdf'),
(324, 109, 206, 'Pending', '2025-03-06 09:30:00', 10, 1, 'Dear FinancePro, I am applying for the Financial Analyst position. My customer service background has developed my analytical and communication skills.', 'resume_5009_206_1709632200.pdf'),
(325, 110, 204, 'Pending', '2025-03-07 11:00:00', 15, 1, 'Dear DataDynamics, I am interested in the Data Analyst role. My administrative experience with Excel and data management translates well to this position.', 'resume_5010_204_1709722800.pdf'),
(326, 101, 205, 'Pending', '2025-03-08 10:00:00', 25, 1, 'Dear CloudNine, I am applying for the DevOps Engineer position. My full-stack experience has given me exposure to cloud technologies and CI/CD.', 'resume_5001_205_1709805600.pdf'),
(327, 102, 209, 'Pending', '2025-03-09 14:30:00', 0, 0, 'Dear DesignHub, I am interested in the Senior UX/UI Designer position. While my background is in data, I have a passion for design.', 'resume_5002_209_1709905800.pdf'),
(328, 103, 201, 'Pending', '2025-03-10 10:00:00', 0, 0, 'Dear TechCorp, I am applying for the Senior Full Stack Developer position. My marketing experience has exposed me to web technologies.', 'resume_5003_201_1709978400.pdf'),
(329, 105, 215, 'Interview', '2025-03-11 13:00:00', 10, 1, 'Dear DataDynamics, I am excited to apply for the Business Intelligence Analyst position. My finance background combined with my interest in data makes me a strong candidate.', 'resume_5005_215_1710162000.pdf'),
(330, 106, 211, 'Pending', '2025-03-12 11:30:00', 5, 1, 'Dear HealthTech, I am applying for the Software Developer position. My network administration background has given me strong technical skills.', 'resume_5006_211_1710244200.pdf'),
(332, 321, 214, 'Pending', '2025-12-04 01:57:30', 20, 1, 'Dear, hellooooo', 'resume_321_214_1764826050.pdf');

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
-- Table structure for table `tblmessages`
--

CREATE TABLE `tblmessages` (
  `ID` int(11) NOT NULL,
  `SENDER_ID` int(11) NOT NULL,
  `RECIPIENT_ID` int(11) NOT NULL,
  `MESSAGE` text NOT NULL,
  `DATEPOSTED` datetime DEFAULT current_timestamp(),
  `IS_READ` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblmessages`
--

INSERT INTO `tblmessages` (`ID`, `SENDER_ID`, `RECIPIENT_ID`, `MESSAGE`, `DATEPOSTED`, `IS_READ`) VALUES
(1, 321, 4004, 'Hello', '2025-12-04 01:59:13', 0),
(2, 321, 4004, 'Chatting to checkup on my application', '2025-12-04 02:00:01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblnewsletter`
--

CREATE TABLE `tblnewsletter` (
  `ID` int(11) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `STATUS` enum('Active','Unsubscribed') DEFAULT 'Active',
  `SUBSCRIBE_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  `UNSUBSCRIBE_DATE` timestamp NULL DEFAULT NULL,
  `IP_ADDRESS` varchar(45) DEFAULT NULL,
  `USER_AGENT` text DEFAULT NULL,
  `VERIFICATION_TOKEN` varchar(100) DEFAULT NULL,
  `IS_VERIFIED` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblnewsletter`
--

INSERT INTO `tblnewsletter` (`ID`, `EMAIL`, `STATUS`, `SUBSCRIBE_DATE`, `UNSUBSCRIBE_DATE`, `IP_ADDRESS`, `USER_AGENT`, `VERIFICATION_TOKEN`, `IS_VERIFIED`) VALUES
(5, 'emehchiemerie9@gmail.com', 'Active', '2025-11-13 03:19:41', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'cdd302984229b7e88cda97dffedc4141235382bde1a20bf3f83e5845b8fe989e', 0);

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

--
-- Dumping data for table `tblnotification`
--

INSERT INTO `tblnotification` (`ID`, `USERID`, `TYPE`, `TYPEID`, `STATUS`, `DATETIME`, `NOTE`) VALUES
(1, 4004, 'Message', 1, 'Unread', '2025-12-04 01:59:13', 'New message from Dominic Carl (message id: 1)'),
(2, 4004, 'Message', 2, 'Unread', '2025-12-04 02:00:01', 'New message from Dominic Carl (message id: 2)');

-- --------------------------------------------------------

--
-- Table structure for table `tblpasswordreset`
--

CREATE TABLE `tblpasswordreset` (
  `ID` int(11) NOT NULL,
  `USERID` int(11) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `TOKEN` varchar(255) NOT NULL,
  `EXPIRY` datetime NOT NULL,
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpasswordreset`
--

INSERT INTO `tblpasswordreset` (`ID`, `USERID`, `EMAIL`, `TOKEN`, `EXPIRY`, `CREATED_AT`) VALUES
(1, 321, 'emehchiemerie9@gmail.com', 'f661511c79843fe7cae0e94681b6c636671282c382dae5f652714cee9bacdf2d', '2025-12-01 08:23:13', '2025-12-01 05:38:50');

-- --------------------------------------------------------

--
-- Table structure for table `tblscreeninganswers`
--

CREATE TABLE `tblscreeninganswers` (
  `ANSWERID` int(11) NOT NULL,
  `APPLICATIONID` int(11) NOT NULL,
  `QUESTIONID` int(11) NOT NULL,
  `USERID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `ANSWER` text NOT NULL,
  `SCORE` int(11) DEFAULT 0,
  `ANSWEREDAT` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblscreeninganswers`
--

INSERT INTO `tblscreeninganswers` (`ANSWERID`, `APPLICATIONID`, `QUESTIONID`, `USERID`, `JOBID`, `ANSWER`, `SCORE`, `ANSWEREDAT`) VALUES
(401, 301, 201, 5001, 201, 'Yes', 10, '2025-02-16 10:00:00'),
(402, 301, 202, 5001, 201, 'Yes', 10, '2025-02-16 10:00:00'),
(403, 303, 206, 5002, 204, 'Yes', 10, '2025-02-17 09:30:00'),
(404, 303, 207, 5002, 204, 'Tableau', 5, '2025-02-17 09:30:00'),
(405, 304, 220, 5002, 215, 'Yes', 10, '2025-02-17 10:00:00'),
(406, 305, 211, 5003, 207, '1-2 years', 5, '2025-02-18 14:30:00'),
(407, 305, 212, 5003, 207, 'Yes', 10, '2025-02-18 14:30:00'),
(408, 307, 214, 5004, 209, 'Yes', 10, '2025-02-19 11:00:00'),
(409, 307, 215, 5004, 209, 'Yes', 10, '2025-02-19 11:00:00'),
(410, 309, 210, 5005, 206, 'No', 0, '2025-02-20 10:00:00'),
(411, 310, 208, 5006, 205, 'Yes', 15, '2025-02-21 13:00:00'),
(412, 310, 209, 5006, 205, 'Yes', 10, '2025-02-21 13:00:00'),
(413, 311, 211, 5007, 207, '1-2 years', 5, '2025-02-22 09:00:00'),
(414, 311, 212, 5007, 207, 'Yes', 10, '2025-02-22 09:00:00'),
(415, 313, 217, 5008, 213, 'Yes', 10, '2025-02-23 10:30:00'),
(416, 314, 211, 5009, 207, '0-1 years', 5, '2025-02-24 14:00:00'),
(417, 314, 212, 5009, 207, 'Yes', 10, '2025-02-24 14:00:00'),
(418, 316, 216, 5001, 211, 'No', 0, '2025-02-26 10:00:00'),
(419, 317, 204, 5002, 203, 'No', 0, '2025-02-27 09:00:00'),
(420, 317, 205, 5002, 203, 'Bachelor Degree', 0, '2025-02-27 09:00:00'),
(421, 318, 210, 5003, 206, 'No', 0, '2025-02-28 13:30:00'),
(422, 319, 216, 5004, 211, 'No', 0, '2025-03-01 10:00:00'),
(423, 320, 206, 5005, 204, 'Yes', 10, '2025-03-02 11:30:00'),
(424, 320, 207, 5005, 204, 'Power BI', 5, '2025-03-02 11:30:00'),
(425, 324, 210, 5009, 206, 'No', 0, '2025-03-06 09:30:00'),
(426, 325, 206, 5010, 204, 'Yes', 10, '2025-03-07 11:00:00'),
(427, 325, 207, 5010, 204, 'Power BI', 5, '2025-03-07 11:00:00'),
(428, 326, 208, 5001, 205, 'Yes', 15, '2025-03-08 10:00:00'),
(429, 326, 209, 5001, 205, 'Yes', 10, '2025-03-08 10:00:00'),
(430, 329, 220, 5005, 215, 'Yes', 10, '2025-03-11 13:00:00'),
(431, 331, 218, 321, 214, 'Yes', 20, '2025-12-04 05:19:07'),
(432, 331, 219, 321, 214, '1-2 years', 0, '2025-12-04 05:19:07'),
(433, 332, 218, 321, 214, 'Yes', 20, '2025-12-04 05:27:30'),
(434, 332, 219, 321, 214, '3-4 years', 0, '2025-12-04 05:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `tblscreeningquestions`
--

CREATE TABLE `tblscreeningquestions` (
  `QUESTIONID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `QUESTION` text NOT NULL,
  `QUESTIONTYPE` enum('yes_no','multiple_choice','text','number','date') DEFAULT 'text',
  `OPTIONS` text DEFAULT NULL,
  `ISREQUIRED` tinyint(1) DEFAULT 1,
  `CORRECTANSWER` varchar(255) DEFAULT NULL,
  `POINTS` int(11) DEFAULT 0,
  `CREATEDAT` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblscreeningquestions`
--

INSERT INTO `tblscreeningquestions` (`QUESTIONID`, `JOBID`, `QUESTION`, `QUESTIONTYPE`, `OPTIONS`, `ISREQUIRED`, `CORRECTANSWER`, `POINTS`, `CREATEDAT`) VALUES
(201, 201, 'Do you have at least 3 years of experience with React?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 10, '2025-02-01 09:00:00'),
(202, 201, 'Are you proficient in Node.js and Express?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 10, '2025-02-01 09:00:00'),
(203, 202, 'What is your preferred backend framework?', 'multiple_choice', '[\"Express.js\",\"Django\",\"Spring Boot\",\"Laravel\"]', 1, 'Express.js', 5, '2025-02-03 10:30:00'),
(204, 203, 'Do you have experience with TensorFlow or PyTorch?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 15, '2025-02-02 11:00:00'),
(205, 203, 'What is your highest level of education?', 'multiple_choice', '[\"Bachelor Degree\",\"Master Degree\",\"PhD\"]', 1, 'Master Degree', 10, '2025-02-02 11:00:00'),
(206, 204, 'Are you proficient in SQL?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 10, '2025-02-04 14:00:00'),
(207, 204, 'Which data visualization tool do you prefer?', 'multiple_choice', '[\"Tableau\",\"Power BI\",\"Looker\",\"QlikView\"]', 1, '', 5, '2025-02-04 14:00:00'),
(208, 205, 'Do you have AWS certification?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 15, '2025-02-05 09:30:00'),
(209, 205, 'Are you experienced with Kubernetes?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 10, '2025-02-05 09:30:00'),
(210, 206, 'Do you have a CFA certification?', 'yes_no', '[\"Yes\",\"No\"]', 0, '', 5, '2025-02-06 10:00:00'),
(211, 207, 'How many years of digital marketing experience do you have?', 'multiple_choice', '[\"0-1 years\",\"1-2 years\",\"2-3 years\",\"3+ years\"]', 1, '', 5, '2025-02-07 11:30:00'),
(212, 207, 'Are you experienced with Google Analytics?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 10, '2025-02-07 11:30:00'),
(213, 208, 'Do you have experience managing a content team?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 15, '2025-02-08 13:00:00'),
(214, 209, 'Are you proficient in Figma?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 10, '2025-02-09 10:00:00'),
(215, 209, 'Do you have a design portfolio you can share?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 10, '2025-02-09 10:00:00'),
(216, 211, 'Do you have experience with healthcare software?', 'yes_no', '[\"Yes\",\"No\"]', 0, '', 5, '2025-02-11 09:00:00'),
(217, 213, 'Are you familiar with solar energy systems?', 'yes_no', '[\"Yes\",\"No\"]', 1, '', 10, '2025-02-13 10:30:00'),
(218, 214, 'Do you hold AWS Solutions Architect certification?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 20, '2025-02-14 15:00:00'),
(219, 214, 'How many years of cloud architecture experience do you have?', 'multiple_choice', '[\"1-2 years\",\"3-4 years\",\"5-7 years\",\"8+ years\"]', 1, '5-7 years', 10, '2025-02-14 15:00:00'),
(220, 215, 'Are you proficient in Python for data analysis?', 'yes_no', '[\"Yes\",\"No\"]', 1, 'Yes', 10, '2025-02-15 13:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `USERID` int(30) NOT NULL,
  `FNAME` varchar(40) NOT NULL,
  `ONAME` varchar(200) NOT NULL,
  `EMAIL` varchar(50) NOT NULL,
  `PHONE` text DEFAULT NULL,
  `USERNAME` varchar(90) NOT NULL,
  `PASS` varchar(90) NOT NULL,
  `ROLE` varchar(30) NOT NULL,
  `USERSTATUS` varchar(20) DEFAULT NULL,
  `DATEUPDATED` datetime DEFAULT NULL,
  `DATECREATED` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`USERID`, `FNAME`, `ONAME`, `EMAIL`, `PHONE`, `USERNAME`, `PASS`, `ROLE`, `USERSTATUS`, `DATEUPDATED`, `DATECREATED`) VALUES
(228, 'Admin', 'Manager', 'admin@gmail.com', '0779787777', 'admin', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Admin', 'Active', '2025-11-29 18:18:15', NULL),
(321, 'Dominic', 'Carl', 'emehchiemerie9@gmail.com', '', 'emeldo', '$2y$10$6u.ZUBDOBuQZ4K2GzFjD3.Z8e.StDKKCpeuoodzOFeyXX74A.NJ92', 'Applicant', 'Active', '2025-11-17 01:02:57', NULL),
(2749, 'James', 'Cletus', 'emelcarl2023@gmail.com', '', 'emelcarl2023', '$2y$10$4r2eYwzVBwTSnGM4B8MJ5u/.Wrjxqk2oYpCn30cmWIMM/r5cRpUnC', 'Employer', 'Active', NULL, NULL),
(4001, 'Sarah', 'Mitchell', 'sarah.mitchell@techcorp.ca', '7095551001', 'smitchell', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Active', NULL, '2025-01-15 10:00:00'),
(4002, 'James', 'Patterson', 'james.p@innovatesoft.ca', '7095551002', 'jpatterson', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Active', NULL, '2025-01-18 11:30:00'),
(4003, 'Emily', 'Rodriguez', 'emily.r@datadynamics.ca', '7095551003', 'erodriguez', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Active', NULL, '2025-01-20 14:15:00'),
(4004, 'Michael', 'Chen', 'michael.chen@cloudnine.ca', '7095551004', 'mchen', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Active', NULL, '2025-01-22 09:45:00'),
(4005, 'Lisa', 'Thompson', 'lisa.t@financepro.ca', '7095551005', 'lthompson', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Active', NULL, '2025-01-25 16:20:00'),
(4006, 'David', 'Wilson', 'david.w@marketmaster.ca', '7095551006', 'dwilson', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Active', NULL, '2025-01-28 10:30:00'),
(4007, 'Rachel', 'Lee', 'rachel.lee@designhub.ca', '7095551007', 'rlee', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Active', NULL, '2025-02-01 13:00:00'),
(4008, 'Thomas', 'Brown', 'thomas.b@healthtech.ca', '7095551008', 'tbrown', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Active', NULL, '2025-02-03 15:45:00'),
(4009, 'Jennifer', 'Davis', 'jennifer.d@edulearn.ca', '7095551009', 'jdavis', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Pending', NULL, '2025-02-05 11:20:00'),
(4010, 'Robert', 'Garcia', 'robert.g@greenenergy.ca', '7095551010', 'rgarcia', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Employer', 'Active', NULL, '2025-02-08 09:00:00'),
(5001, 'Emma', 'Johnson', 'emma.johnson@email.com', '7095552001', 'ejohnson', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-10 08:00:00'),
(5002, 'Noah', 'Williams', 'noah.williams@email.com', '7095552002', 'nwilliams', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-12 09:30:00'),
(5003, 'Olivia', 'Martinez', 'olivia.martinez@email.com', '7095552003', 'omartinez', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-14 10:15:00'),
(5004, 'Liam', 'Anderson', 'liam.anderson@email.com', '7095552004', 'landerson', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-16 11:45:00'),
(5005, 'Ava', 'Taylor', 'ava.taylor@email.com', '7095552005', 'ataylor', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-18 14:20:00'),
(5006, 'Ethan', 'Thomas', 'ethan.thomas@email.com', '7095552006', 'ethomas', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-20 16:00:00'),
(5007, 'Sophia', 'Jackson', 'sophia.jackson@email.com', '7095552007', 'sjackson', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-22 12:30:00'),
(5008, 'Mason', 'White', 'mason.white@email.com', '7095552008', 'mwhite', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-24 15:45:00'),
(5009, 'Isabella', 'Harris', 'isabella.harris@email.com', '7095552009', 'iharris', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-26 13:00:00'),
(5010, 'Lucas', 'Martin', 'lucas.martin@email.com', '7095552010', 'lmartin', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Applicant', 'Active', NULL, '2025-01-28 10:30:00'),
(5011, 'Joshua', 'Oseimobor', 'joshuaoseimobor@gmail.com', NULL, 'joseimobor24', '$2y$10$f5XHuzuMPim8oQEOITtbKe8jFgNN9LRsjtHZ0QlInGwTd2uLjjO42', 'Applicant', 'Active', NULL, '2025-12-02 18:24:27'),
(5012, 'Joshua', 'Oseimobor', 'joshuasemo.abu@gmail.com', NULL, 'joseimobor247', '$2y$10$v6WIRpWESqSif0XwsXNueuHxN6pfMzri/8uO2OvP0iYO2rIRxygfW', 'Employer', 'Active', NULL, '2025-12-02 18:33:29'),
(5013, 'Akefar', 'Islam', 'akefari@mun.ca', NULL, 'Akefar', '$2y$10$cvU5ZMAE9E6nHRO2UuifzeopxeXQfqGKztV8uvTauNb94AowzDtG.', 'Applicant', 'Active', NULL, '2025-12-02 19:33:38'),
(5014, 'DimEmployer', 'Checking', 'dim@gmail.com', NULL, 'dim', '$2y$10$5k4MlKujKC0aMZAlXePD1Ov8Scs2JESZkvnPjMXq.Fdv81aWdfIKS', 'Employer', 'Active', NULL, '2025-12-04 02:22:21');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_activity_log`
--

CREATE TABLE `tbl_activity_log` (
  `ACTIVITY_ID` int(11) NOT NULL,
  `USERID` int(11) NOT NULL,
  `ACTION` varchar(100) NOT NULL,
  `DESCRIPTION` text DEFAULT NULL,
  `IP_ADDRESS` varchar(45) DEFAULT NULL,
  `USER_AGENT` text DEFAULT NULL,
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_activity_log`
--

INSERT INTO `tbl_activity_log` (`ACTIVITY_ID`, `USERID`, `ACTION`, `DESCRIPTION`, `IP_ADDRESS`, `USER_AGENT`, `CREATED_AT`) VALUES
(4, 228, 'SETTINGS_UPDATED', 'Admin updated email configuration', '2605:b100:b14:a8cf:c460:60b3:9f03:d0c2', NULL, '2025-12-02 17:28:05'),
(5, 228, 'SETTINGS_UPDATED', 'Admin updated system configuration', '2605:b100:b14:a8cf:c460:60b3:9f03:d0c2', NULL, '2025-12-02 17:28:26'),
(6, 228, 'LOGOUT', 'User logged out', '2605:b100:b14:a8cf:c460:60b3:9f03:d0c2', NULL, '2025-12-02 17:29:15'),
(7, 228, 'LOGOUT', 'User logged out', '2605:b100:b14:a8cf:ad97:2bb2:7f8d:e66d', NULL, '2025-12-02 17:54:05'),
(8, 5011, 'LOGOUT', 'User logged out', '209.205.254.14', NULL, '2025-12-02 18:31:09'),
(9, 5011, 'LOGOUT', 'User logged out', '209.205.254.14', NULL, '2025-12-02 18:31:16'),
(10, 5012, 'LOGOUT', 'User logged out', '209.205.254.14', NULL, '2025-12-02 18:41:11'),
(11, 228, 'JOB_APPROVED', 'Admin approved a pending job posting', '209.205.254.14', NULL, '2025-12-02 18:45:06'),
(12, 321, 'LOGOUT', 'User logged out', '::1', NULL, '2025-12-04 04:55:34'),
(13, 321, 'LOGOUT', 'User logged out', '::1', NULL, '2025-12-04 04:56:33'),
(14, 321, 'LOGOUT', 'User logged out', '::1', NULL, '2025-12-04 04:57:37'),
(15, 321, 'LOGOUT', 'User logged out', '::1', NULL, '2025-12-04 05:41:48'),
(16, 321, 'LOGOUT', 'User logged out', '::1', NULL, '2025-12-04 05:44:30'),
(17, 2749, 'LOGOUT', 'User logged out', '::1', NULL, '2025-12-04 05:51:17'),
(18, 5014, 'LOGOUT', 'User logged out', '::1', NULL, '2025-12-04 07:45:10'),
(19, 228, 'LOGOUT', 'User logged out', '::1', NULL, '2025-12-04 07:57:47');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_email_templates`
--

CREATE TABLE `tbl_email_templates` (
  `id` int(11) NOT NULL,
  `template_key` varchar(100) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `variables` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_email_templates`
--

INSERT INTO `tbl_email_templates` (`id`, `template_key`, `template_name`, `subject`, `body`, `variables`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'welcome_email', 'Welcome Email', 'Welcome to MUNext - {{site_name}}', '<h2>Welcome {{user_name}}!</h2><p>Thank you for joining {{site_name}}. We\'re excited to have you on board.</p><p>You can now browse and apply for jobs that match your skills and interests.</p><p>Best regards,<br>The {{site_name}} Team</p>', '{{user_name}}, {{site_name}}, {{user_email}}', 1, '2025-11-23 17:28:05', '2025-11-23 17:28:05'),
(2, 'job_application_received', 'Job Application Received', 'Application Received - {{job_title}}', '<h2>Application Received</h2><p>Dear {{applicant_name}},</p><p>Thank you for applying to the position of <strong>{{job_title}}</strong> at {{company_name}}.</p><p>Your application has been received and is currently under review. We will contact you if your profile matches our requirements.</p><p>Application ID: {{application_id}}</p><p>Best regards,<br>{{company_name}}</p>', '{{applicant_name}}, {{job_title}}, {{company_name}}, {{application_id}}', 1, '2025-11-23 17:28:05', '2025-11-23 17:28:05'),
(3, 'application_status_update', 'Application Status Update', 'Application Status Update - {{job_title}}', '<h2>Application Status Update</h2><p>Dear {{applicant_name}},</p><p>Your application for <strong>{{job_title}}</strong> at {{company_name}} has been updated.</p><p><strong>New Status:</strong> {{application_status}}</p><p>Application ID: {{application_id}}</p><p>Best regards,<br>{{company_name}}</p>', '{{applicant_name}}, {{job_title}}, {{company_name}}, {{application_status}}, {{application_id}}', 1, '2025-11-23 17:28:05', '2025-11-29 17:00:09'),
(4, 'new_application_alert', 'New Application Alert (Employer)', 'New Application - {{job_title}}', '<h2>New Application Received</h2><p>Dear {{company_name}},</p><p>A new application has been submitted for your job posting: <strong>{{job_title}}</strong></p><p><strong>Applicant:</strong> {{applicant_name}}<br><strong>Email:</strong> {{applicant_email}}<br><strong>Applied On:</strong> {{application_date}}</p><p>Login to your dashboard to review the application.</p><p>Best regards,<br>{{site_name}}</p>', '{{company_name}}, {{job_title}}, {{applicant_name}}, {{applicant_email}}, {{application_date}}, {{site_name}}', 1, '2025-11-23 17:28:05', '2025-11-23 17:28:05'),
(5, 'job_approved', 'Job Posting Approved', 'Your Job Posting Has Been Approved', '<h2>Job Posting Approved</h2><p>Dear {{company_name}},</p><p>Great news! Your job posting for <strong>{{job_title}}</strong> has been approved and is now live on our platform.</p><p>You can view your posting and manage applications from your dashboard.</p><p>Best regards,<br>{{site_name}}</p>', '{{company_name}}, {{job_title}}, {{site_name}}', 1, '2025-11-23 17:28:05', '2025-11-23 17:28:05'),
(6, 'company_approved', 'Company Registration Approved', 'Welcome to {{site_name}} - Company Approved', '<h2>Company Registration Approved</h2><p>Dear {{company_name}},</p><p>Congratulations! Your company registration has been approved.</p><p>You can now login to your dashboard and start posting jobs to find the best candidates.</p><p>Best regards,<br>{{site_name}}</p>', '{{company_name}}, {{site_name}}', 1, '2025-11-23 17:28:05', '2025-11-23 17:28:05'),
(7, 'password_reset', 'Password Reset Request', 'Password Reset Request - {{site_name}}', '<h2>Password Reset Request</h2><p>Dear {{user_name}},</p><p>We received a request to reset your password for your {{site_name}} account.</p><p>Click the link below to reset your password:<br><a href=\"{{reset_link}}\">{{reset_link}}</a></p><p>This link will expire in 24 hours.</p><p>If you didn\'t request this, please ignore this email.</p><p>Best regards,<br>{{site_name}}</p>', '{{user_name}}, {{site_name}}, {{reset_link}}', 1, '2025-11-23 17:28:05', '2025-11-23 17:28:05');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_login_history`
--

CREATE TABLE `tbl_login_history` (
  `LOGIN_ID` int(11) NOT NULL,
  `USERID` int(11) DEFAULT NULL,
  `USERNAME` varchar(100) DEFAULT NULL,
  `EMAIL` varchar(150) DEFAULT NULL,
  `USERTYPE` enum('Admin','Employer','Applicant') NOT NULL,
  `LOGIN_TIME` datetime NOT NULL DEFAULT current_timestamp(),
  `LOGOUT_TIME` datetime DEFAULT NULL,
  `IP_ADDRESS` varchar(45) DEFAULT NULL,
  `USER_AGENT` text DEFAULT NULL,
  `BROWSER` varchar(100) DEFAULT NULL,
  `DEVICE` varchar(100) DEFAULT NULL,
  `OPERATING_SYSTEM` varchar(100) DEFAULT NULL,
  `LOGIN_STATUS` enum('Success','Failed','Blocked') NOT NULL DEFAULT 'Success',
  `FAILURE_REASON` varchar(255) DEFAULT NULL,
  `SESSION_ID` varchar(255) DEFAULT NULL,
  `LOCATION_COUNTRY` varchar(100) DEFAULT NULL,
  `LOCATION_CITY` varchar(100) DEFAULT NULL,
  `IS_ACTIVE` tinyint(1) DEFAULT 1,
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_login_history`
--

INSERT INTO `tbl_login_history` (`LOGIN_ID`, `USERID`, `USERNAME`, `EMAIL`, `USERTYPE`, `LOGIN_TIME`, `LOGOUT_TIME`, `IP_ADDRESS`, `USER_AGENT`, `BROWSER`, `DEVICE`, `OPERATING_SYSTEM`, `LOGIN_STATUS`, `FAILURE_REASON`, `SESSION_ID`, `LOCATION_COUNTRY`, `LOCATION_CITY`, `IS_ACTIVE`, `CREATED_AT`) VALUES
(1, 228, 'admin', 'admin@gmail.com', 'Admin', '2025-12-02 17:25:52', '2025-12-02 17:29:15', '2605:b100:b14:a8cf:c460:60b3:9f03:d0c2', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Safari', 'Mobile', 'Linux', 'Success', NULL, '512ilid71mn1kklk2ib7qcle2i', NULL, NULL, 0, '2025-12-02 17:25:52'),
(2, 5011, 'joseimobor24', 'joshuaoseimobor@gmail.com', 'Applicant', '2025-12-02 18:24:38', '2025-12-02 18:31:09', '209.205.254.14', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'tshu96c9rsqh7hed61ujduose9', NULL, NULL, 0, '2025-12-02 18:24:38'),
(3, 5011, 'joseimobor24', 'joshuaoseimobor@gmail.com', 'Applicant', '2025-12-02 18:31:13', '2025-12-02 18:31:16', '209.205.254.14', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, '650s1htlh8iiibhuc5s18pcobs', NULL, NULL, 0, '2025-12-02 18:31:13'),
(4, 5012, 'joseimobor247', 'joshuasemo.abu@gmail.com', 'Employer', '2025-12-02 18:33:39', '2025-12-02 18:41:11', '209.205.254.14', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'qq2ug5dsro47pc89neo34ijacm', NULL, NULL, 0, '2025-12-02 18:33:39'),
(5, 228, 'admin', 'admin@gmail.com', 'Admin', '2025-12-02 18:41:51', NULL, '209.205.254.14', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Failed', 'Incorrect password', 'qq2ug5dsro47pc89neo34ijacm', NULL, NULL, 1, '2025-12-02 18:41:51'),
(6, 228, 'admin', 'admin@gmail.com', 'Admin', '2025-12-02 18:42:03', NULL, '209.205.254.14', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'ppqvloun70shmfpcne9sdkbj7h', NULL, NULL, 1, '2025-12-02 18:42:03'),
(7, 5013, 'Akefar', 'akefari@mun.ca', 'Applicant', '2025-12-02 19:51:49', NULL, '193.149.173.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'hts2o7ghfjehgvv361t14guj3l', NULL, NULL, 1, '2025-12-02 19:51:49'),
(8, 321, 'emeldo', 'emehchiemerie9@gmail.com', 'Applicant', '2025-12-03 18:36:12', '2025-12-04 01:25:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'nig8kgtque73jpq7emtiq24fai', NULL, NULL, 0, '2025-12-03 22:06:12'),
(16, 321, 'emeldo', 'emehchiemerie9@gmail.com', 'Applicant', '2025-12-04 01:25:41', '2025-12-04 01:26:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'afonnbdd2c02n033pbnpdv1kmf', NULL, NULL, 0, '2025-12-04 04:55:41'),
(17, 321, 'emeldo', 'emehchiemerie9@gmail.com', 'Applicant', '2025-12-04 01:26:45', '2025-12-04 01:27:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'f76nsil01q4dcdd9ba5utl06tp', NULL, NULL, 0, '2025-12-04 04:56:45'),
(18, 321, 'emeldo', 'emehchiemerie9@gmail.com', 'Applicant', '2025-12-04 01:27:42', '2025-12-04 02:11:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, '5ev5viaglfjevuveoimm697afh', NULL, NULL, 0, '2025-12-04 04:57:42'),
(19, 321, 'emeldo', 'emehchiemerie9@gmail.com', 'Applicant', '2025-12-04 02:11:58', '2025-12-04 02:14:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'r2vm5gk133vpit9m187fd3pngo', NULL, NULL, 0, '2025-12-04 05:41:58'),
(20, 2749, 'emelcarl2023', 'emelcarl2023@gmail.com', 'Employer', '2025-12-04 02:15:49', '2025-12-04 02:21:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'tvkad50fmu59i17hioe8br4ulo', NULL, NULL, 0, '2025-12-04 05:45:49'),
(21, 5014, 'dim', 'dim@gmail.com', 'Employer', '2025-12-04 02:22:42', '2025-12-04 04:15:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, 'ts94pvp63j3h9bbld4jp2s3hvp', NULL, NULL, 0, '2025-12-04 05:52:42'),
(22, 228, 'admin', 'admin@gmail.com', 'Admin', '2025-12-04 04:21:24', '2025-12-04 04:27:47', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Safari', 'Desktop', 'Windows 10', 'Success', NULL, '6gsoj8t1hpdoem2sh75m29ratj', NULL, NULL, 0, '2025-12-04 07:51:24');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `setting_key`, `setting_value`, `setting_description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'MUNext', 'Name of the job portal', '2025-11-23 17:01:05', '2025-11-29 17:46:09'),
(2, 'site_tagline', 'Memorial University Job Portal', 'Site tagline or slogan', '2025-11-23 17:01:05', '2025-11-23 17:01:05'),
(3, 'site_email', 'info@munext.com', 'Primary contact email', '2025-11-23 17:01:05', '2025-11-23 17:01:05'),
(4, 'site_phone', '1234567890', 'Contact phone number', '2025-11-23 17:01:05', '2025-11-23 17:19:14'),
(5, 'site_address', 'ST JOHN, NL. CANADA', 'Physical address of organization', '2025-11-23 17:01:05', '2025-11-29 17:47:02'),
(6, 'smtp_host', 'smtp.munext.ca', 'SMTP server hostname', '2025-11-23 17:01:05', '2025-12-01 00:51:14'),
(7, 'smtp_port', '587', 'SMTP server port', '2025-11-23 17:01:05', '2025-11-23 17:01:05'),
(8, 'smtp_username', 'support@munext.ca', 'SMTP account username', '2025-11-23 17:01:05', '2025-11-29 17:47:53'),
(9, 'smtp_password', '@munext.ca', 'SMTP account password', '2025-11-23 17:01:05', '2025-11-29 17:47:53'),
(10, 'smtp_from_email', 'noreply@munext.ca', 'Email address for outgoing emails', '2025-11-23 17:01:05', '2025-12-02 17:28:05'),
(11, 'smtp_from_name', 'MUNext', 'Display name for outgoing emails', '2025-11-23 17:01:05', '2025-11-23 17:01:05'),
(12, 'items_per_page', '10', 'Number of items per page in listings', '2025-11-23 17:01:05', '2025-11-29 17:54:13'),
(13, 'maintenance_mode', '0', 'Enable/disable maintenance mode', '2025-11-23 17:01:05', '2025-11-30 03:32:48'),
(14, 'user_registration', '1', 'Allow user registration', '2025-11-23 17:01:05', '2025-11-23 17:01:05'),
(15, 'company_registration', '1', 'Allow company registration', '2025-11-23 17:01:05', '2025-11-23 17:01:05'),
(16, 'auto_approve_jobs', '1', 'Auto-approve job postings', '2025-11-23 17:01:05', '2025-12-02 17:28:26'),
(17, 'auto_approve_companies', '1', 'Auto-approve company registrations', '2025-11-23 17:01:05', '2025-12-02 17:28:26'),
(24, 'notify_new_application', '1', NULL, '2025-11-29 16:30:28', '2025-11-29 16:58:56'),
(25, 'notify_application_status', '1', NULL, '2025-11-29 16:30:28', '2025-11-29 16:30:28'),
(26, 'notify_job_approved', '1', NULL, '2025-11-29 16:30:28', '2025-11-29 16:30:28'),
(27, 'notify_company_approved', '1', NULL, '2025-11-29 16:30:28', '2025-11-29 16:30:28'),
(28, 'notify_new_user', '1', NULL, '2025-11-29 16:30:28', '2025-11-29 16:30:28'),
(141, 'maintenance_end_time', '', 'Estimated time when site will be back online', '2025-11-30 02:47:23', '2025-11-30 02:47:23'),
(222, 'use_smtp', '0', 'Use SMTP (1) or PHP mail (0)', '2025-12-01 05:29:05', '2025-12-01 05:29:05'),
(227, 'smtp_encryption', 'tls', 'Encryption type (tls/ssl)', '2025-12-01 05:31:11', '2025-12-01 05:31:11');

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
-- Indexes for table `tbljobsubcategory`
--
ALTER TABLE `tbljobsubcategory`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblmessages`
--
ALTER TABLE `tblmessages`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblnewsletter`
--
ALTER TABLE `tblnewsletter`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblnotification`
--
ALTER TABLE `tblnotification`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblpasswordreset`
--
ALTER TABLE `tblpasswordreset`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblscreeninganswers`
--
ALTER TABLE `tblscreeninganswers`
  ADD PRIMARY KEY (`ANSWERID`);

--
-- Indexes for table `tblscreeningquestions`
--
ALTER TABLE `tblscreeningquestions`
  ADD PRIMARY KEY (`QUESTIONID`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`USERID`);

--
-- Indexes for table `tbl_activity_log`
--
ALTER TABLE `tbl_activity_log`
  ADD PRIMARY KEY (`ACTIVITY_ID`);

--
-- Indexes for table `tbl_email_templates`
--
ALTER TABLE `tbl_email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_login_history`
--
ALTER TABLE `tbl_login_history`
  ADD PRIMARY KEY (`LOGIN_ID`);

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblapplicants`
--
ALTER TABLE `tblapplicants`
  MODIFY `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `tblbookmarkjob`
--
ALTER TABLE `tblbookmarkjob`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=420;

--
-- AUTO_INCREMENT for table `tblbookmarkresume`
--
ALTER TABLE `tblbookmarkresume`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=516;

--
-- AUTO_INCREMENT for table `tblcompany`
--
ALTER TABLE `tblcompany`
  MODIFY `COMPANYID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `tbljob`
--
ALTER TABLE `tbljob`
  MODIFY `JOBID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT for table `tbljobapplication`
--
ALTER TABLE `tbljobapplication`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=333;

--
-- AUTO_INCREMENT for table `tbljobcategory`
--
ALTER TABLE `tbljobcategory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tbljobsubcategory`
--
ALTER TABLE `tbljobsubcategory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tblmessages`
--
ALTER TABLE `tblmessages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblnewsletter`
--
ALTER TABLE `tblnewsletter`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblnotification`
--
ALTER TABLE `tblnotification`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblpasswordreset`
--
ALTER TABLE `tblpasswordreset`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblscreeninganswers`
--
ALTER TABLE `tblscreeninganswers`
  MODIFY `ANSWERID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=435;

--
-- AUTO_INCREMENT for table `tblscreeningquestions`
--
ALTER TABLE `tblscreeningquestions`
  MODIFY `QUESTIONID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `USERID` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5015;

--
-- AUTO_INCREMENT for table `tbl_activity_log`
--
ALTER TABLE `tbl_activity_log`
  MODIFY `ACTIVITY_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_email_templates`
--
ALTER TABLE `tbl_email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_login_history`
--
ALTER TABLE `tbl_login_history`
  MODIFY `LOGIN_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;





CREATE TABLE `tbl_email_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) NOT NULL,
  `template_name` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` enum('sent','failed','pending') DEFAULT 'pending',
  `sent_at` datetime DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient` (`recipient`),
  KEY `status` (`status`),
  KEY `sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;