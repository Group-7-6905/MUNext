-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2022 at 03:48 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `erisdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblapplicants`
--

CREATE TABLE IF NOT EXISTS `tblapplicants` (
  `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT,
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
  `LinkedIn_link` varchar(255) NOT NULL,
  PRIMARY KEY (`APPLICANTID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `tblapplicants`
--

INSERT INTO `tblapplicants` (`APPLICANTID`, `JOBCATEGORYID`, `JOBTITLE`, `EXCOMPANYNAME`, `EXJOBTITLE`, `USERID`, `FNAME`, `OTHERNAMES`, `FULLADDRESS`, `CITY`, `COUNTRY`, `SEX`, `BIRTHDATE`, `ABOUTME`, `USERNAME`, `EMAILADDRESS`, `CONTACTNO`, `DEGREE`, `SCHOOLNAME`, `SKILLS`, `APPLICANTPHOTO`, `FB_link`, `LinkedIn_link`) VALUES
(1, 21, 'Web Developer', 'Dimconnect', 'Web Developer', 321, 'Chiemerie', 'Emeldo Cletus', 'Festec ojo rd', 'Lekki', 'Nigeria', 'Male', '1996-08-31', 'I am flexible, reliable and possess excellent time keeping skills.\r\nI am an enthusiastic, self-motivated, reliable, responsible and hard working person. I am a mature team worker and adaptable to all challenging situations. I am able to work well both in a team environment as well as using own initiative. I am able to work well under pressure and adhere to strict deadlines.', 'emeldo', 'emehchiemerie9@gmail.com', '09130216739', 'B.Sc', 'Chukwuemeka Odumegwu Ojukwu University', 'HTML, CSS, PHP, Javascript', 'profile/IMG_20210510_141429.jpg', 'https://web.facebook.com/carldom/', 'https://web.linkedin.com/carldom/'),
(2, 21, 'Web Developer', '', '', 479, 'Chioma', 'Uba', 'uli center', 'ihiala', 'Nigeria', 'Female', '1995-01-10', 'I am an enthusiastic, self-motivated, reliable, responsible and hard working person. I am a mature team worker and adaptable to all challenging situations. I am able to work well both in a team environment as well as using own initiative. I am able to work well under pressure and adhere to strict deadlines.', 'chioma', 'Chioma@gmail.com', '09056456579', 'Master Degree', 'Chukwuemeka Odumegwu Ojukwu University', 'HTML, CSS, PHP, Javascript', 'profile/th.jpg', 'https://web.facebook.com/chioma/?_rdc=1&_rdr', 'https://web.linkedin.com/chioma/'),
(3, 20, 'Senior Software Developer', 'Dimconnect ICT Services', 'Senior Software Developer', 2745, 'Kene', 'Okafor', 'Uli center', 'Ihiala', 'Nigeria', 'Male', '1990-07-04', 'I am outgoing, dedicated, and open-minded. I get across to people and adjust to changes with ease. I believe that a person should work on developing their professional skills and learning new things all the time. Currently, I am looking for new career opportunities my current job position cannot provide.', 'ekene', 'okafor@gmail.com', '08145648790', 'Master Degree', 'Chukwuemeka Odumegwu Ojukwu University', 'Java, SQLite, Networking, XML', 'profile/3.jpeg', 'fb.profile', 'linkedin.profile'),
(4, 20, 'Senior Software Developer', 'Dimconnect ICT Services', 'Senior Software Developer', 2745, 'Kene', 'Okafor', 'Uli center', 'Ihiala', 'Nigeria', 'Male', '1990-07-04', 'I am outgoing, dedicated, and open-minded. I get across to people and adjust to changes with ease. I believe that a person should work on developing their professional skills and learning new things all the time. Currently, I am looking for new career opportunities my current job position cannot provide.', 'ekene', 'okafor@gmail.com', '08145648790', 'Master Degree', 'Chukwuemeka Odumegwu Ojukwu University', 'Java, SQLite, Networking, XML', 'profile/3.jpeg', 'fb.profile', 'linkedin.profile'),
(5, 20, 'Senior Software Developer', 'Dimconnect ICT Services', 'Senior Software Developer', 2745, 'Kene', 'Okafor', 'Uli center', 'Ihiala', 'Nigeria', 'Male', '1990-07-04', 'I am outgoing, dedicated, and open-minded. I get across to people and adjust to changes with ease. I believe that a person should work on developing their professional skills and learning new things all the time. Currently, I am looking for new career opportunities my current job position cannot provide.', 'ekene', 'okafor@gmail.com', '08145648790', 'Master Degree', 'Chukwuemeka Odumegwu Ojukwu University', 'Java, SQLite, Networking, XML', 'profile/3.jpeg', 'fb.profile', 'linkedin.profile');

-- --------------------------------------------------------

--
-- Table structure for table `tblattachmentfile`
--

CREATE TABLE IF NOT EXISTS `tblattachmentfile` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) DEFAULT NULL,
  `JOBID` int(11) NOT NULL,
  `FILE_NAME` varchar(90) NOT NULL,
  `FILE_LOCATION` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tblattachmentfile`
--

INSERT INTO `tblattachmentfile` (`ID`, `APPLICANTID`, `JOBID`, `FILE_NAME`, `FILE_LOCATION`) VALUES
(2, 2147483647, 2, 'Resume', 'photos/27052018124027PLATENO FE95483.docx');

-- --------------------------------------------------------

--
-- Table structure for table `tblautonumbers`
--

CREATE TABLE IF NOT EXISTS `tblautonumbers` (
  `AUTOID` int(11) NOT NULL AUTO_INCREMENT,
  `AUTOSTART` varchar(30) NOT NULL,
  `AUTOEND` int(11) NOT NULL,
  `AUTOINC` int(11) NOT NULL,
  `AUTOKEY` varchar(30) NOT NULL,
  PRIMARY KEY (`AUTOID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tblautonumbers`
--

INSERT INTO `tblautonumbers` (`AUTOID`, `AUTOSTART`, `AUTOEND`, `AUTOINC`, `AUTOKEY`) VALUES
(1, '02983', 7, 1, 'userid'),
(2, '000', 78, 1, 'employeeid'),
(3, '0', 16, 1, 'APPLICANT'),
(4, '69125', 29, 1, 'FILEID');

-- --------------------------------------------------------

--
-- Table structure for table `tblbookmarkjob`
--

CREATE TABLE IF NOT EXISTS `tblbookmarkjob` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `DATETIME` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `tblbookmarkjob`
--

INSERT INTO `tblbookmarkjob` (`ID`, `APPLICANTID`, `JOBID`, `DATETIME`) VALUES
(11, 321, 4, '2022-03-07 16:32:57'),
(13, 321, 5, '2022-03-07 16:38:37'),
(15, 321, 7, '2022-03-07 16:40:08');

-- --------------------------------------------------------

--
-- Table structure for table `tblbookmarkresume`
--

CREATE TABLE IF NOT EXISTS `tblbookmarkresume` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `USERID` int(11) NOT NULL,
  `JOBAPPLICATIONID` int(11) NOT NULL,
  `JOBRESUMEID` int(11) NOT NULL,
  `DATETIME` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tblbookmarkresume`
--

INSERT INTO `tblbookmarkresume` (`ID`, `USERID`, `JOBAPPLICATIONID`, `JOBRESUMEID`, `DATETIME`) VALUES
(3, 228, 15, 4, '2022-05-10 11:04:05');

-- --------------------------------------------------------

--
-- Table structure for table `tblcompany`
--

CREATE TABLE IF NOT EXISTS `tblcompany` (
  `COMPANYID` int(11) NOT NULL AUTO_INCREMENT,
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
  `COMPANYLOGO` blob NOT NULL,
  PRIMARY KEY (`COMPANYID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `tblcompany`
--

INSERT INTO `tblcompany` (`COMPANYID`, `COMPANYNAME`, `COMPANYADDRESS`, `COMPANYCONTACTNO`, `COMPANYSTATUS`, `COMPANYABOUT`, `COMPANYEMAIL`, `COMPANYINDUSTRY`, `COMPANYSPECIALISM`, `COMPANYCOUNTRY`, `COMPANYCITY`, `COMPANYAWARD`, `COMPANYYEAR`, `COMPANYAWARDDESC`, `COMPANYLOGO`) VALUES
(2, 'Chevron Corporation', 'San Ramon', '9258423232', '', 'Chevron Corporation is an American multinational energy corporation. One of the successor companies of Standard Oil, it is headquartered in San Ramon, California, and active in more than 180 countries.', 'support@Chevron.com', 'Manufacturing Industry', 'Construction', 'United States of America', 'California', 'Chevron Wins at EGYP', '2022', 'SAN RAMON, Calif., March 8, 2022 /3BL Media/ - Chevron Corporation (NYSE: CVX) announced it has been named Employer of the Year Championing Inclusion, Diversity and Equality at the Egypt Petroleum Show (EGYPS) 2022 conference in Egypt held February 14 to 16. EGYPS Global Equality in Energy Awards recognizes global oil and gas industry achievements in equality, inclusion and diversity in the industry', 0x636f6d70616e795f6c6f676f2f43686576726f6e2d4c6f676f2e706e67),
(3, 'Shell Plc', 'Mabinays SE1 7NA', '0804776477543', '', ' Shell plc is a British publicly traded multinational oil and gas company headquartered at Shell Centre in London, United Kingdom. Shell is a public limited company with a primary listing on the London Stock Exchange', 'support@shell.com', 'Chemical Plant', 'Industrial Machinery, Gas and Chemicals', 'United States of America', 'Shell Centre London', 'Best Corporations fo', '2020', 'The National Veteran-Owned Business Association (NaVOBA) announced today the best U.S. corporations committed to working with veteran-owned businesses in 2020 using data and responses from the 2020 Best Corporations for Veterans Business Enterprises® (BCVBE) Survey. This prestigious list honors those large corporations that most successfully engage the nations certified Veterans and Service-Disabled Veterans Business Enterprises® (VBEs/SDVBEs) as suppliers.', 0x636f6d70616e795f6c6f676f2f7368656c6c2e6a7067),
(4, 'Nigerian National Petroleum Corporation NNPC', 'Herbert Macaulay Way,  P.M.B. 190', '234946081000', '', '   The Nigerian National Petroleum Corporation (NNPC) is the state oil corporation which was ?established on April 1, 1977. In addition to its exploration activities, the Corporation was given powers and operational interests in refining, petrochemicals and products transportation as well as marketing. Between 1978 and 1989, NNPC constructed refineries in Warri, Kaduna and Port Harcourt and took over the 35,000-barrel Shell Refinery established in Port Harcourt in 1965.', 'support@nnpc.com', 'Tertiary Sector', 'Industrial Machinery, Gas and Chemicals', 'Nigeria', 'Garki, Abuja', 'Oil, Gas Reforms', '2020', 'he Nigerian National Petroleum Company (NNPC) Limited has won an award as the overall best company in oil and gas reforms at the Open Government Partnership (OGP) global awards which was held in Seoul, South Korea.\r\nNigeria picked the award at the expense of other countries in Africa and the Middle East that were implementing the OGP at the summit of member countries, for setting up a Beneficial Ownership (BO) registry to end anonymous companies in the country.', 0x636f6d70616e795f6c6f676f2f6e6e70632e6a7067),
(6, 'Kuda MicroFinance', '151 Herbert Macaulay Way, Yaba', '0625656899', '', ' A few years ago, a small team of people determined to transform banking launched a savings app for Nigerians. That app was the first step toward Kuda.\r\n\r\nToday, we’re even more determined and we’ve built a Central Bank-licensed, microfinance bank to help you get the best out of your money without overcharging you.\r\n\r\nKuda includes tools for tracking your spending habits, saving more and making the right money moves.\r\n\r\nSo no matter who you are or where you live in Nigeria, we’re here because of you. We know the pain that comes with using a regular bank and we will make things work better for everyone.', 'support@Kuda.com', 'Bank Services', 'Banking', 'Nigeria', 'Yaba, Lagos', '2m Customer Mileston', '2022', 'Kuda Microfinance Bank has announced that it has passed the two-million customer milestone, just six months after the bank signed up its one-millionth customer.\r\nKuda has been on a consistent upward trajectory since 2020, growth that the bank credits to aggressive marketing, an improved banking app, and increased adoption of digital banking since the COVID-19 pandemic broke out.', 0x636f6d70616e795f6c6f676f2f6b7564612e6a7067),
(7, 'NIIT', 'NIIT Limited UK Sheffield Technology Parks Cooper Buildings Suite W3 Sheffield, S12NS UK', '18884546448', 'Active', ' The idea of NIIT was born on a rainy, monsoon evening in Mumbai over endless cups of tea, way back in 1981. Two engineering graduates from the Indian Institute of Technology, New Delhi were envisioning the future of Information Technology training and founded a company that is now considered the crucible of Indias IT revolution.\r\n\r\nFrom being Indias most trusted education brand, NIIT has grown to earn the trust of many Fortune and Global 500 companies in over 30 countries over the past 40 years. NIITs instructional designers apply award-winning design methodologies and high-end media and technology across several industries every day, working with market leaders across the world including; oil and gas, pharmaceuticals, life sciences, banking and financial services, insurance, technology, telecom, and manufacturing. NIITs goal is to help leading companies run training like a business, by maximizing the effectiveness and efficiency of training. ', 'support@niit.com', 'IT & Software', 'Technology, Media & Telecommunications', 'United States of America', 'Los Angeles', '2020 Brandon Hall Go', '2020', 'The Monitoring Performance Training (MPT) program is a comprehensive, 17-week performance-linked, blended learning onboarding program for Clinical Research Associates at IQVIA which reduced time to productivity for 46% while allowing complete flexibility for a remote workforce.', 0x636f6d70616e795f6c6f676f2f646f776e6c6f6164202833292e706e67),
(8, 'Dimconnect ICT Services', 'Lekki', '07034984833', 'Active', 'Dimconnect is an eductional IT company that seeks to impact and develop ICT skills and talents of nigerian youths while empowering them for the IT industry. Enhance business process through automation using technology and we also provide business IT solutions and advice. Website', 'support@dimconnect.com', 'IT & Software', 'Accounting', 'Select Country', 'Festac', 'Best Company ', '2021', 'DEFENSE TECHCONNECT INNOVATION AWARDS. TechConnect is proud to announce the TechConnect Defense Innovation Awards recognizing the top 15%', 0x636f6d70616e795f6c6f676f2f64696d636f6e6e6563742e6a7067),
(9, 'Dangote Group', 'Union Marble House 1 Alfred Rewane Road, PMB 40032', '23412712231', 'Active', 'The Dangote Group is a Nigerian multinational industrial conglomerate, founded by Aliko Dangote. It is the largest conglomerate in West Africa and one of the largest on the African continent.', 'communications@dangote.com', 'Manufacturing Industry', 'Business Franchises', 'Nigeria', 'Falomo Ikoyi, Lagos', 'Best Quality Cement ', '2022', 'Dangote Cement gets award By Okwy lroegbu-Chikezie On Mar 1, 2022 The Dangote Cement has won the coveted West Africas Best Quality Cement Brand of the Decade award. The foremost cement manufacturer in Africa won the award with its novel Dangote 3X (42.5) brand.', 0x636f6d70616e795f6c6f676f2f64616e676f74652e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `tblemployees`
--

CREATE TABLE IF NOT EXISTS `tblemployees` (
  `INCID` int(11) NOT NULL AUTO_INCREMENT,
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
  `COMPANYID` int(11) NOT NULL,
  PRIMARY KEY (`INCID`),
  UNIQUE KEY `EMPLOYEEID` (`EMPLOYEEID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;

--
-- Dumping data for table `tblemployees`
--

INSERT INTO `tblemployees` (`INCID`, `EMPLOYEEID`, `FNAME`, `LNAME`, `MNAME`, `ADDRESS`, `BIRTHDATE`, `BIRTHPLACE`, `AGE`, `SEX`, `CIVILSTATUS`, `TELNO`, `EMP_EMAILADDRESS`, `CELLNO`, `POSITION`, `WORKSTATS`, `EMPPHOTO`, `EMPUSERNAME`, `EMPPASSWORD`, `DATEHIRED`, `COMPANYID`) VALUES
(76, '2018001', 'Chambe', 'Narciso', 'Captain', 'mabinay', '1992-01-23', 'Mabinay', 26, 'Male', 'Married', '032656', 'chambe@yahoo.com', '', 'Fuel Tender', '', '', '2018001', 'f3593fd40c55c33d1788309d4137e82f5eab0dea', '2018-05-23', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tblfeedback`
--

CREATE TABLE IF NOT EXISTS `tblfeedback` (
  `FEEDBACKID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `ADMINID` int(11) NOT NULL,
  `SENTBY` int(11) NOT NULL,
  `FEEDBACK` text NOT NULL,
  `DATETIME` datetime NOT NULL,
  `STATUS` varchar(10) NOT NULL,
  PRIMARY KEY (`FEEDBACKID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `tblfeedback`
--

INSERT INTO `tblfeedback` (`FEEDBACKID`, `APPLICANTID`, `ADMINID`, `SENTBY`, `FEEDBACK`, `DATETIME`, `STATUS`) VALUES
(2, 321, 228, 228, 'You are to come for Interview on 10th March 2022\r\n', '2022-03-08 15:46:27', 'Read'),
(3, 321, 228, 228, 'Okay', '2022-03-08 18:06:03', 'Read'),
(4, 321, 228, 228, 'What?', '2022-03-08 18:09:42', 'Read'),
(5, 321, 228, 321, 'Okay Sir', '2022-03-08 18:12:08', 'Read');

-- --------------------------------------------------------

--
-- Table structure for table `tbljob`
--

CREATE TABLE IF NOT EXISTS `tbljob` (
  `JOBID` int(11) NOT NULL AUTO_INCREMENT,
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
  `DATEPOSTED` datetime NOT NULL,
  PRIMARY KEY (`JOBID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tbljob`
--

INSERT INTO `tbljob` (`JOBID`, `COMPANYID`, `WORKPLACE_POLICY`, `JOBTITLE`, `JOBCATEGORYID`, `SALARY`, `JOBTYPE`, `QUALIFICATION`, `JOBDESCRIPTION`, `PREFEREDSEX`, `CAREERLEVEL`, `WORKEXPERIENCE`, `DEADLINE`, `JOBSTATUS`, `DATEPOSTED`) VALUES
(3, 8, 'Hybrid', 'Web Developer', 21, 0, 'Contract', 'B.Sc', 'Skill Requirements: HTML, CSS, JAVASCRIPT, PHP', 'Both', '', '3 Years', '2022-03-27', 'Vacancy', '2022-02-27 22:55:27'),
(4, 8, 'Remote', 'Senior Software Developer', 0, 100000, 'Full Time', 'B.Sc', 'Integrity, Honesty, Leadership', 'Both', 'Team leader', '4 Years', '2022-03-12', 'Vacancy', '2022-02-27 23:58:34'),
(5, 8, 'On-site', 'Administrator', 1, 0, 'Full Time', 'B.Sc', 'Critical thinking and problem solving, Teamwork and collaboration.', 'Both', 'Manager', '3 Years', '2022-03-11', 'Vacancy', '2022-02-28 02:01:45'),
(6, 7, 'On-site', 'Sales Attendant', 2, 0, 'Full Time', 'B.Sc', 'Integrity, Honesty, Punctuality', 'Female', 'Not Necessary', 'No Restriction', '2022-03-24', 'Vacancy', '2022-03-04 02:09:21'),
(7, 7, 'On-site', 'Administrative Assistant', 1, 0, 'Full Time', 'B.Sc', 'Punctuality, Hardworking', 'Both', 'Manager', '1 Years', '2022-03-30', 'Vacancy', '2022-03-04 02:38:40'),
(8, 9, 'On-site', 'Account Manager', 1, 0, 'Full Time', 'B.Sc', 'Responsible for the management of sales and relationships with particular customers. Maintains the companys existing relationships with a client or group of clients, so that they will continue using the company for business. The account manager does not manage the daily running of the account itself. ', 'Both', 'Manager', '3 Years', '2022-03-31', 'Vacancy', '2022-03-09 11:01:14');

-- --------------------------------------------------------

--
-- Table structure for table `tbljobapplication`
--

CREATE TABLE IF NOT EXISTS `tbljobapplication` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `RESUME_FILE` varchar(255) NOT NULL,
  `APPLICATIONSTATUS` varchar(20) NOT NULL,
  `APPLICATIONDATE` datetime NOT NULL,
  `SCORE` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `tbljobapplication`
--

INSERT INTO `tbljobapplication` (`ID`, `APPLICANTID`, `JOBID`, `RESUME_FILE`, `APPLICATIONSTATUS`, `APPLICATIONDATE`, `SCORE`) VALUES
(15, 2745, 4, 'resumes_cv/Certificate_Of_Registration-converted-converted.pdf', 'Pending', '2022-05-10 01:06:21', 7),
(16, 2745, 3, 'resumes_cv/questions for interview.docx', 'Approved', '2022-05-10 17:56:16', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tbljobcategory`
--

CREATE TABLE IF NOT EXISTS `tbljobcategory` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CATEGORY` varchar(200) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

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

CREATE TABLE IF NOT EXISTS `tbljobregistration` (
  `REGISTRATIONID` int(11) NOT NULL AUTO_INCREMENT,
  `COMPANYID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `APPLICANT` varchar(90) NOT NULL,
  `REGISTRATIONDATE` date NOT NULL,
  `REMARKS` varchar(255) NOT NULL DEFAULT 'Pending',
  `FILEID` varchar(30) DEFAULT NULL,
  `PENDINGAPPLICATION` tinyint(1) NOT NULL DEFAULT '1',
  `HVIEW` tinyint(1) NOT NULL DEFAULT '1',
  `DATETIMEAPPROVED` datetime NOT NULL,
  PRIMARY KEY (`REGISTRATIONID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tbljobregistration`
--

INSERT INTO `tbljobregistration` (`REGISTRATIONID`, `COMPANYID`, `JOBID`, `APPLICANTID`, `APPLICANT`, `REGISTRATIONDATE`, `REMARKS`, `FILEID`, `PENDINGAPPLICATION`, `HVIEW`, `DATETIMEAPPROVED`) VALUES
(1, 2, 2, 2018013, 'Kim Domingo', '2018-05-27', 'Ive seen your work and its really interesting', '2147483647', 0, 1, '2018-05-26 16:13:01'),
(2, 2, 2, 2018015, 'Janry Tan', '2018-05-26', 'aasd', '2147483647', 0, 0, '2018-05-28 14:14:45');

-- --------------------------------------------------------

--
-- Table structure for table `tbljobscreening_ques`
--

CREATE TABLE IF NOT EXISTS `tbljobscreening_ques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

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

CREATE TABLE IF NOT EXISTS `tbljobsubcategory` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CATEGORYID` int(11) NOT NULL,
  `SUBCATEGORY` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

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

CREATE TABLE IF NOT EXISTS `tblnotification` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `USERID` int(11) NOT NULL,
  `TYPE` varchar(50) NOT NULL,
  `TYPEID` int(11) NOT NULL,
  `STATUS` varchar(50) NOT NULL,
  `DATETIME` datetime NOT NULL,
  `NOTE` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `tblnotification`
--

INSERT INTO `tblnotification` (`ID`, `USERID`, `TYPE`, `TYPEID`, `STATUS`, `DATETIME`, `NOTE`) VALUES
(2, 321, 'Job Application', 1, 'Unread', '2022-03-08 14:32:50', 'Job Application for (Sales Attendant) status has changed to Approved'),
(3, 321, 'Message', 2, 'Unread', '2022-03-08 15:46:28', 'Message'),
(5, 321, 'Job Application', 6, 'Unread', '2022-03-08 16:05:39', 'Job Application Notice for (Sales Attendant) says: CV/Resume not good enough'),
(6, 0, 'Message', 2, 'Unread', '2022-03-08 18:01:25', 'Message'),
(7, 321, 'Message', 2, 'Unread', '2022-03-08 18:03:59', 'Message'),
(8, 321, 'Message', 3, 'Unread', '2022-03-08 18:06:03', 'Message'),
(9, 321, 'Message', 3, 'Unread', '2022-03-08 18:06:34', 'Message'),
(10, 321, 'Message', 4, 'Unread', '2022-03-08 18:09:42', 'Message'),
(11, 321, 'Message', 5, 'Unread', '2022-03-08 18:12:08', 'Message'),
(12, 479, 'Job Application', 3, 'Unread', '2022-03-09 00:06:54', 'Job Application for (Sales Attendant) status has changed to Pending'),
(13, 479, 'Job Application', 3, 'Unread', '2022-03-09 00:07:02', 'Job Application for (Sales Attendant) status has changed to Pending'),
(14, 479, 'Job Application', 3, 'Unread', '2022-03-09 00:09:02', 'Job Application for (Sales Attendant) status has changed to Pending'),
(15, 479, 'Job Application', 3, 'Unread', '2022-03-09 00:15:24', 'Job Application for (Sales Attendant) status has changed to Pending'),
(16, 228, 'Job Application', 3, 'Unread', '2022-03-09 00:37:45', 'Job Application for (Sales Attendant) status has changed to Pending'),
(17, 228, 'Job Application', 1, 'Unread', '2022-03-09 00:37:52', 'Job Application for (Sales Attendant) status has changed to Approved'),
(18, 228, 'Job Application', 3, 'Unread', '2022-03-09 00:39:52', 'Job Application for (Sales Attendant) status has changed to Pending'),
(19, 228, 'Job Application', 1, 'Unread', '2022-03-09 00:40:06', 'Job Application for (Sales Attendant) status has changed to Approved'),
(20, 228, 'Job Application', 3, 'Unread', '2022-03-09 00:43:30', 'Job Application for (Sales Attendant) status has changed to Pending'),
(21, 228, 'Job Application', 4, 'Unread', '2022-05-09 14:19:25', 'Job Application for (Administrative Assistant) status has changed to Pending'),
(22, 228, 'Job Application', 15, 'Unread', '2022-05-10 11:02:52', 'Job Application for (Senior Software Developer) status has changed to Pending'),
(23, 228, 'Job Application', 15, 'Unread', '2022-05-10 11:04:05', 'Job Application for (Senior Software Developer) status has changed to Pending'),
(24, 228, 'Job Application', 16, 'Unread', '2022-05-10 17:57:24', 'Job Application for (Web Developer) status has changed to Approved');

-- --------------------------------------------------------

--
-- Table structure for table `tblscreening`
--

CREATE TABLE IF NOT EXISTS `tblscreening` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `q_title` varchar(200) NOT NULL,
  `question` text NOT NULL,
  `opt_A` varchar(50) NOT NULL,
  `opt_B` varchar(50) NOT NULL,
  `opt_C` varchar(50) NOT NULL,
  `opt_D` varchar(50) NOT NULL,
  `opt_E` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=56 ;

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

CREATE TABLE IF NOT EXISTS `tblscreening_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `ideal_ans_opt` varchar(11) NOT NULL,
  `ideal_ans` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

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

CREATE TABLE IF NOT EXISTS `tblscreening_qa` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `JOBAPPLICATION_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `QUESTION_ID` int(11) NOT NULL,
  `APPLICANT_ANSWER` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

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
(20, 16, 2745, 3, 29, 'D');

-- --------------------------------------------------------

--
-- Table structure for table `tblscreening_score`
--

CREATE TABLE IF NOT EXISTS `tblscreening_score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jobapp_id` int(11) NOT NULL,
  `score` varchar(10) NOT NULL,
  `total_ques` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tblscreening_score`
--

INSERT INTO `tblscreening_score` (`id`, `jobapp_id`, `score`, `total_ques`) VALUES
(1, 15, '7', '10'),
(2, 16, '3', '10');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE IF NOT EXISTS `tblusers` (
  `USERID` varchar(30) NOT NULL,
  `FNAME` varchar(40) NOT NULL,
  `ONAME` varchar(200) NOT NULL,
  `EMAIL` varchar(50) NOT NULL,
  `USERNAME` varchar(90) NOT NULL,
  `PASS` varchar(90) NOT NULL,
  `ROLE` varchar(30) NOT NULL,
  PRIMARY KEY (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`USERID`, `FNAME`, `ONAME`, `EMAIL`, `USERNAME`, `PASS`, `ROLE`) VALUES
('228', 'Admin', 'Carl', 'admin@gmail.com', 'admin', '$2y$10$vGI.NTvWVDS5bmI/smya7OJI9FIVyT/rwxusgkOIwOY874fpOltwu', 'Administrator'),
('2745', 'Kene', 'Okafor', 'okafor@gmail.com', 'ekene', '$2y$10$XiRW./ReW.kzSWmVJ2wQyuBvKbaSGxOvuNNaGB6mWjcmDul0Vj85G', 'User'),
('321', 'Dom', 'Carl', 'emehchiemerie9@gmail.com', 'emeldo', '$2y$10$6u.ZUBDOBuQZ4K2GzFjD3.Z8e.StDKKCpeuoodzOFeyXX74A.NJ92', 'User'),
('479', 'Chioma', 'Uba', 'Chioma@gmail.com', 'chioma', '$2y$10$UjZgWmcMZbFflPVF/3.XNeqTUNUUayM.XOydi5nQqa1h12VwrwOW6', 'User');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
