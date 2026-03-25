-- ============================================================
-- HTEC - Database Schema
-- Run this file in phpMyAdmin or MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS htec_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE htec_db;

-- ============================================================
-- Users Table (Admin)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Product Categories
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Products Table
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    category_id INT,
    datasheet VARCHAR(500) DEFAULT NULL,
    featured TINYINT(1) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Product Images Table
-- ============================================================
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Portfolio Table
-- ============================================================
CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    client VARCHAR(255),
    project_url VARCHAR(500),
    image VARCHAR(500),
    technologies VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Contact Messages Table
-- ============================================================
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(500),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Services Table
-- ============================================================
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB;

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- IMPORTANT: Do not ship default admin credentials in production.
-- Create the initial admin user manually with a unique strong password, e.g.:
-- INSERT INTO users (username, email, password, role)
-- VALUES ('<admin-user>', '<admin-email>', '<bcrypt-hash-from-password_hash()>', 'admin');

-- Categories
INSERT INTO categories (name, slug) VALUES
('Industrial Automation', 'industrial-automation'),
('Power Systems', 'power-systems'),
('Control Systems', 'control-systems'),
('Sensors & IoT', 'sensors-iot'),
('Software Solutions', 'software-solutions');

-- Products
INSERT INTO products (name, slug, description, short_description, category_id, featured, status) VALUES
('HT-5000 PLC Controller', 'ht-5000-plc-controller',
'The HT-5000 PLC Controller is HTEC\'s flagship programmable logic controller designed for demanding industrial environments. Built with military-grade components, it offers unparalleled reliability and performance in extreme conditions. The system supports up to 512 I/O points and features advanced diagnostics with real-time monitoring capabilities.\n\nKey highlights include redundant CPU architecture, hot-swap module support, and seamless integration with SCADA systems. The HT-5000 supports all major industrial protocols including Modbus RTU/TCP, EtherNet/IP, PROFIBUS, and CANopen.\n\nWith its intuitive programming environment compatible with IEC 61131-3 standards, engineers can develop complex automation sequences quickly and efficiently.',
'Industrial-grade PLC with 512 I/O points, redundant architecture, and multi-protocol support.',
1, 1, 'active'),

('PowerGrid Pro 2000', 'powergrid-pro-2000',
'PowerGrid Pro 2000 is an advanced power distribution management system engineered for critical infrastructure. It provides comprehensive monitoring, protection, and control of electrical distribution networks up to 35kV.\n\nThe system features intelligent load balancing, automatic fault detection and isolation, and predictive maintenance analytics powered by machine learning. Remote monitoring capabilities allow operators to manage entire power networks from a centralized control room.\n\nCertified to IEC 61850 and IEEE 2030 standards, PowerGrid Pro 2000 is suitable for utilities, data centers, manufacturing plants, and smart grid applications.',
'Smart power distribution management up to 35kV with AI-powered predictive maintenance.',
2, 1, 'active'),

('SensorNet IIoT Gateway', 'sensornet-iiot-gateway',
'SensorNet IIoT Gateway bridges the gap between legacy industrial sensors and modern cloud infrastructure. Supporting over 200 industrial protocols, it seamlessly aggregates data from thousands of field devices and transmits processed information to any cloud platform.\n\nBuilt-in edge computing capabilities allow real-time data processing and local decision-making without cloud dependency. The device features dual redundant Ethernet ports, 4G/LTE cellular backup, and end-to-end AES-256 encryption for secure communications.\n\nThe web-based management interface provides drag-and-drop data flow configuration, eliminating the need for custom programming in most deployments.',
'Edge computing IIoT gateway supporting 200+ protocols with built-in cellular backup.',
4, 1, 'active'),

('HTEC SCADA Suite', 'htec-scada-suite',
'HTEC SCADA Suite is a comprehensive supervisory control and data acquisition platform designed for enterprise-scale industrial operations. The software provides real-time visualization, historical trending, alarming, and reporting for complex industrial processes.\n\nThe HTML5-based HMI engine delivers responsive interfaces accessible from any device without plugins. Advanced analytics modules include OEE calculation, energy consumption analysis, and production KPI dashboards.\n\nThe suite scales from small single-site installations to distributed architectures spanning multiple facilities globally, with built-in redundancy and disaster recovery capabilities.',
'Enterprise SCADA platform with HTML5 HMI, advanced analytics, and global scalability.',
5, 1, 'active'),

('MotionMaster Servo Drive', 'motionmaster-servo-drive',
'MotionMaster is a high-performance servo drive system delivering precision motion control for robotics, CNC machinery, and automated manufacturing systems. Available in power ranges from 0.4kW to 110kW, the drive family covers virtually all industrial motion applications.\n\nAdvanced algorithms provide position accuracy to ±0.001mm with dynamic response times under 1ms. The integrated safety functions include STO, SS1, SS2, SOS, and SLS as per IEC 62061 SIL3 certification.\n\nUniversal encoder support (incremental, absolute, resolver, EnDat) and EtherCAT real-time fieldbus ensure compatibility with all major motion control architectures.',
'High-performance servo drives from 0.4-110kW with SIL3 safety certification.',
1, 0, 'active'),

('ThermoSense Pro Series', 'thermosense-pro-series',
'ThermoSense Pro is a family of industrial temperature sensors and transmitters designed for accurate measurement in harsh environments. Available in thermocouple (K, J, T, N, B, S, R types) and RTD (PT100, PT1000) configurations.\n\nAll units feature IP67 protection, ATEX Zone 1 certification for hazardous areas, and SIL2-rated outputs for safety-critical applications. The smart transmitters support HART, Foundation Fieldbus, and Profibus PA communications.\n\nBuilt-in diagnostic functions continuously monitor sensor health, providing early warning of drift or failure before process impact occurs.',
'ATEX-certified temperature sensors and smart transmitters for hazardous environments.',
4, 0, 'active');

-- Portfolio Items
INSERT INTO portfolio (title, description, client, technologies, status) VALUES
('Automotive Assembly Line Automation', 
'Complete automation of a 3-shift automotive body assembly line including 47 ABB robots, 12 HT-5000 PLC systems, and an HTEC SCADA Suite deployment. The project reduced assembly cycle time by 34% and improved quality metrics by 28%.',
'Major European Automotive OEM',
'HT-5000 PLC, HTEC SCADA Suite, ABB Robotics, EtherCAT',
'active'),

('Smart Grid Implementation - 500MW Plant',
'End-to-end smart grid deployment for a 500MW combined cycle power plant including PowerGrid Pro 2000 systems, advanced metering infrastructure, and real-time energy management platform. Achieved 12% efficiency improvement.',
'National Energy Corporation',
'PowerGrid Pro 2000, AMI, Energy Management System',
'active'),

('Offshore Platform IIoT Upgrade',
'Retrofit of legacy SCADA systems on three offshore oil platforms with SensorNet IIoT Gateways, enabling real-time condition monitoring of 3,200 field instruments with predictive maintenance analytics.',
'North Sea Energy Operations',
'SensorNet IIoT, HTEC SCADA Suite, Edge Analytics',
'active'),

('Pharmaceutical Clean Room Monitoring',
'Environmental monitoring and control system for 24 clean rooms across two pharmaceutical manufacturing facilities. FDA 21 CFR Part 11 compliant with complete audit trail and electronic batch records.',
'Global Pharma Holdings',
'ThermoSense Pro, Custom Controllers, Validation Suite',
'active'),

('Water Treatment Plant Modernization',
'Full SCADA modernization for municipal water treatment plant serving 2.4 million residents. Included new HMI interfaces, historian upgrade, and cybersecurity hardening per NIST SP 800-82.',
'City Municipal Authority',
'HTEC SCADA Suite, OT Cybersecurity, HMI Upgrade',
'active');

-- Services
INSERT INTO services (title, description, icon, sort_order) VALUES
('Industrial Automation', 'End-to-end automation solutions for manufacturing, process, and discrete industries. From concept to commissioning, we deliver systems that maximize efficiency and reliability.', 'fas fa-cogs', 1),
('Power Systems Engineering', 'Complete power system solutions including design, protection relay engineering, SCADA integration, and arc flash studies for utilities and industrial facilities.', 'fas fa-bolt', 2),
('IIoT & Digital Transformation', 'Transform your operations with Industrial IoT solutions. We connect legacy systems to modern cloud platforms, enabling data-driven decision making across your enterprise.', 'fas fa-network-wired', 3),
('Control System Integration', 'Expert integration of PLCs, DCS, SCADA, and HMI systems. We work with all major platforms including Siemens, Rockwell, ABB, and Schneider Electric.', 'fas fa-microchip', 4),
('Cybersecurity for OT/ICS', 'Protect your operational technology with purpose-built cybersecurity solutions. Risk assessments, network segmentation, and continuous monitoring for industrial control systems.', 'fas fa-shield-alt', 5),
('Engineering Consulting', 'Leverage our 20+ years of industry expertise for technical consulting, feasibility studies, and independent verification services.', 'fas fa-drafting-compass', 6);
