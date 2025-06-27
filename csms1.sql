-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 06:33 AM
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
-- Database: `csms1`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `bank_master_fillmodel` (IN `p_bank_id` INT)   BEGIN
SELECT * 
       FROM `tbl_bank_master` 
        WHERE bank_id= p_bank_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `bank_master_transaction` (INOUT `p_bank_id` INT, IN `p_bank_name` VARCHAR(100), IN `p_branch_name` VARCHAR(100), IN `p_account_no` VARCHAR(100), IN `p_ifs_code` VARCHAR(100), IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_bank_id = (SELECT COALESCE(MAX(bank_id),0) + 1 FROM tbl_bank_master);

                    insert into tbl_bank_master
                    (
                        bank_id,
                        bank_name,
                        branch_name,
                        account_no,
                        ifs_code,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_bank_id,
                        p_bank_name,
                        p_branch_name,
                        p_account_no,
                        p_ifs_code,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_bank_master
                
                SET
                        bank_name=COALESCE(p_bank_name,bank_name),
                        branch_name=COALESCE(p_branch_name,branch_name),
                        account_no=COALESCE(p_account_no,account_no),
                        ifs_code=COALESCE(p_ifs_code,ifs_code),
                        status=COALESCE(p_status,status),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE bank_id= p_bank_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_bank_master WHERE bank_id= p_bank_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `chamber_master_fillmodel` (IN `p_chamber_id` INT)   BEGIN
SELECT * 
       FROM `tbl_chamber_master` 
        WHERE chamber_id= p_chamber_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `chamber_master_transaction` (INOUT `p_chamber_id` INT, IN `p_chamber_name` VARCHAR(100), IN `p_created_date` DATE, IN `p_created_by` INT, IN `p_modified_date` DATE, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_chamber_id = (SELECT COALESCE(MAX(chamber_id),0) + 1 FROM tbl_chamber_master);

                    insert into tbl_chamber_master
                    (
                        chamber_id,
                        chamber_name,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_chamber_id,
                        p_chamber_name,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_chamber_master
                
                SET
                        chamber_name=COALESCE(p_chamber_name,chamber_name),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE chamber_id= p_chamber_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_chamber_master WHERE chamber_id= p_chamber_id;
                ALTER TABLE tbl_chamber_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `city_master_fillmodel` (IN `p_city_id` INT)   BEGIN
    SELECT c.*, co.country_name
    FROM tbl_city_master c
    LEFT JOIN tbl_country_master co ON c.country_id = co.country_id
    WHERE c.city_id = p_city_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `city_master_transaction` (INOUT `p_city_id` INT, IN `p_city_name` VARCHAR(100), IN `p_state_id` INT, IN `p_country_id` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_city_id = (SELECT COALESCE(MAX(city_id),0) + 1 FROM tbl_city_master);

                    insert into tbl_city_master
                    (
                        city_id,
                        city_name,
                        state_id,
                        country_id,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_city_id,
                        p_city_name,
                        p_state_id,
                        p_country_id,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_city_master
                
                SET
                        city_name=COALESCE(p_city_name,city_name),
                        state_id=COALESCE(p_state_id,state_id),
                        country_id=COALESCE(p_country_id,country_id),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE city_id= p_city_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_city_master WHERE city_id= p_city_id;
                ALTER TABLE tbl_city_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_master_fillmodel` (IN `p_company_id` INT)   BEGIN
SELECT * 
       FROM `tbl_company_master` 
        WHERE company_id= p_company_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_master_transaction` (INOUT `p_company_id` INT, IN `p_company_name` VARCHAR(500), IN `p_company_code` VARCHAR(100), IN `p_company_logo` BLOB, IN `p_company_logo_url` VARCHAR(255), IN `p_address` TEXT, IN `p_city` VARCHAR(50), IN `p_pincode` VARCHAR(50), IN `p_state` VARCHAR(50), IN `p_phone` VARCHAR(50), IN `p_email` VARCHAR(100), IN `p_web_address` VARCHAR(100), IN `p_gstin` VARCHAR(50), IN `p_bank_id` INT, IN `p_jurisdiction` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_company_id = (SELECT COALESCE(MAX(company_id),0) + 1 FROM tbl_company_master);

                    insert into tbl_company_master
                    (
                        company_id,
                        company_name,
                        company_code,
                        company_logo,
                        company_logo_url,
                        address,
                        city,
                        pincode,
                        state,
                        phone,
                        email,
                        web_address,
                        gstin,
                        bank_id,
                        jurisdiction,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_company_id,
                        p_company_name,
                        p_company_code,
                        p_company_logo,
                        p_company_logo_url,
                        p_address,
                        p_city,
                        p_pincode,
                        p_state,
                        p_phone,
                        p_email,
                        p_web_address,
                        p_gstin,
                        p_bank_id,
                        p_jurisdiction,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_company_master
                
                SET
                        company_name=COALESCE(p_company_name,company_name),
                        company_code=COALESCE(p_company_code,company_code),
                        company_logo=COALESCE(p_company_logo,company_logo),
                        company_logo_url=COALESCE(p_company_logo_url,company_logo_url),
                        address=COALESCE(p_address,address),
                        city=COALESCE(p_city,city),
                        pincode=COALESCE(p_pincode,pincode),
                        state=COALESCE(p_state,state),
                        phone=COALESCE(p_phone,phone),
                        email=COALESCE(p_email,email),
                        web_address=COALESCE(p_web_address,web_address),
                        gstin=COALESCE(p_gstin,gstin),
                        bank_id=COALESCE(p_bank_id,bank_id),
                        jurisdiction=COALESCE(p_jurisdiction,jurisdiction),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by)

                WHERE company_id= p_company_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_company_master WHERE company_id= p_company_id;
                ALTER TABLE tbl_company_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_year_master_fillmodel` (IN `p_company_year_id` INT)   BEGIN
SELECT * 
       FROM `tbl_company_year_master` 
        WHERE company_year_id= p_company_year_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_year_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_year_master_transaction` (INOUT `p_company_year_id` INT, IN `p_company_id` INT, IN `p_company_year_type` VARCHAR(100), IN `p_start_date` DATE, IN `p_end_date` DATE, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_company_year_id = (SELECT COALESCE(MAX(company_year_id),0) + 1 FROM tbl_company_year_master);

                    insert into tbl_company_year_master
                    (
                        company_year_id,
                        company_id,
                        company_year_type,
                        start_date,
                        end_date,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_company_year_id,
                        p_company_id,
                        p_company_year_type,
                        p_start_date,
                        p_end_date,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_company_year_master
                
                SET
                        company_id=COALESCE(p_company_id,company_id),
                        company_year_type=COALESCE(p_company_year_type,company_year_type),
                        start_date=COALESCE(p_start_date,start_date),
                        end_date=COALESCE(p_end_date,end_date),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by)

                WHERE company_year_id= p_company_year_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_company_year_master WHERE company_year_id= p_company_year_id;
                ALTER TABLE tbl_company_year_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `contact_person_detail_fillmodel` (IN `p_contact_person_id` INT)   BEGIN
SELECT * 
       FROM `tbl_contact_person_detail` 
        WHERE contact_person_id= p_contact_person_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `contact_person_detail_transaction` (INOUT `p_contact_person_id` INT, IN `p_customer_id` INT, IN `p_contact_person_name` VARCHAR(100), IN `p_mobile` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_is_whatsapp` TINYINT, IN `p_is_email` TINYINT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_contact_person_id = (SELECT COALESCE(MAX(contact_person_id),0) + 1 FROM tbl_contact_person_detail);

                    insert into tbl_contact_person_detail
                    (
                        contact_person_id,
                            customer_id,
                            contact_person_name,
                            mobile,
                            email,
                            is_whatsapp,
                            is_email
                    )
                    values
                    ( 
                        p_contact_person_id,
                            p_customer_id,
                            p_contact_person_name,
                            p_mobile,
                            p_email,
                            p_is_whatsapp,
                            p_is_email
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_contact_person_detail
                
                SET
                        customer_id=COALESCE(p_customer_id,customer_id),
                        contact_person_name=COALESCE(p_contact_person_name,contact_person_name),
                        mobile=COALESCE(p_mobile,mobile),
                        email=COALESCE(p_email,email),
                        is_whatsapp=COALESCE(p_is_whatsapp,is_whatsapp),
                        is_email=COALESCE(p_is_email,is_email)

                WHERE contact_person_id= p_contact_person_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_contact_person_detail WHERE contact_person_id= p_contact_person_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `country_master_fillmodel` (IN `p_country_id` INT)   BEGIN
    SELECT *
    FROM tbl_country_master c
    WHERE c.country_id = p_country_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `country_master_transaction` (INOUT `p_country_id` INT, IN `p_country_name` VARCHAR(100), IN `p_company_id` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_country_id = (SELECT COALESCE(MAX(country_id),0) + 1 FROM tbl_country_master);

                    insert into tbl_country_master
                    (
                        country_id,
                        country_name,
                        company_id,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_country_id,
                        p_country_name,
                        p_company_id,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_country_master
                
                SET
                        country_name=COALESCE(p_country_name,country_name),
                        company_id=COALESCE(p_company_id,company_id),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by)

                WHERE country_id= p_country_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_country_master WHERE country_id= p_country_id;
                ALTER TABLE tbl_country_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_check_duplicate` (IN `p_column_name` VARCHAR(50), IN `p_column_value` VARCHAR(50), IN `p_id_name` VARCHAR(50), IN `p_id_value` INT, IN `p_table_name` VARCHAR(50), OUT `is_duplicate` BOOLEAN)   BEGIN
    DECLARE duplicate_count INT;

    -- Build the SQL query dynamically
    SET @query = CONCAT('SELECT COUNT(*) INTO @duplicate_count FROM ', p_table_name,
                        ' WHERE ', p_column_name, ' = "', p_column_value, '" ',
                        ' AND (', p_id_value, ' IS NULL OR ', p_id_name, ' <> ', p_id_value, ')');

    -- Prepare and execute the query
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Set output variable
    IF @duplicate_count > 0 THEN
        SET is_duplicate = TRUE;
    ELSE
        SET is_duplicate = FALSE;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_getval` (IN `p_field` VARCHAR(10), IN `p_field_val` VARCHAR(10), IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
    SET @sql = CONCAT('SELECT ', columns, ' FROM ', tableName, ' WHERE ', p_field, ' = "', p_field_val, '"');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_menu_search` (IN `in_user_id` INT)   BEGIN
    IF in_user_id = 0 THEN
        -- Return all visible modules and menus
        SELECT 
            m.module_text, 
            mm.menu_text,   
            mm.menu_url AS menu_link,
            mm.menu_group
        FROM tbl_menu_master mm
        INNER JOIN tbl_module_master m ON mm.module_id = m.module_id
        WHERE mm.is_display = 1
        ORDER BY m.tab_index, mm.tab_index, mm.menu_group, mm.menu_text;
    ELSE
        -- Return only modules and menus for which the user has at least one right
        SELECT 
            m.module_text, 
            mm.menu_text,   
            mm.menu_url AS menu_link,
            mm.menu_group
        FROM tbl_user_right_master urm
        INNER JOIN tbl_menu_right_master mrm ON urm.menu_right_id = mrm.menu_right_id
        INNER JOIN tbl_menu_master mm ON mrm.menu_id = mm.menu_id
        INNER JOIN tbl_module_master m ON mm.module_id = m.module_id
        WHERE urm.user_id = in_user_id
          AND urm.has_right = 1
          AND mm.is_display = 1
        GROUP BY mm.menu_id
        ORDER BY m.tab_index, mm.tab_index, mm.menu_group, mm.menu_text;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
    -- Declare a variable to store the dynamically constructed SQL
    SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
    
    -- Prepare the SQL statement for execution
    PREPARE stmt FROM @sql;
    
    -- Execute the prepared statement
    EXECUTE stmt;
    
    -- Deallocate the prepared statement to free resources
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_search_detail` (IN `columns` TEXT, IN `tableName` TEXT, IN `whereColumn` TEXT)   BEGIN

SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName, " WHERE 1=1");


IF whereColumn IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND ', whereColumn);
    END IF;
   

PREPARE stmt FROM @sql;

EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_search_with_joins` (IN `columns` TEXT, IN `tables` TEXT, IN `joinTypes` TEXT, IN `joinConditions` TEXT)   BEGIN
    DECLARE tableList TEXT;
    DECLARE joinTypeList TEXT;
    DECLARE conditionList TEXT;
    DECLARE tableArray TEXT;
    DECLARE joinArray TEXT;
    DECLARE conditionArray TEXT;
    DECLARE numJoins INT;
    DECLARE i INT DEFAULT 1;
    DECLARE resultQuery TEXT;

    -- Split tables, join types, and join conditions
    SET tableArray = tables;
    SET joinArray = joinTypes;
    SET conditionArray = joinConditions;
    
    -- Get the first table
    SET tableList = SUBSTRING_INDEX(tableArray, ',', 1);
    SET tableArray = SUBSTRING(tableArray FROM LOCATE(',', tableArray) + 1);

    -- Initialize the final query
    SET resultQuery = CONCAT("SELECT ", columns, " FROM ", tableList);

    -- Count the number of joins (tables - 1)
    SET numJoins = (LENGTH(tables) - LENGTH(REPLACE(tables, ',', '')));

    -- Loop through the remaining tables and build JOIN statements
    WHILE i <= numJoins DO
        -- Get next join type, table, and condition
        SET joinTypeList = SUBSTRING_INDEX(joinArray, ',', 1);
        SET joinArray = IF(LOCATE(',', joinArray) > 0, SUBSTRING(joinArray FROM LOCATE(',', joinArray) + 1), '');

        SET tableList = SUBSTRING_INDEX(tableArray, ',', 1);
        SET tableArray = IF(LOCATE(',', tableArray) > 0, SUBSTRING(tableArray FROM LOCATE(',', tableArray) + 1), '');

        SET conditionList = SUBSTRING_INDEX(conditionArray, ',', 1);
        SET conditionArray = IF(LOCATE(',', conditionArray) > 0, SUBSTRING(conditionArray FROM LOCATE(',', conditionArray) + 1), '');

        -- Append the JOIN clause
        SET resultQuery = CONCAT(resultQuery, ' ', joinTypeList, ' JOIN ', tableList, ' ON ', conditionList);

        SET i = i + 1;
    END WHILE;

    -- Prepare and execute the query
    SET @sql = resultQuery;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `currency_master_fillmodel` (IN `p_currency_id` INT)   BEGIN
SELECT * 
       FROM `tbl_currency_master` 
        WHERE currency_id= p_currency_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `currency_master_transaction` (INOUT `p_currency_id` INT, IN `p_currency_symbol` VARCHAR(100), IN `p_currency_name` VARCHAR(100), IN `p_currency_in_paise` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_currency_id = (SELECT COALESCE(MAX(currency_id),0) + 1 FROM tbl_currency_master);

                    insert into tbl_currency_master
                    (
                        currency_id,
                        currency_symbol,
                        currency_name,
                        currency_in_paise,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_currency_id,
                        p_currency_symbol,
                        p_currency_name,
                        p_currency_in_paise,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_currency_master
                
                SET
                        currency_symbol=COALESCE(p_currency_symbol,currency_symbol),
                        currency_name=COALESCE(p_currency_name,currency_name),
                        currency_in_paise=COALESCE(p_currency_in_paise,currency_in_paise),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE currency_id= p_currency_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_currency_master WHERE currency_id= p_currency_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_account_group_master_fillmodel` (IN `p_customer_account_group_id` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_account_group_master` 
        WHERE customer_account_group_id= p_customer_account_group_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_account_group_master_transaction` (INOUT `p_customer_account_group_id` INT, IN `p_customer_account_group_name` VARCHAR(100), IN `p_under_group` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_customer_account_group_id = (SELECT COALESCE(MAX(customer_account_group_id),0) + 1 FROM tbl_customer_account_group_master);

                    insert into tbl_customer_account_group_master
                    (
                        customer_account_group_id,
                        customer_account_group_name,
                        under_group,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_customer_account_group_id,
                        p_customer_account_group_name,
                        p_under_group,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_customer_account_group_master
                
                SET
                        customer_account_group_name=COALESCE(p_customer_account_group_name,customer_account_group_name),
                        under_group=COALESCE(p_under_group,under_group),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE customer_account_group_id= p_customer_account_group_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_customer_account_group_master WHERE customer_account_group_id= p_customer_account_group_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_item_preservation_price_list_master_fillmodel` (IN `p_customer_item_preservation_price_list` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_item_preservation_price_list_master` 
        WHERE customer_item_preservation_price_list= p_customer_item_preservation_price_list;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_item_preservation_price_list_master_transaction` (INOUT `p_customer_item_preservation_price_list` INT, IN `p_customer_id` INT, IN `p_item_id` INT, IN `p_rent_kg_per_month` DECIMAL, IN `p_rent_per_kg` DECIMAL, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    IF TransactionMode = 'I' THEN
        SET p_customer_item_preservation_price_list = (
            SELECT COALESCE(MAX(customer_item_preservation_price_list),0) + 1
            FROM tbl_customer_item_preservation_price_list_master
        );

        INSERT INTO tbl_customer_item_preservation_price_list_master
        (
            customer_item_preservation_price_list,
            customer_id,
            item_id,
            rent_kg_per_month,
            rent_per_kg,
            created_date,
            created_by,
            modified_date,
            modified_by,
            company_year_id
        )
        VALUES
        (
            p_customer_item_preservation_price_list,
            p_customer_id,
            p_item_id,
            p_rent_kg_per_month,
            p_rent_per_kg,
            p_created_date,
            p_created_by,
            p_modified_date,
            p_modified_by,
            p_company_year_id
        );

    ELSEIF TransactionMode = 'U' THEN

        UPDATE tbl_customer_item_preservation_price_list_master
        SET
            customer_id = p_customer_id,
            item_id = p_item_id,
            rent_kg_per_month = p_rent_kg_per_month,
            rent_per_kg = p_rent_per_kg,
            created_date = p_created_date,
            created_by = p_created_by,
            modified_date = p_modified_date,
            modified_by = p_modified_by
        WHERE customer_item_preservation_price_list = p_customer_item_preservation_price_list
          AND company_year_id = p_company_year_id;

    ELSEIF TransactionMode = 'D' THEN

        DELETE FROM tbl_customer_item_preservation_price_list_master
        WHERE customer_item_preservation_price_list = p_customer_item_preservation_price_list
          AND company_year_id = p_company_year_id;

    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_master_fillmodel` (IN `p_customer_id` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_master` 
        WHERE customer_id= p_customer_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_master_transaction` (INOUT `p_customer_id` INT, IN `p_customer` VARCHAR(100), IN `p_customer_name` VARCHAR(100), IN `p_customer_type` INT, IN `p_account_group_id` INT, IN `p_address` TEXT, IN `p_city_id` INT, IN `p_pincode` VARCHAR(100), IN `p_state_id` INT, IN `p_country_id` INT, IN `p_phone` VARCHAR(100), IN `p_email_id` VARCHAR(100), IN `p_web_address` VARCHAR(100), IN `p_gstin` VARCHAR(100), IN `p_pan` VARCHAR(100), IN `p_aadhar_no` VARCHAR(100), IN `p_mandli_license_no` VARCHAR(100), IN `p_fssai_license_no` VARCHAR(100), IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_customer_id = (SELECT COALESCE(MAX(customer_id),0) + 1 FROM tbl_customer_master);

                    insert into tbl_customer_master
                    (
                        customer_id,
                        customer,
                        customer_name,
                        customer_type,
                        account_group_id,
                        address,
                        city_id,
                        pincode,
                        state_id,
                        country_id,
                        phone,
                        email_id,
                        web_address,
                        gstin,
                        pan,
                        aadhar_no,
                        mandli_license_no,
                        fssai_license_no,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_customer_id,
                        p_customer,
                        p_customer_name,
                        p_customer_type,
                        p_account_group_id,
                        p_address,
                        p_city_id,
                        p_pincode,
                        p_state_id,
                        p_country_id,
                        p_phone,
                        p_email_id,
                        p_web_address,
                        p_gstin,
                        p_pan,
                        p_aadhar_no,
                        p_mandli_license_no,
                        p_fssai_license_no,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_customer_master
                
                SET
                        customer=COALESCE(p_customer,customer),
                        customer_name=COALESCE(p_customer_name,customer_name),
                        customer_type=COALESCE(p_customer_type,customer_type),
                        account_group_id=COALESCE(p_account_group_id,account_group_id),
                        address=COALESCE(p_address,address),
                        city_id=COALESCE(p_city_id,city_id),
                        pincode=COALESCE(p_pincode,pincode),
                        state_id=COALESCE(p_state_id,state_id),
                        country_id=COALESCE(p_country_id,country_id),
                        phone=COALESCE(p_phone,phone),
                        email_id=COALESCE(p_email_id,email_id),
                        web_address=COALESCE(p_web_address,web_address),
                        gstin=COALESCE(p_gstin,gstin),
                        pan=COALESCE(p_pan,pan),
                        aadhar_no=COALESCE(p_aadhar_no,aadhar_no),
                        mandli_license_no=COALESCE(p_mandli_license_no,mandli_license_no),
                        fssai_license_no=COALESCE(p_fssai_license_no,fssai_license_no),
                        status=COALESCE(p_status,status),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE customer_id= p_customer_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_customer_master WHERE customer_id= p_customer_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_item_preservation_price_list_detail_fillmodel` (IN `p_customer_wise_item_preservation_price_list_detail_id` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_wise_item_preservation_price_list_detail` 
        WHERE customer_wise_item_preservation_price_list_detail_id= p_customer_wise_item_preservation_price_list_detail_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_item_preservation_price_list_detail_transaction` (INOUT `p_customer_wise_item_preservation_price_list_detail_id` INT, IN `p_customer_wise_item_preservation_price_list_id` INT, IN `p_packing_unit_id` INT, IN `p_rent_per_qty_month` DECIMAL(18,2), IN `p_rent_per_qty_season` DECIMAL(18,2), IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_customer_wise_item_preservation_price_list_detail_id = (SELECT COALESCE(MAX(customer_wise_item_preservation_price_list_detail_id),0) + 1 FROM tbl_customer_wise_item_preservation_price_list_detail);

                    insert into tbl_customer_wise_item_preservation_price_list_detail
                    (
                        customer_wise_item_preservation_price_list_detail_id,
                            customer_wise_item_preservation_price_list_id,
                            packing_unit_id,
                            rent_per_qty_month,
                            rent_per_qty_season
                    )
                    values
                    ( 
                        p_customer_wise_item_preservation_price_list_detail_id,
                            p_customer_wise_item_preservation_price_list_id,
                            p_packing_unit_id,
                            p_rent_per_qty_month,
                            p_rent_per_qty_season
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_customer_wise_item_preservation_price_list_detail
                
                SET
                        customer_wise_item_preservation_price_list_id=COALESCE(p_customer_wise_item_preservation_price_list_id,customer_wise_item_preservation_price_list_id),
                        packing_unit_id=COALESCE(p_packing_unit_id,packing_unit_id),
                        rent_per_qty_month=COALESCE(p_rent_per_qty_month,rent_per_qty_month),
                        rent_per_qty_season=COALESCE(p_rent_per_qty_season,rent_per_qty_season)

                WHERE customer_wise_item_preservation_price_list_detail_id= p_customer_wise_item_preservation_price_list_detail_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_customer_wise_item_preservation_price_list_detail WHERE customer_wise_item_preservation_price_list_detail_id= p_customer_wise_item_preservation_price_list_detail_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_item_preservation_price_list_master_fillmodel` (IN `p_customer_wise_item_preservation_price_list_id` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_wise_item_preservation_price_list_master` 
        WHERE customer_wise_item_preservation_price_list_id= p_customer_wise_item_preservation_price_list_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_item_preservation_price_list_master_transaction` (INOUT `p_customer_wise_item_preservation_price_list_id` INT, IN `p_customer_id` INT, IN `p_item_id` INT, IN `p_rent_per_kg_month` DECIMAL(18,2), IN `p_rent_per_kg_season` DECIMAL(18,3), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `p_company_year_id` INT, IN `TransactionMode` CHAR(1))   BEGIN

    IF TransactionMode = 'I' THEN

        SET p_customer_wise_item_preservation_price_list_id = (
            SELECT COALESCE(MAX(customer_wise_item_preservation_price_list_id),0) + 1
            FROM tbl_customer_wise_item_preservation_price_list_master
        );

        INSERT INTO tbl_customer_wise_item_preservation_price_list_master
        (
            customer_wise_item_preservation_price_list_id,
            customer_id,
            item_id,
            rent_per_kg_month,
            rent_per_kg_season,
            created_date,
            created_by,
            modified_date,
            modified_by,
            company_id,
            company_year_id          -- Added here
        )
        VALUES
        (
            p_customer_wise_item_preservation_price_list_id,
            p_customer_id,
            p_item_id,
            p_rent_per_kg_month,
            p_rent_per_kg_season,
            p_created_date,
            p_created_by,
            p_modified_date,
            p_modified_by,
            p_company_id,
            p_company_year_id        -- Added here
        );

    ELSEIF TransactionMode = 'U' THEN

        UPDATE tbl_customer_wise_item_preservation_price_list_master

        SET
            customer_id = COALESCE(p_customer_id,customer_id),
            item_id = COALESCE(p_item_id,item_id),
            rent_per_kg_month = COALESCE(p_rent_per_kg_month,rent_per_kg_month),
            rent_per_kg_season = COALESCE(p_rent_per_kg_season,rent_per_kg_season),
            created_date = COALESCE(p_created_date,created_date),
            created_by = COALESCE(p_created_by,created_by),
            modified_date = COALESCE(p_modified_date,modified_date),
            modified_by = COALESCE(p_modified_by,modified_by),
            company_id = COALESCE(p_company_id,company_id),
            company_year_id = COALESCE(p_company_year_id,company_year_id) -- Added here

        WHERE customer_wise_item_preservation_price_list_id = p_customer_wise_item_preservation_price_list_id;

    ELSEIF TransactionMode = 'D' THEN

        DELETE FROM tbl_customer_wise_item_preservation_price_list_master
        WHERE customer_wise_item_preservation_price_list_id = p_customer_wise_item_preservation_price_list_id;
        ALTER TABLE tbl_customer_wise_item_preservation_price_list_master AUTO_INCREMENT = 1;

    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_tem_preservation_price_list_master_fillmodel` (IN `p_customer_wise_item_preservation_price_list` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_wise_tem_preservation_price_list_master` 
        WHERE customer_wise_item_preservation_price_list= p_customer_wise_item_preservation_price_list;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_tem_preservation_price_list_master_transaction` (INOUT `p_customer_wise_item_preservation_price_list` INT, IN `p_customer_id` INT, IN `p_item_id` INT, IN `p_rent_kg_per_month` DECIMAL, IN `p_rent_per_kg` DECIMAL, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_customer_wise_item_preservation_price_list = (SELECT COALESCE(MAX(customer_wise_item_preservation_price_list),0) + 1 FROM tbl_customer_wise_tem_preservation_price_list_master);

                    insert into tbl_customer_wise_tem_preservation_price_list_master
                    (
                        customer_wise_item_preservation_price_list,
                        customer_id,
                        item_id,
                        rent_kg_per_month,
                        rent_per_kg,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_customer_wise_item_preservation_price_list,
                        p_customer_id,
                        p_item_id,
                        p_rent_kg_per_month,
                        p_rent_per_kg,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_customer_wise_tem_preservation_price_list_master
                
                SET
                        customer_id= p_customer_id,
                        item_id= p_item_id,
                        rent_kg_per_month= p_rent_kg_per_month,
                        rent_per_kg= p_rent_per_kg,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE customer_wise_item_preservation_price_list= p_customer_wise_item_preservation_price_list;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_customer_wise_tem_preservation_price_list_master WHERE customer_wise_item_preservation_price_list= p_customer_wise_item_preservation_price_list;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `floor_master_fillmodel` (IN `p_floor_id` INT)   BEGIN
SELECT * 
       FROM `tbl_floor_master` 
        WHERE floor_id= p_floor_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `floor_master_transaction` (INOUT `p_floor_id` INT, IN `p_floor_name` VARCHAR(100), IN `p_chamber_id` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_floor_id = (SELECT COALESCE(MAX(floor_id),0) + 1 FROM tbl_floor_master);

                    insert into tbl_floor_master
                    (
                        floor_id,
                        floor_name,
                        chamber_id,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_floor_id,
                        p_floor_name,
                        p_chamber_id,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_floor_master
                
                SET
                        floor_name=COALESCE(p_floor_name,floor_name),
                        chamber_id=COALESCE(p_chamber_id,chamber_id),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE floor_id= p_floor_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_floor_master WHERE floor_id= p_floor_id;
                ALTER TABLE tbl_floor_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `gst_tax_detail_fillmodel` (IN `p_gst_tax_id` INT)   BEGIN
SELECT * 
       FROM `tbl_gst_tax_detail` 
        WHERE gst_tax_id= p_gst_tax_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `gst_tax_detail_transaction` (INOUT `p_gst_tax_id` INT, IN `p_hsn_code_id` INT, IN `p_tax_type` VARCHAR(100), IN `p_tax` DECIMAL, IN `p_effective_date` DATE, IN `p_remark` VARCHAR(100), IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_gst_tax_id = (SELECT COALESCE(MAX(gst_tax_id),0) + 1 FROM tbl_gst_tax_detail);

                    insert into tbl_gst_tax_detail
                    (
                        gst_tax_id,
                            hsn_code_id,
                            tax_type,
                            tax,
                            effective_date,
                            remark
                    )
                    values
                    ( 
                        p_gst_tax_id,
                            p_hsn_code_id,
                            p_tax_type,
                            p_tax,
                            p_effective_date,
                            p_remark
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_gst_tax_detail
                
                SET
                        hsn_code_id=COALESCE(p_hsn_code_id,hsn_code_id),
                        tax_type=COALESCE(p_tax_type,tax_type),
                        tax=COALESCE(p_tax,tax),
                        effective_date=COALESCE(p_effective_date,effective_date),
                        remark=COALESCE(p_remark,remark)

                WHERE gst_tax_id= p_gst_tax_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_gst_tax_detail WHERE gst_tax_id= p_gst_tax_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_code_master_fillmodel` (IN `p_hsn_code_id` INT)   BEGIN
SELECT * 
       FROM `tbl_hsn_code_master` 
        WHERE hsn_code_id= p_hsn_code_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_code_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_code_master_transaction` (INOUT `p_hsn_code_id` INT, IN `p_hsn_code_name` VARCHAR(100), IN `p_description` VARCHAR(500), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_hsn_code_id = (SELECT COALESCE(MAX(hsn_code_id),0) + 1 FROM tbl_hsn_code_master);

                    insert into tbl_hsn_code_master
                    (
                        hsn_code_id,
                        hsn_code_name,
                        description,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_hsn_code_id,
                        p_hsn_code_name,
                        p_description,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_hsn_code_master
                
                SET
                        hsn_code_name=COALESCE(p_hsn_code_name,hsn_code_name),
                        description=COALESCE(p_description,description),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE hsn_code_id= p_hsn_code_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_hsn_code_master WHERE hsn_code_id= p_hsn_code_id;
                ALTER TABLE tbl_hsn_code_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_master_fillmodel` (IN `p_hsn_id` INT)   BEGIN
SELECT * 
       FROM `tbl_hsn_master` 
        WHERE hsn_id= p_hsn_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_master_transaction` (INOUT `p_hsn_id` INT, IN `p_hsn_code` VARCHAR(100), IN `p_description` VARCHAR(500), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_hsn_id = (SELECT COALESCE(MAX(hsn_id),0) + 1 FROM tbl_hsn_master);

                    insert into tbl_hsn_master
                    (
                        hsn_id,
                        hsn_code,
                        description,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_hsn_id,
                        p_hsn_code,
                        p_description,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_hsn_master
                
                SET
                        hsn_code= p_hsn_code,
                        description= p_description,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE hsn_id= p_hsn_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_hsn_master WHERE hsn_id= p_hsn_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `inward_detail_fillmodel` (IN `p_inward_detail_id` BIGINT)   BEGIN
SELECT * 
       FROM `tbl_inward_detail` 
        WHERE inward_detail_id= p_inward_detail_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `inward_detail_transaction` (INOUT `p_inward_detail_id` BIGINT, IN `p_inward_id` BIGINT, IN `p_lot_no` VARCHAR(100), IN `p_item` INT, IN `p_gst_type` INT, IN `p_marko` VARCHAR(100), IN `p_packing_unit` INT, IN `p_inward_qty` INT, IN `p_inward_wt` DECIMAL(18,2), IN `p_avg_wt_per_bag` DECIMAL(18,2), IN `p_location` VARCHAR(255), IN `p_moisture` VARCHAR(100), IN `p_storage_duration` INT, IN `p_rent_per_month` DECIMAL(18,2), IN `p_rent_per_storage_duration` DECIMAL(18,2), IN `p_seasonal_start_date` DATE, IN `p_seasonal_end_date` DATE, IN `p_rent_per` INT, IN `p_unloading_charge` DECIMAL(18,2), IN `p_remark` VARCHAR(500), IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_inward_detail_id = (SELECT COALESCE(MAX(inward_detail_id),0) + 1 FROM tbl_inward_detail);

                insert into tbl_inward_detail
                (
                    inward_detail_id,
                    inward_id,
                    lot_no,
                    item,
                    gst_type,
                    marko,
                    packing_unit,
                    inward_qty,
                    inward_wt,
                    avg_wt_per_bag,
                    location,
                    moisture,
                    storage_duration,
                    rent_per_month,
                    rent_per_storage_duration, 
                    seasonal_start_date,
                    seasonal_end_date,
                    rent_per,
                    unloading_charge,
                    remark
                )

                    values
                    ( 
                        p_inward_detail_id,
                            p_inward_id,
                            p_lot_no,
                            p_item,
                            p_gst_type,
                            p_marko,
                            p_packing_unit,
                            p_inward_qty,
                            p_inward_wt,
                            p_avg_wt_per_bag,
                            p_location,
                            p_moisture,
                            p_storage_duration,
                            p_rent_per_month,
                            p_rent_per_storage_duration,
                            p_seasonal_start_date,
                            p_seasonal_end_date,
                            p_rent_per,
                            p_unloading_charge,
                            p_remark
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_inward_detail
                
                SET
                        inward_id=COALESCE(p_inward_id,inward_id),
                        lot_no=COALESCE(p_lot_no,lot_no),
                        item=COALESCE(p_item,item),
                        gst_type=COALESCE(p_gst_type,gst_type),
                        marko=COALESCE(p_marko,marko),
                        packing_unit=COALESCE(p_packing_unit,packing_unit),
                        inward_qty=COALESCE(p_inward_qty,inward_qty),
                        inward_wt=COALESCE(p_inward_wt,inward_wt),
                        avg_wt_per_bag=COALESCE(p_avg_wt_per_bag,avg_wt_per_bag),
                        location=COALESCE(p_location,location),
                        moisture=COALESCE(p_moisture,moisture),
                        storage_duration=COALESCE(p_storage_duration,storage_duration),
                        rent_per_month=COALESCE(p_rent_per_month,rent_per_month),
                        rent_per_storage_duration=COALESCE(p_rent_per_storage_duration,rent_per_storage_duration),
                        seasonal_start_date=COALESCE(p_seasonal_start_date,seasonal_start_date),
                        seasonal_end_date=COALESCE(p_seasonal_end_date,seasonal_end_date),
                        rent_per=COALESCE(p_rent_per,rent_per),
                        unloading_charge=COALESCE(p_unloading_charge,unloading_charge),
                        remark=COALESCE(p_remark,remark)

                WHERE inward_detail_id= p_inward_detail_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_inward_detail WHERE inward_detail_id= p_inward_detail_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `inward_master_fillmodel` (IN `p_inward_id` BIGINT)   BEGIN
SELECT * 
       FROM `tbl_inward_master` 
        WHERE inward_id= p_inward_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `inward_master_transaction` (INOUT `p_inward_id` BIGINT, IN `p_inward_sequence` INT, IN `p_inward_no` VARCHAR(100), IN `p_inward_date` DATE, IN `p_customer` INT, IN `p_broker` INT, IN `p_billing_starts_from` DATE, IN `p_total_unloading_charge` DECIMAL(18,2), IN `p_sp_note` TEXT, IN `p_total_qty` INT, IN `p_total_wt` DECIMAL(18,2), IN `p_weigh_bridge_slip_no` VARCHAR(100), IN `p_gross_wt` DECIMAL(18,2), IN `p_tare_wt` DECIMAL(18,2), IN `p_net_wt` DECIMAL(18,2), IN `p_vehicle_no` VARCHAR(100), IN `p_driver_name` VARCHAR(100), IN `p_driver_mobile_no` VARCHAR(100), IN `p_transporter` VARCHAR(100), IN `p_other_expense1` VARCHAR(500), IN `p_other_expense2` VARCHAR(500), IN `p_created_by` INT, IN `p_created_date` DATETIME, IN `p_modified_by` INT, IN `p_modified_date` DATETIME, IN `p_company_id` INT, IN `p_company_year_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_inward_id = (SELECT COALESCE(MAX(inward_id),0) + 1 FROM tbl_inward_master);

                    insert into tbl_inward_master
                    (
                        inward_id,
                        inward_sequence,
                        inward_no,
                        inward_date,
                        customer,
                        broker,
                        billing_starts_from,
                        total_unloading_charge,
                        sp_note,
                        total_qty,
                        total_wt,
                        weigh_bridge_slip_no,
                        gross_wt,
                        tare_wt,
                        net_wt,
                        vehicle_no,
                        driver_name,
                        driver_mobile_no,
                        transporter,
                        other_expense1,
                        other_expense2,
                        created_by,
                        created_date,
                        modified_by,
                        modified_date,
                        company_id,
                        company_year_id
                    )
                    values
                    ( 
                        p_inward_id,
                        p_inward_sequence,
                        p_inward_no,
                        p_inward_date,
                        p_customer,
                        p_broker,
                        p_billing_starts_from,
                        p_total_unloading_charge,
                        p_sp_note,
                        p_total_qty,
                        p_total_wt,
                        p_weigh_bridge_slip_no,
                        p_gross_wt,
                        p_tare_wt,
                        p_net_wt,
                        p_vehicle_no,
                        p_driver_name,
                        p_driver_mobile_no,
                        p_transporter,
                        p_other_expense1,
                        p_other_expense2,
                        p_created_by,
                        p_created_date,
                        p_modified_by,
                        p_modified_date,
                        p_company_id,
                        p_company_year_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_inward_master
                
                SET
                  
                        inward_date=COALESCE(p_inward_date,inward_date),
                        customer=COALESCE(p_customer,customer),
                        broker=COALESCE(p_broker,broker),
                        billing_starts_from=COALESCE(p_billing_starts_from,billing_starts_from),
                        total_unloading_charge=COALESCE(p_total_unloading_charge,total_unloading_charge),
                        sp_note=COALESCE(p_sp_note,sp_note),
                        total_qty=COALESCE(p_total_qty,total_qty),
                        total_wt=COALESCE(p_total_wt,total_wt),
                        weigh_bridge_slip_no=COALESCE(p_weigh_bridge_slip_no,weigh_bridge_slip_no),
                        gross_wt=COALESCE(p_gross_wt,gross_wt),
                        tare_wt=COALESCE(p_tare_wt,tare_wt),
                        net_wt=COALESCE(p_net_wt,net_wt),
                        vehicle_no=COALESCE(p_vehicle_no,vehicle_no),
                        driver_name=COALESCE(p_driver_name,driver_name),
                        driver_mobile_no=COALESCE(p_driver_mobile_no,driver_mobile_no),
                        transporter=COALESCE(p_transporter,transporter),
                        other_expense1=COALESCE(p_other_expense1,other_expense1),
                        other_expense2=COALESCE(p_other_expense2,other_expense2),
                        created_by=COALESCE(p_created_by,created_by),
                        created_date=COALESCE(p_created_date,created_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        company_id=COALESCE(p_company_id,company_id),
                        company_year_id=COALESCE(p_company_year_id,company_year_id)

                WHERE inward_id= p_inward_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_inward_master WHERE inward_id= p_inward_id;
                ALTER TABLE tbl_inward_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_master_fillmodel` (IN `p_item_id` INT)   BEGIN
SELECT * 
       FROM `tbl_item_master` 
        WHERE item_id= p_item_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_master_transaction` (INOUT `p_item_id` INT, IN `p_item_gst` INT, IN `p_item_name` VARCHAR(100), IN `p_market_rate` DECIMAL, IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_item_id = (SELECT COALESCE(MAX(item_id),0) + 1 FROM tbl_item_master);

                    insert into tbl_item_master
                    (
                        item_id,
                        item_gst,
                        item_name,
                        market_rate,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_item_id,
                        p_item_gst,
                        p_item_name,
                        p_market_rate,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_item_master
                
                SET
                        item_gst=COALESCE(p_item_gst,item_gst),
                        item_name=COALESCE(p_item_name,item_name),
                        market_rate=COALESCE(p_market_rate,market_rate),
                        status=COALESCE(p_status,status),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE item_id= p_item_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_item_master WHERE item_id= p_item_id;
                ALTER TABLE tbl_item_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_preservation_price_list_detail_fillmodel` (IN `p_item_preservation_price_list_detail_id` INT)   BEGIN
SELECT * 
       FROM `tbl_item_preservation_price_list_detail` 
        WHERE item_preservation_price_list_detail_id= p_item_preservation_price_list_detail_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_preservation_price_list_detail_transaction` (INOUT `p_item_preservation_price_list_detail_id` INT, IN `p_item_preservation_price_list_id` INT, IN `p_packing_unit_id` INT, IN `p_rent_per_qty_month` DECIMAL(18,2), IN `p_rent_per_qty_season` DECIMAL(18,3), IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_item_preservation_price_list_detail_id = (SELECT COALESCE(MAX(item_preservation_price_list_detail_id),0) + 1 FROM tbl_item_preservation_price_list_detail);

                    insert into tbl_item_preservation_price_list_detail
                    (
                        item_preservation_price_list_detail_id,
                            item_preservation_price_list_id,
                            packing_unit_id,
                            rent_per_qty_month,
                            rent_per_qty_season
                    )
                    values
                    ( 
                        p_item_preservation_price_list_detail_id,
                            p_item_preservation_price_list_id,
                            p_packing_unit_id,
                            p_rent_per_qty_month,
                            p_rent_per_qty_season
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_item_preservation_price_list_detail
                
                SET
                        item_preservation_price_list_id=COALESCE(p_item_preservation_price_list_id,item_preservation_price_list_id),
                        packing_unit_id=COALESCE(p_packing_unit_id,packing_unit_id),
                        rent_per_qty_month=COALESCE(p_rent_per_qty_month,rent_per_qty_month),
                        rent_per_qty_season=COALESCE(p_rent_per_qty_season,rent_per_qty_season)

                WHERE item_preservation_price_list_detail_id= p_item_preservation_price_list_detail_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_item_preservation_price_list_detail WHERE item_preservation_price_list_detail_id= p_item_preservation_price_list_detail_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_preservation_price_list_master_fillmodel` (IN `p_item_preservation_price_list_id` INT)   BEGIN
SELECT * 
       FROM `tbl_item_preservation_price_list_master` 
        WHERE item_preservation_price_list_id= p_item_preservation_price_list_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_preservation_price_list_master_transaction` (INOUT `p_item_preservation_price_list_id` INT, IN `p_item_id` INT, IN `p_rent_per_kg_month` DECIMAL(18,2), IN `p_rent_per_kg_season` DECIMAL(18,3), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `p_company_year_id` INT, IN `TransactionMode` CHAR(1))   BEGIN

    IF TransactionMode = 'I' THEN

        SET p_item_preservation_price_list_id = (SELECT COALESCE(MAX(item_preservation_price_list_id),0) + 1 FROM tbl_item_preservation_price_list_master);

        INSERT INTO tbl_item_preservation_price_list_master
        (
            item_preservation_price_list_id,
            item_id,
            rent_per_kg_month,
            rent_per_kg_season,
            created_date,
            created_by,
            modified_date,
            modified_by,
            company_id,
            company_year_id         
        )
        VALUES
        ( 
            p_item_preservation_price_list_id,
            p_item_id,
            p_rent_per_kg_month,
            p_rent_per_kg_season,
            p_created_date,
            p_created_by,
            p_modified_date,
            p_modified_by,
            p_company_id,
            p_company_year_id     
        );

    ELSEIF TransactionMode = 'U' THEN

        UPDATE tbl_item_preservation_price_list_master

        SET
            item_id = COALESCE(p_item_id, item_id),
            rent_per_kg_month = COALESCE(p_rent_per_kg_month, rent_per_kg_month),
            rent_per_kg_season = COALESCE(p_rent_per_kg_season, rent_per_kg_season),
            created_date = COALESCE(p_created_date, created_date),
            created_by = COALESCE(p_created_by, created_by),
            modified_date = COALESCE(p_modified_date, modified_date),
            modified_by = COALESCE(p_modified_by, modified_by),
            company_id = COALESCE(p_company_id, company_id),
            company_year_id = COALESCE(p_company_year_id, company_year_id)   -- Added here

        WHERE item_preservation_price_list_id = p_item_preservation_price_list_id;

    ELSEIF TransactionMode = 'D' THEN

        DELETE FROM tbl_item_preservation_price_list_master WHERE item_preservation_price_list_id = p_item_preservation_price_list_id;
        ALTER TABLE tbl_item_preservation_price_list_master AUTO_INCREMENT = 1;

    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_master_fillmodel` (IN `p_menu_id` INT)   BEGIN
SELECT * 
       FROM `tbl_menu_master` 
        WHERE menu_id= p_menu_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_master_transaction` (INOUT `p_menu_id` INT, IN `p_module_id` INT, IN `p_menu_name` VARCHAR(100), IN `p_menu_text` VARCHAR(100), IN `p_menu_url` VARCHAR(255), IN `p_tab_index` INT, IN `p_is_display` BIT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_menu_id = (SELECT COALESCE(MAX(menu_id),0) + 1 FROM tbl_menu_master);

                    insert into tbl_menu_master
                    (
                        menu_id,
                        module_id,
                        menu_name,
                        menu_text,
                        menu_url,
                        tab_index,
                        is_display
                    )
                    values
                    ( 
                        p_menu_id,
                        p_module_id,
                        p_menu_name,
                        p_menu_text,
                        p_menu_url,
                        p_tab_index,
                        p_is_display
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_menu_master
                
                SET
                        module_id= p_module_id,
                        menu_name= p_menu_name,
                        menu_text= p_menu_text,
                        menu_url= p_menu_url,
                        tab_index= p_tab_index,
                        is_display= p_is_display

                WHERE menu_id= p_menu_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_menu_master WHERE menu_id= p_menu_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_right_master_fillmodel` (IN `p_menu_right_id` INT)   BEGIN
SELECT * 
       FROM `tbl_menu_right_master` 
        WHERE menu_right_id= p_menu_right_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_right_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_right_master_transaction` (INOUT `p_menu_right_id` INT, IN `p_menu_id` INT, IN `p_right_name` CHAR(1), IN `p_right_text` VARCHAR(10), IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_menu_right_id = (SELECT COALESCE(MAX(menu_right_id),0) + 1 FROM tbl_menu_right_master);

                    insert into tbl_menu_right_master
                    (
                        menu_right_id,
                        menu_id,
                        right_name,
                        right_text
                    )
                    values
                    ( 
                        p_menu_right_id,
                        p_menu_id,
                        p_right_name,
                        p_right_text
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_menu_right_master
                
                SET
                        menu_id= p_menu_id,
                        right_name= p_right_name,
                        right_text= p_right_text

                WHERE menu_right_id= p_menu_right_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_menu_right_master WHERE menu_right_id= p_menu_right_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_search_not_being_used` ()   BEGIN
SELECT 
    m.module_text, 
    mm.menu_text,   
    mm.menu_url AS menu_link,
    mm.menu_group
FROM tbl_menu_master mm
INNER JOIN tbl_module_master m ON mm.module_id = m.module_id
WHERE mm.is_display = 1
ORDER BY m.tab_index, mm.tab_index, mm.menu_group, mm.menu_text;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `module_master_fillmodel` (IN `p_module_id` INT)   BEGIN
SELECT * 
       FROM `tbl_module_master` 
        WHERE module_id= p_module_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `module_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `module_master_transaction` (INOUT `p_module_id` INT, IN `p_module_name` VARCHAR(100), IN `p_module_text` VARCHAR(100), IN `p_tab_index` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_module_id = (SELECT COALESCE(MAX(module_id),0) + 1 FROM tbl_module_master);

                    insert into tbl_module_master
                    (
                        module_id,
                        module_name,
                        module_text,
                        tab_index
                    )
                    values
                    ( 
                        p_module_id,
                        p_module_name,
                        p_module_text,
                        p_tab_index
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_module_master
                
                SET
                        module_name= p_module_name,
                        module_text= p_module_text,
                        tab_index= p_tab_index

                WHERE module_id= p_module_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_module_master WHERE module_id= p_module_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `outward_detail_fillmodel` (IN `p_outward_detail_id` BIGINT)   BEGIN
SELECT * 
       FROM `tbl_outward_detail` 
        WHERE outward_detail_id= p_outward_detail_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `outward_detail_transaction` (INOUT `p_outward_detail_id` BIGINT, IN `p_outward_id` BIGINT, IN `p_inward_detail_id` BIGINT, IN `p_out_qty` DECIMAL, IN `p_out_wt` DECIMAL, IN `p_loading_charges` DECIMAL, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_outward_detail_id = (SELECT COALESCE(MAX(outward_detail_id),0) + 1 FROM tbl_outward_detail);

                    insert into tbl_outward_detail
                    (
                        outward_detail_id,
                            outward_id,
                            inward_detail_id,
                            out_qty,
                            out_wt,
                            loading_charges
                    )
                    values
                    ( 
                        p_outward_detail_id,
                            p_outward_id,
                            p_inward_detail_id,
                            p_out_qty,
                            p_out_wt,
                            p_loading_charges
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_outward_detail
                
                SET
                        outward_id=COALESCE(p_outward_id,outward_id),
                        inward_detail_id=COALESCE(p_inward_detail_id,inward_detail_id),
                        out_qty=COALESCE(p_out_qty,out_qty),
                        out_wt=COALESCE(p_out_wt,out_wt),
                        loading_charges=COALESCE(p_loading_charges,loading_charges)

                WHERE outward_detail_id= p_outward_detail_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_outward_detail WHERE outward_detail_id= p_outward_detail_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `outward_master_fillmodel` (IN `p_outward_id` BIGINT)   BEGIN
SELECT * 
       FROM `tbl_outward_master` 
        WHERE outward_id= p_outward_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `outward_master_transaction` (INOUT `p_outward_id` BIGINT, IN `p_outward_sequence` INT, IN `p_outward_no` VARCHAR(100), IN `p_outward_date` DATE, IN `p_customer` INT, IN `p_total_qty` INT, IN `p_total_wt` DECIMAL, IN `p_gross_wt` DECIMAL, IN `p_tare_wt` DECIMAL, IN `p_net_wt` DECIMAL, IN `p_loading_expense` DECIMAL, IN `p_other_expense1` DECIMAL, IN `p_other_expense2` DECIMAL, IN `p_outward_order_by` INT, IN `p_delivery_to` VARCHAR(100), IN `p_vehicle_no` VARCHAR(500), IN `p_driver_name` VARCHAR(500), IN `p_driver_mob_no` VARCHAR(500), IN `p_transporter` VARCHAR(500), IN `p_sp_note` VARCHAR(8000), IN `p_created_by` INT, IN `p_created_date` DATETIME, IN `p_modified_by` INT, IN `p_modified_date` DATETIME, IN `p_company_id` INT, IN `p_company_year_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_outward_id = (SELECT COALESCE(MAX(outward_id),0) + 1 FROM tbl_outward_master);

                    insert into tbl_outward_master
                    (
                        outward_id,
                        outward_sequence,
                        outward_no,
                        outward_date,
                        customer,
                        total_qty,
                        total_wt,
                        gross_wt,
                        tare_wt,
                        net_wt,
                        loading_expense,
                        other_expense1,
                        other_expense2,
                        outward_order_by,
                        delivery_to,
                        vehicle_no,
                        driver_name,
                        driver_mob_no,
                        transporter,
                        sp_note,
                        created_by,
                        created_date,
                        modified_by,
                        modified_date,
                        company_id,
                        company_year_id
                    )
                    values
                    ( 
                        p_outward_id,
                        p_outward_sequence,
                        p_outward_no,
                        p_outward_date,
                        p_customer,
                        p_total_qty,
                        p_total_wt,
                        p_gross_wt,
                        p_tare_wt,
                        p_net_wt,
                        p_loading_expense,
                        p_other_expense1,
                        p_other_expense2,
                        p_outward_order_by,
                        p_delivery_to,
                        p_vehicle_no,
                        p_driver_name,
                        p_driver_mob_no,
                        p_transporter,
                        p_sp_note,
                        p_created_by,
                        p_created_date,
                        p_modified_by,
                        p_modified_date,
                        p_company_id,
                        p_company_year_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_outward_master
                
                SET
                        outward_date=COALESCE(p_outward_date,outward_date),
                        customer=COALESCE(p_customer,customer),
                        total_qty=COALESCE(p_total_qty,total_qty),
                        total_wt=COALESCE(p_total_wt,total_wt),
                        gross_wt=COALESCE(p_gross_wt,gross_wt),
                        tare_wt=COALESCE(p_tare_wt,tare_wt),
                        net_wt=COALESCE(p_net_wt,net_wt),
                        loading_expense=COALESCE(p_loading_expense,loading_expense),
                        other_expense1=COALESCE(p_other_expense1,other_expense1),
                        other_expense2=COALESCE(p_other_expense2,other_expense2),
                        outward_order_by=COALESCE(p_outward_order_by,outward_order_by),
                        delivery_to=COALESCE(p_delivery_to,delivery_to),
                        vehicle_no=COALESCE(p_vehicle_no,vehicle_no),
                        driver_name=COALESCE(p_driver_name,driver_name),
                        driver_mob_no=COALESCE(p_driver_mob_no,driver_mob_no),
                        transporter=COALESCE(p_transporter,transporter),
                        sp_note=COALESCE(p_sp_note,sp_note),
                        created_by=COALESCE(p_created_by,created_by),
                        created_date=COALESCE(p_created_date,created_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        company_id=COALESCE(p_company_id,company_id),
                        company_year_id=COALESCE(p_company_year_id,company_year_id)

                WHERE outward_id= p_outward_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_outward_master WHERE outward_id= p_outward_id;
                ALTER TABLE tbl_outward_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `packing_unit_master_fillmodel` (IN `p_packing_unit_id` INT)   BEGIN
SELECT * 
       FROM `tbl_packing_unit_master` 
        WHERE packing_unit_id= p_packing_unit_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `packing_unit_master_transaction` (INOUT `p_packing_unit_id` INT, IN `p_packing_unit_name` VARCHAR(100), IN `p_conversion_factor` DECIMAL, IN `p_unloading_charge` DECIMAL, IN `p_loading_charge` DECIMAL, IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_packing_unit_id = (SELECT COALESCE(MAX(packing_unit_id),0) + 1 FROM tbl_packing_unit_master);

                    insert into tbl_packing_unit_master
                    (
                        packing_unit_id,
                        packing_unit_name,
                        conversion_factor,
                        unloading_charge,
                        loading_charge,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_packing_unit_id,
                        p_packing_unit_name,
                        p_conversion_factor,
                        p_unloading_charge,
                        p_loading_charge,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_packing_unit_master
                
                SET
                        packing_unit_name=COALESCE(p_packing_unit_name,packing_unit_name),
                        conversion_factor=COALESCE(p_conversion_factor,conversion_factor),
                        unloading_charge=COALESCE(p_unloading_charge,unloading_charge),
                        loading_charge=COALESCE(p_loading_charge,loading_charge),
                        status=COALESCE(p_status,status),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE packing_unit_id= p_packing_unit_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_packing_unit_master WHERE packing_unit_id= p_packing_unit_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `rent_invoice_detail_fillmodel` (IN `p_rent_invoice_detail_id` INT)   BEGIN
SELECT * 
       FROM `tbl_rent_invoice_detail` 
        WHERE rent_invoice_detail_id= p_rent_invoice_detail_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `rent_invoice_detail_transaction` (INOUT `p_rent_invoice_detail_id` INT, IN `p_rent_invoice_id` INT, IN `p_description` VARCHAR(500), IN `p_qty` INT, IN `p_unit` VARCHAR(500), IN `p_weight` DECIMAL(18,2), IN `p_rate_per_unit` DECIMAL(18,2), IN `p_amount` INT, IN `p_remark` VARCHAR(1000), IN `p_inward_no` INT(1), IN `p_inward_date` DATE, IN `p_lot_no` INT, IN `p_item` INT, IN `p_marko` INT, IN `p_invoice_qty` INT, IN `p_unit_name` INT, IN `p_wt_per_kg` INT, IN `p_storage_duration` INT, IN `p_rent_per_storage_duration` INT, IN `p_rent_per` INT, IN `p_outward_date` DATE, IN `p_charges_from` DATE, IN `p_charges_to` DATE, IN `p_actual_month` INT, IN `p_actual_day` INT, IN `p_invoice_month` INT, IN `p_invoice_day` INT, IN `p_invoice_amount` INT, IN `p_invoice_for` VARCHAR(255), IN `TransactionMode` CHAR(1))   BEGIN
    IF TransactionMode = 'I' THEN
        SET p_rent_invoice_detail_id = (SELECT COALESCE(MAX(rent_invoice_detail_id),0) + 1 FROM tbl_rent_invoice_detail);

        INSERT INTO tbl_rent_invoice_detail (
            rent_invoice_detail_id,
            rent_invoice_id,
            description,
            qty,
            unit,
            weight,
            rate_per_unit,
            amount,
            remark,
            inward_no,
            inward_date,
            lot_no,
            item,
            marko,
            invoice_qty,
            unit_name,
            wt_per_kg,
            storage_duration,
            rent_per_storage_duration,
            rent_per,
            outward_date,
            charges_from,
            charges_to,
            actual_month,
            actual_day,
            invoice_month,
            invoice_day,
            invoice_amount,
            invoice_for
        )
        VALUES ( 
            p_rent_invoice_detail_id,
            p_rent_invoice_id,
            p_description,
            p_qty,
            p_unit,
            p_weight,
            p_rate_per_unit,
            p_amount,
            p_remark,
            p_inward_no,
            p_inward_date,
            p_lot_no,
            p_item,
            p_marko,
            p_invoice_qty,
            p_unit_name,
            p_wt_per_kg,
            p_storage_duration,
            p_rent_per_storage_duration,
            p_rent_per,
            p_outward_date,
            p_charges_from,
            p_charges_to,
            p_actual_month,
            p_actual_day,
            p_invoice_month,
            p_invoice_day,
            p_invoice_amount,
            p_invoice_for
        );
                
    ELSEIF TransactionMode = 'U' THEN
        UPDATE tbl_rent_invoice_detail
        SET
            rent_invoice_id=COALESCE(p_rent_invoice_id,rent_invoice_id),
            description=COALESCE(p_description,description),
            qty=COALESCE(p_qty,qty),
            unit=COALESCE(p_unit,unit),
            weight=COALESCE(p_weight,weight),
            rate_per_unit=COALESCE(p_rate_per_unit,rate_per_unit),
            amount=COALESCE(p_amount,amount),
            remark=COALESCE(p_remark,remark),
            inward_no=COALESCE(p_inward_no,inward_no),
            inward_date=COALESCE(p_inward_date,inward_date),
            lot_no=COALESCE(p_lot_no,lot_no),
            item=COALESCE(p_item,item),
            marko=COALESCE(p_marko,marko),
            invoice_qty=COALESCE(p_invoice_qty,invoice_qty),
            unit_name=COALESCE(p_unit_name,unit_name),
            wt_per_kg=COALESCE(p_wt_per_kg,wt_per_kg),
            storage_duration=COALESCE(p_storage_duration,storage_duration),
            rent_per_storage_duration=COALESCE(p_rent_per_storage_duration,rent_per_storage_duration),
            rent_per=COALESCE(p_rent_per,rent_per),
            outward_date=COALESCE(p_outward_date,outward_date),
            charges_from=COALESCE(p_charges_from,charges_from),
            charges_to=COALESCE(p_charges_to,charges_to),
            actual_month=COALESCE(p_actual_month,actual_month),
            actual_day=COALESCE(p_actual_day,actual_day),
            invoice_month=COALESCE(p_invoice_month,invoice_month),
            invoice_day=COALESCE(p_invoice_day,invoice_day),
            invoice_amount=COALESCE(p_invoice_amount,invoice_amount),
            invoice_for=COALESCE(p_invoice_for,invoice_for)
        WHERE rent_invoice_detail_id = p_rent_invoice_detail_id;
  
    ELSEIF TransactionMode = 'D' THEN
        DELETE FROM tbl_rent_invoice_detail WHERE rent_invoice_detail_id = p_rent_invoice_detail_id;
            
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `rent_invoice_master_fillmodel` (IN `p_rent_invoice_id` BIGINT)   BEGIN
SELECT * 
       FROM `tbl_rent_invoice_master` 
        WHERE rent_invoice_id= p_rent_invoice_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `rent_invoice_master_transaction` (INOUT `p_rent_invoice_id` BIGINT, IN `p_invoice_type` INT, IN `p_rent_invoice_sequence` INT, IN `p_invoice_no` VARCHAR(100), IN `p_invoice_date` DATE, IN `p_billing_till_date` DATE, IN `p_debit_cash` INT, IN `p_customer` INT, IN `p_invoice_for` INT, IN `p_hsn_code` INT, IN `p_grace_days` INT, IN `p_lot_no` VARCHAR(100), IN `p_basic_amount` DECIMAL(18,2), IN `p_unloading_exp` DECIMAL(18,2), IN `p_loading_exp` DECIMAL(18,2), IN `p_other_expense3` DECIMAL(18,2), IN `p_tax_amount` INT, IN `p_sgst` INT, IN `p_cgst` INT, IN `p_igst` INT, IN `p_net_amount` DECIMAL(10,0), IN `p_sp_note` VARCHAR(8000), IN `p_created_by` INT, IN `p_created_date` DATETIME, IN `p_modified_by` INT, IN `p_modified_date` DATETIME, IN `p_company_id` INT, IN `p_company_year_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_rent_invoice_id = (SELECT COALESCE(MAX(rent_invoice_id),0) + 1 FROM tbl_rent_invoice_master);

                    insert into tbl_rent_invoice_master
                    (
                        rent_invoice_id,
                        invoice_type,
                        rent_invoice_sequence,
                        invoice_no,
                        invoice_date,
                        billing_till_date,
                        debit_cash,
                        customer,
                        invoice_for,
                        hsn_code,
                        grace_days,
                        lot_no,
                        basic_amount,
                        unloading_exp,
                        loading_exp,
                        other_expense3,
                        tax_amount,
                        sgst,
                        cgst,
                        igst,
                        net_amount,
                        sp_note,
                        created_by,
                        created_date,
                        modified_by,
                        modified_date,
                        company_id,
                        company_year_id
                    )
                    values
                    ( 
                        p_rent_invoice_id,
                        p_invoice_type,
                        p_rent_invoice_sequence,
                        p_invoice_no,
                        p_invoice_date,
                        p_billing_till_date,
                        p_debit_cash,
                        p_customer,
                        p_invoice_for,
                        p_hsn_code,
                        p_grace_days,
                        p_lot_no,
                        p_basic_amount,
                        p_unloading_exp,
                        p_loading_exp,
                        p_other_expense3,
                        p_tax_amount,
                        p_sgst,
                        p_cgst,
                        p_igst,
                        p_net_amount,
                        p_sp_note,
                        p_created_by,
                        p_created_date,
                        p_modified_by,
                        p_modified_date,
                        p_company_id,
                        p_company_year_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_rent_invoice_master
                
                SET
                        invoice_type=COALESCE(p_invoice_type,invoice_type),
                        rent_invoice_sequence=COALESCE(p_rent_invoice_sequence,rent_invoice_sequence),
                        invoice_no=COALESCE(p_invoice_no,invoice_no),
                        invoice_date=COALESCE(p_invoice_date,invoice_date),
                        billing_till_date=COALESCE(p_billing_till_date,billing_till_date),
                        debit_cash=COALESCE(p_debit_cash,debit_cash),
                        customer=COALESCE(p_customer,customer),
                        invoice_for=COALESCE(p_invoice_for,invoice_for),
                        hsn_code=COALESCE(p_hsn_code,hsn_code),
                        grace_days=COALESCE(p_grace_days,grace_days),
                        lot_no=COALESCE(p_lot_no,lot_no),
                        basic_amount=COALESCE(p_basic_amount,basic_amount),
                        unloading_exp=COALESCE(p_unloading_exp,unloading_exp),
                        loading_exp=COALESCE(p_loading_exp,loading_exp),
                        other_expense3=COALESCE(p_other_expense3,other_expense3),
                        tax_amount=COALESCE(p_tax_amount,tax_amount),
                        sgst=COALESCE(p_sgst,sgst),
                        cgst=COALESCE(p_cgst,cgst),
                        igst=COALESCE(p_igst,igst),
                        net_amount=COALESCE(p_net_amount,net_amount),
                        sp_note=COALESCE(p_sp_note,sp_note),
                        created_by=COALESCE(p_created_by,created_by),
                        created_date=COALESCE(p_created_date,created_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        company_id=COALESCE(p_company_id,company_id),
                        company_year_id=COALESCE(p_company_year_id,company_year_id)

                WHERE rent_invoice_id= p_rent_invoice_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_rent_invoice_master WHERE rent_invoice_id= p_rent_invoice_id;
                ALTER TABLE tbl_rent_invoice_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `state_master_fillmodel` (IN `p_state_id` INT)   BEGIN
SELECT * 
       FROM `tbl_state_master` 
        WHERE state_id= p_state_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `state_master_transaction` (INOUT `p_state_id` INT, IN `p_state_name` VARCHAR(100), IN `p_country_id` INT, IN `p_gst_code` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_state_id = (SELECT COALESCE(MAX(state_id),0) + 1 FROM tbl_state_master);

                    insert into tbl_state_master
                    (
                        state_id,
                        state_name,
                        country_id,
                        gst_code,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_state_id,
                        p_state_name,
                        p_country_id,
                        p_gst_code,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_state_master
                
                SET
                        state_name=COALESCE(p_state_name,state_name),
                        country_id=COALESCE(p_country_id,country_id),
                        gst_code=COALESCE(p_gst_code,gst_code),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE state_id= p_state_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_state_master WHERE state_id= p_state_id;
                ALTER TABLE tbl_state_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_login_not_being_used` (IN `p_login_id` VARCHAR(100), IN `p_login_pass` VARCHAR(100))   BEGIN
    SELECT user_id, person_name 
    FROM tbl_user_master 
    WHERE login_id = p_login_id 
    AND login_pass = p_login_pass 
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_master_fillmodel` (IN `p_user_id` INT)   BEGIN
SELECT * 
       FROM `tbl_user_master` 
        WHERE user_id= p_user_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_master_transaction` (INOUT `p_user_id` INT, IN `p_login_id` VARCHAR(100), IN `p_login_pass` VARCHAR(100), IN `p_person_name` VARCHAR(100), IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `p_company_id` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_user_id = (SELECT COALESCE(MAX(user_id),0) + 1 FROM tbl_user_master);

                    insert into tbl_user_master
                    (
                        user_id,
                        login_id,
                        login_pass,
                        person_name,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by,
                        company_id
                    )
                    values
                    ( 
                        p_user_id,
                        p_login_id,
                        p_login_pass,
                        p_person_name,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by,
                        p_company_id
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_user_master
                
                SET
                        login_id=COALESCE(p_login_id,login_id),
                        login_pass=COALESCE(p_login_pass,login_pass),
                        person_name=COALESCE(p_person_name,person_name),
                        status=COALESCE(p_status,status),
                        created_date=COALESCE(p_created_date,created_date),
                        created_by=COALESCE(p_created_by,created_by),
                        modified_date=COALESCE(p_modified_date,modified_date),
                        modified_by=COALESCE(p_modified_by,modified_by),
                        company_id=COALESCE(p_company_id,company_id)

                WHERE user_id= p_user_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_user_master WHERE user_id= p_user_id;
                ALTER TABLE tbl_user_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_right_master_fillmodel` (IN `p_user_right_master_id` BIGINT)   BEGIN
SELECT * 
       FROM `tbl_user_right_master` 
        WHERE user_right_master_id= p_user_right_master_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_right_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_right_master_transaction` (INOUT `p_user_right_master_id` BIGINT, IN `p_user_id` INT, IN `p_menu_right_id` INT, IN `p_has_right` BIT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_user_right_master_id = (SELECT COALESCE(MAX(user_right_master_id),0) + 1 FROM tbl_user_right_master);

                    insert into tbl_user_right_master
                    (
                        user_right_master_id,
                        user_id,
                        menu_right_id,
                        has_right
                    )
                    values
                    ( 
                        p_user_right_master_id,
                        p_user_id,
                        p_menu_right_id,
                        p_has_right
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_user_right_master
                
                SET
                        user_id=COALESCE(p_user_id,user_id),
                        menu_right_id=COALESCE(p_menu_right_id,menu_right_id),
                        has_right=COALESCE(p_has_right,has_right)

                WHERE user_right_master_id= p_user_right_master_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_user_right_master WHERE user_right_master_id= p_user_right_master_id;
                ALTER TABLE tbl_user_right_master AUTO_INCREMENT = 1;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `view_gsttaxdetail_tax_type_fillmodel` (IN `p_id` INT)   BEGIN
SELECT * 
       FROM `view_gsttaxdetail_tax_type` 
        WHERE id= p_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `view_gsttaxdetail_tax_type_transaction` (INOUT `p_id` INT, IN `p_value` VARCHAR(4), IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_id = (SELECT COALESCE(MAX(id),0) + 1 FROM view_gsttaxdetail_tax_type);

                    insert into view_gsttaxdetail_tax_type
                    (
                        id,
                            value
                    )
                    values
                    ( 
                        p_id,
                            p_value
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE view_gsttaxdetail_tax_type
                
                SET
                        value= p_value

                WHERE id= p_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  view_gsttaxdetail_tax_type WHERE id= p_id;
            
            END IF;
        END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bank_master`
--

CREATE TABLE `tbl_bank_master` (
  `bank_id` int(11) UNSIGNED NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `account_no` varchar(100) DEFAULT NULL,
  `ifs_code` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_bank_master`
--

INSERT INTO `tbl_bank_master` (`bank_id`, `bank_name`, `branch_name`, `account_no`, `ifs_code`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(1, 'HDFCRb', 'Rajkot', '789545623154', '45D8741J42', NULL, '2025-04-05 12:23:41', 1, '2025-04-08 11:32:05', 1, NULL),
(2, 'HDFC', 'Andheri West', '00012366', 'XXXXX1234', 1, '2025-04-08 15:29:16', 1, '2025-06-19 15:59:06', 1, 2),
(3, 'Bank Of Baroda', 'Rajkot', '00012366', 'XXXXX66', 1, '2025-04-23 12:37:40', NULL, '2025-04-23 12:37:40', NULL, 2),
(4, 'ICICI', 'Andheri West2', '00012366', 'XXXXX12345', 1, '2025-04-23 12:38:15', NULL, '2025-06-19 15:59:12', 1, 1),
(5, 'ICICII', 'Andheri West2', '00012369', 'XXXXX12347', 1, '2025-06-18 17:29:46', 1, '2025-06-18 17:29:46', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_chamber_master`
--

CREATE TABLE `tbl_chamber_master` (
  `chamber_id` int(11) UNSIGNED NOT NULL,
  `chamber_name` varchar(100) DEFAULT NULL,
  `created_date` date NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` date NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_chamber_master`
--

INSERT INTO `tbl_chamber_master` (`chamber_id`, `chamber_name`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(1, 'Medical', '2025-04-23', 1, '2025-04-23', 1, 2),
(2, 'fruits', '2025-04-23', 1, '2025-04-23', 1, 2),
(3, 'Medical2', '2025-04-23', 1, '2025-04-28', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_city_master`
--

CREATE TABLE `tbl_city_master` (
  `city_id` int(11) UNSIGNED NOT NULL,
  `city_name` varchar(100) DEFAULT NULL,
  `state_id` int(11) UNSIGNED DEFAULT NULL,
  `country_id` int(11) UNSIGNED DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_city_master`
--

INSERT INTO `tbl_city_master` (`city_id`, `city_name`, `state_id`, `country_id`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(1, 'rajkot', 4, 1, '2025-04-22 15:14:32', 1, '2025-04-23 11:00:48', 1, 2),
(8, 'DHRANGADHRA', 2, 3, '2025-04-22 16:45:16', 1, '2025-06-23 16:47:34', 1, 1),
(12, 'New city', 8, 1, '2025-05-21 16:19:59', NULL, '2025-06-16 14:22:31', NULL, 1),
(13, 'ahemdabad', 8, 1, '2025-05-24 11:45:52', 1, '2025-06-16 14:21:18', 1, 1),
(14, 'Rajkot32', 35, 13, '2025-06-23 16:02:45', 1, '2025-06-23 16:02:45', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_company_master`
--

CREATE TABLE `tbl_company_master` (
  `company_id` int(11) UNSIGNED NOT NULL,
  `company_name` varchar(500) DEFAULT NULL,
  `company_code` varchar(100) DEFAULT NULL,
  `company_logo` blob DEFAULT NULL,
  `company_logo_url` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `pincode` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `web_address` varchar(100) DEFAULT NULL,
  `gstin` varchar(50) DEFAULT NULL,
  `bank_id` int(11) UNSIGNED DEFAULT NULL,
  `jurisdiction` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_company_master`
--

INSERT INTO `tbl_company_master` (`company_id`, `company_name`, `company_code`, `company_logo`, `company_logo_url`, `address`, `city`, `pincode`, `state`, `phone`, `email`, `web_address`, `gstin`, `bank_id`, `jurisdiction`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'CBS Software Solutions', '1224', 0x6362735f6c6f676f5f31332e706e67, 'uploads/company_master/cbs_logo_13.png', 'no', 'Rajkot', '360002', 'Gujarat', '56815484154', 'Nisha1@nisha.com', 'CbssoftwareSolution.com', 'COM2632854', 5, 'asd', '2025-06-19 15:33:36', 1, '2025-06-26 15:24:26', 1),
(2, 'NRI Gujarati', '185', 0x6c6f676f2e706e67, '/uploads//company_master/logo.png', 'University Road', 'rjk', '4324', 'Gujarat', '54846', 'NriGuj@gmail.com', 'NRIGujarati.com', '6576', 2, 'asd', '2025-04-21 11:16:24', 1, '2025-06-19 16:00:10', 1),
(3, 'SoftTech', '58658', 0x3132335f312e706e67, 'uploads/company_master/123_1.png', 'Kotecha Chowk', 'Rajkot', '360002', 'Gujarat', '09427562269', 'Nisha1@nisha.com', 'www.com', 'COM2632854', 5, NULL, '2025-06-19 15:33:36', 1, '2025-06-19 16:00:41', 1),
(4, 'EverGreen', 'no', 0x747265652e706e67, 'uploads/company_master/tree.png', 'no', 'Rajkot', '360002', 'Gujarat', '09427562269', 'Nisha1@nisha.com', 'no', 'LOGO5244145', 5, NULL, '2025-06-19 15:57:50', 1, '2025-06-19 16:00:09', 1),
(5, 'RedLine', '4893789', 0x7265642e706e67, 'uploads/company_master/red.png', 'RedLine', 'Rajkot', '360002', 'Gujarat', '09427562269', 'Nisha@nisha.com', 'RedLine.com', '8273189HJSND', 5, NULL, '2025-06-19 16:03:21', 1, '2025-06-19 16:04:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_company_year_master`
--

CREATE TABLE `tbl_company_year_master` (
  `company_year_id` int(11) UNSIGNED NOT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `company_year_type` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_company_year_master`
--

INSERT INTO `tbl_company_year_master` (`company_year_id`, `company_id`, `company_year_type`, `start_date`, `end_date`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(16, 2, '1', '2026-04-01', '2027-03-31', '2025-05-10 11:02:20', 1, '2025-05-10 11:02:20', 1),
(17, 2, '2', '2024-04-01', '2025-03-31', '2025-05-10 11:02:24', 1, '2025-05-10 11:02:24', 1),
(22, 2, NULL, '2025-04-01', '2026-03-31', '2025-05-10 12:05:51', NULL, '2025-05-10 12:05:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_contact_person_detail`
--

CREATE TABLE `tbl_contact_person_detail` (
  `contact_person_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `mobile` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `is_whatsapp` tinyint(4) NOT NULL,
  `is_email` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_contact_person_detail`
--

INSERT INTO `tbl_contact_person_detail` (`contact_person_id`, `customer_id`, `contact_person_name`, `mobile`, `email`, `is_whatsapp`, `is_email`) VALUES
(1, 1, 'manuu', '13434', 'khushbudetroja2002@gmail.com', 1, 1),
(2, 2, 'mansi', '13434', 'khushbudetroja2002@gmail.com', 1, 1),
(3, 5, 'aa', '2434343345', 'hetanshreemehta@gmail.com', 1, 0),
(4, 4, 'ggg', '2434343345', 'hetanshreemehta@gmail.com', 1, 0),
(5, 1, 'heudii', '8795545121', 'hetanshreemehta@gmail.com', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_country_master`
--

CREATE TABLE `tbl_country_master` (
  `country_id` int(11) UNSIGNED NOT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_country_master`
--

INSERT INTO `tbl_country_master` (`country_id`, `country_name`, `company_id`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'India', 1, '2025-04-05 11:20:32', 1, '2025-04-22 11:31:56', 1),
(3, 'France', 1, '2025-04-10 15:07:37', 1, '2025-06-12 11:36:28', 1),
(4, 'brazile', 2, '2025-04-11 12:48:12', 1, '2025-04-22 11:31:46', 1),
(5, 'indiaaa', 1, '2025-06-23 09:30:31', 1, '2025-06-23 09:30:31', 1),
(6, 'Tonga', 1, '2025-06-23 09:35:46', 1, '2025-06-23 09:35:46', 1),
(9, 'switzerland1', 1, '2025-06-23 09:56:00', 1, '2025-06-23 09:56:00', 1),
(10, 'switzerland1', 1, '2025-06-23 15:30:17', 1, '2025-06-23 15:30:17', 1),
(11, 'aaa', 1, '2025-06-23 15:49:52', 1, '2025-06-23 15:49:52', 1),
(12, 'aaa', 1, '2025-06-23 15:50:38', 1, '2025-06-23 15:50:38', 1),
(13, 'switzerland1', 1, '2025-06-23 16:01:06', 1, '2025-06-23 16:01:06', 1),
(14, 'indiaaa', 1, '2025-06-23 17:22:52', 1, '2025-06-23 17:22:52', 1),
(15, 'switzerland1', 1, '2025-06-23 17:22:52', 1, '2025-06-23 17:22:52', 1),
(16, 'Tonga', 1, '2025-06-23 17:23:50', 1, '2025-06-23 17:23:50', 1),
(17, 'Tonga3', 1, '2025-06-23 17:29:32', 1, '2025-06-23 17:29:32', 1),
(18, 'Tonga11', 1, '2025-06-23 17:33:20', 1, '2025-06-23 17:33:20', 1),
(19, 'India1', 1, '2025-06-23 17:40:36', 1, '2025-06-23 17:40:36', 1),
(20, 'Tonga18', 1, '2025-06-23 17:49:36', 1, '2025-06-23 17:49:36', 1),
(21, 'test', 1, '2025-06-24 10:29:40', 1, '2025-06-24 10:29:40', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_currency_master`
--

CREATE TABLE `tbl_currency_master` (
  `currency_id` int(11) UNSIGNED NOT NULL,
  `currency_symbol` varchar(100) DEFAULT NULL,
  `currency_name` varchar(100) DEFAULT NULL,
  `currency_in_paise` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_currency_master`
--

INSERT INTO `tbl_currency_master` (`currency_id`, `currency_symbol`, `currency_name`, `currency_in_paise`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(1, '₹', 'Indian Rupee', '1 Rupee = 100 Paise', '2025-04-05 15:04:46', 1, '2025-04-05 15:04:46', 1, NULL),
(2, '$', 'US Dollar', 'No Paise', '2025-04-05 15:16:27', 1, '2025-04-05 15:16:27', 1, NULL),
(3, '€', 'Euroo', '€No Paise', '2025-04-05 15:17:32', 1, '2025-06-13 10:28:13', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_account_group_master`
--

CREATE TABLE `tbl_customer_account_group_master` (
  `customer_account_group_id` int(11) UNSIGNED NOT NULL,
  `customer_account_group_name` varchar(100) DEFAULT NULL,
  `under_group` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_account_group_master`
--

INSERT INTO `tbl_customer_account_group_master` (`customer_account_group_id`, `customer_account_group_name`, `under_group`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(1, '1000 (Assets)', 'G:\\xampp\\htdocs\\csms1\\classes/../uploads/customer_account_group_master/purchase_order_summary__1_.cs', '2025-04-05 15:54:52', 1, '2025-04-05 15:54:52', 1, NULL),
(2, '10022(Assets)', '/uploads//customer_account_group_master/purchase_order_summary__1_.csv', '2025-04-08 15:43:55', 1, '2025-04-08 15:43:55', 1, NULL),
(3, '100(Assets)', '/uploads//customer_account_group_master/annual-enterprise-survey-2023-financial-year-provisional.csv', '2025-04-23 12:15:03', NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_master`
--

CREATE TABLE `tbl_customer_master` (
  `customer_id` int(11) UNSIGNED NOT NULL,
  `customer` varchar(100) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_type` int(11) DEFAULT NULL,
  `account_group_id` int(11) UNSIGNED DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city_id` int(11) UNSIGNED DEFAULT NULL,
  `pincode` varchar(100) DEFAULT NULL,
  `state_id` int(11) UNSIGNED DEFAULT NULL,
  `country_id` int(11) UNSIGNED DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email_id` varchar(100) DEFAULT NULL,
  `web_address` varchar(100) DEFAULT NULL,
  `gstin` varchar(100) DEFAULT NULL,
  `pan` varchar(100) DEFAULT NULL,
  `aadhar_no` varchar(100) DEFAULT NULL,
  `mandli_license_no` varchar(100) DEFAULT NULL,
  `fssai_license_no` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `company_year_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_master`
--

INSERT INTO `tbl_customer_master` (`customer_id`, `customer`, `customer_name`, `customer_type`, `account_group_id`, `address`, `city_id`, `pincode`, `state_id`, `country_id`, `phone`, `email_id`, `web_address`, `gstin`, `pan`, `aadhar_no`, `mandli_license_no`, `fssai_license_no`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`, `company_year_id`) VALUES
(1, 'cold storage', 'mansi', 1, 1, 'dcf', 1, '324443', 4, 1, '6789067890', 'khushbudetroja2002@gmail.com', 'dcf', '22ABCDE1234F1Z5', '1234567890', '12324356', '2354655', '54656557676', 1, '2025-05-13 15:35:06', NULL, '2025-06-26 12:31:47', 1, 1, NULL),
(2, 'hetu', 'hetu', 1, 2, 'dcf', 8, '324443', 2, 4, '6789067890', 'khushbudetroja2002@gmail.com', 'dcf', '22ABCDE1234F1Z5', '1234567890', '12324356', '2354655', '54656557676', 1, '2025-05-13 15:39:12', NULL, '2025-06-26 12:08:34', 1, 1, NULL),
(3, 'mannsi', 'mannsi', 2, 3, 'vishvnagar 10,maruti nanadan near', 8, '360004', 2, 4, '9374638393', 'manas@gmail.com', 'vishvnagar 10,maruti nanadan near', '767687897898787', '2342232323', '3213213231', '3231312321321', '3232132', 1, '2025-05-22 14:31:10', 1, '2025-05-26 09:47:12', 1, 1, NULL),
(4, 'drashti chavda', 'drashti chavda', 1, 1, 'dcf', 12, '123456', 8, 1, '6789067890', 'khushbudetroja2002@gmail.com', 'dcf', '22ABCDE1234F1Z5', '1234567890', NULL, NULL, '54656557676', 1, '2025-05-24 11:40:13', 1, '2025-06-26 10:00:09', 1, 1, NULL),
(5, 'hetanshree', 'hetansharee', 2, 1, 'dcf', 12, '123456', 4, 1, '6789067890', 'khushbudetroja2002@gmail.com', 'dcf', '22ABCDE1234F1Z5', '1234567890', NULL, NULL, '54656557676', 1, '2025-05-24 11:59:45', 1, '2025-05-26 15:36:08', 1, 1, NULL),
(6, 'xyz', 'xyz', 1, 2, 'dcf', 12, '324443', 8, 1, '6789098762', 'khushbudetroja2002@gmail.com', 'dcf', '22ABCDE1234F1Z5', '5678905678', '12324356', '2354655', '54656557676', 1, '2025-06-03 14:35:42', 1, '2025-06-26 12:32:40', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_wise_item_preservation_price_list_detail`
--

CREATE TABLE `tbl_customer_wise_item_preservation_price_list_detail` (
  `customer_wise_item_preservation_price_list_detail_id` int(11) UNSIGNED NOT NULL,
  `customer_wise_item_preservation_price_list_id` int(11) UNSIGNED DEFAULT NULL,
  `packing_unit_id` int(11) UNSIGNED DEFAULT NULL,
  `rent_per_qty_month` decimal(18,2) DEFAULT NULL,
  `rent_per_qty_season` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_wise_item_preservation_price_list_detail`
--

INSERT INTO `tbl_customer_wise_item_preservation_price_list_detail` (`customer_wise_item_preservation_price_list_detail_id`, `customer_wise_item_preservation_price_list_id`, `packing_unit_id`, `rent_per_qty_month`, `rent_per_qty_season`) VALUES
(1, 1, 4, 888786.00, 0.00),
(2, 1, 2, 748.00, 12.00),
(3, 2, 4, 8888.00, 0.00),
(4, 2, 2, 748.00, 0.00),
(5, 3, 4, 9.68, 9.00),
(6, 3, 2, 9.00, 9.00),
(7, 4, 4, 789879.99, 0.00),
(8, 4, 2, 100.98, 0.00),
(9, 1, 5, 0.00, 0.00),
(10, 1, 1, 0.00, 0.00),
(11, 5, 4, 88.00, 0.00),
(12, 5, 5, 8.00, 9.00),
(13, 5, 2, 9.00, 9.00),
(14, 5, 1, 0.00, 9.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_wise_item_preservation_price_list_master`
--

CREATE TABLE `tbl_customer_wise_item_preservation_price_list_master` (
  `customer_wise_item_preservation_price_list_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED DEFAULT NULL,
  `item_id` int(11) UNSIGNED DEFAULT NULL,
  `rent_per_kg_month` decimal(18,2) DEFAULT NULL,
  `rent_per_kg_season` decimal(18,3) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `company_year_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_wise_item_preservation_price_list_master`
--

INSERT INTO `tbl_customer_wise_item_preservation_price_list_master` (`customer_wise_item_preservation_price_list_id`, `customer_id`, `item_id`, `rent_per_kg_month`, `rent_per_kg_season`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`, `company_year_id`) VALUES
(1, 1, 4, 12.00, 56.000, '2025-05-29 15:07:12', 1, '2025-06-18 14:46:39', 1, 1, 22),
(2, 1, 4, 12.00, 56.000, '2025-05-29 15:07:53', 1, '2025-05-29 15:07:53', 1, 1, 16),
(3, 4, 3, 34.00, 43.000, '2025-05-29 15:09:24', 1, '2025-05-30 12:18:26', 1, 1, 22),
(4, 4, 4, 50.00, 60.000, '2025-05-29 15:32:59', 1, '2025-05-30 12:19:50', 1, 1, 22),
(5, 2, 4, 6.00, 78.000, '2025-06-18 16:37:51', 1, '2025-06-18 16:37:51', 1, 1, 17);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_floor_master`
--

CREATE TABLE `tbl_floor_master` (
  `floor_id` int(11) UNSIGNED NOT NULL,
  `floor_name` varchar(100) DEFAULT NULL,
  `chamber_id` int(11) UNSIGNED DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_floor_master`
--

INSERT INTO `tbl_floor_master` (`floor_id`, `floor_name`, `chamber_id`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(1, 'Floor1', 1, '2025-04-23 12:30:27', 1, '2025-04-23 12:30:59', 1, 2),
(3, '2', NULL, '2025-04-28 10:32:41', NULL, '2025-04-28 10:32:41', NULL, 2),
(4, '5', 2, '2025-04-28 10:33:22', NULL, '2025-04-28 10:33:45', NULL, 2),
(5, '100', 3, '2025-05-12 12:37:31', NULL, '2025-05-15 14:06:42', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_generator_master`
--

CREATE TABLE `tbl_generator_master` (
  `generator_id` int(11) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `generator_options` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_generator_master`
--

INSERT INTO `tbl_generator_master` (`generator_id`, `table_name`, `generator_options`) VALUES
(20, 'tbl_issue_slip_master', '{\"field_name\":[\"issue_id\",\"issue_no\",\"issue_date\",\"special_note\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"number\",\"date\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Issue Id\",\"Issue No\",\"Issue Date\",\"Special Note\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"issue_no\",\"issue_date\",\"created_date\",\"modified_date\"],\"field_required\":[\"issue_no\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"issue_no\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"date\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(21, 'tbl_issue_slip_detail', '{\"field_name\":[\"issue_slip_detail_id\",\"issue_id\",\"item_id\",\"current_stock\",\"issue_qty\",\"unit_id\",\"item_type\",\"remark\"],\"field_type\":[\"hidden\",\"hidden\",\"select\",\"number\",\"number\",\"number\",\"select\",\"text\"],\"field_scale\":[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"\",\"\"],\"dropdown_table\":[\"\",\"\",\"tbl_item_master\",\"\",\"\",\"\",\"item_type_view\",\"\"],\"value_column\":[\"\",\"\",\"item_id\",\"\",\"\",\"\",\"item_type\",\"\"],\"label_column\":[\"\",\"\",\"item_name\",\"\",\"\",\"\",\"item_type\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Issue Slip Detail Id\",\"Issue Id\",\"Item Name\",\"Current Stock\",\"Issue Qty\",\"Unit Id\",\"Item Type\",\"Remark\"],\"field_display\":[\"item_id\",\"current_stock\",\"issue_qty\",\"unit_id\",\"item_type\",\"remark\"],\"field_required\":[\"item_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"int\",\"int\",\"int\",\"int\",\"varchar\",\"varchar\"]}'),
(23, 'tbl_purchase_order_master', '{\"field_name\":[\"purchase_order_id\",\"purchase_order_no\",\"date\",\"customer_id\",\"ref_no\",\"ref_date\",\"total_quantity\",\"total_amount\",\"special_note\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"number\",\"date\",\"select\",\"number\",\"date\",\"number\",\"number\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"0\",\"\",\"\",\"0\",\"0\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"tbl_customer_master\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"customer_id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"customer_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Purchase Order Id\",\"Purchase Order No\",\"Date\",\"Customer Name\",\"Ref No\",\"Ref Date\",\"Total Quantity\",\"Total Amount\",\"Special Note\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"purchase_order_no\",\"date\",\"customer_id\",\"ref_no\",\"ref_date\",\"created_date\",\"modified_date\"],\"field_required\":[\"purchase_order_no\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"purchase_order_no\"],\"is_disabled\":[],\"after_detail\":[\"total_quantity\",\"total_amount\",\"special_note\"],\"field_data_type\":[\"int\",\"int\",\"date\",\"int\",\"varchar\",\"date\",\"int\",\"int\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(24, 'tbl_purchase_order_detail', '{\"field_name\":[\"purchase_order_detail_id\",\"purchase_order_id\",\"item_id\",\"unit\",\"po_qty\",\"rate\",\"amount\",\"remark\",\"pending_qty\"],\"field_type\":[\"hidden\",\"hidden\",\"select\",\"number\",\"number\",\"number\",\"number\",\"text\",\"number\"],\"field_scale\":[\"0\",\"0\",\"0\",\"0\",\"3\",\"2\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"tbl_item_master\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"item_id\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"item_name\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Purchase Order Detail Id\",\"Purchase Order Id\",\"Item Id\",\"Unit\",\"Po Qty\",\"Rate\",\"Amount\",\"Remark\",\"Pending Qty\"],\"field_display\":[\"item_id\",\"unit\",\"po_qty\",\"rate\",\"amount\",\"remark\",\"pending_qty\"],\"field_required\":[\"item_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"int\",\"int\",\"decimal\",\"decimal\",\"int\",\"varchar\",\"int\"]}'),
(25, 'tbl_module_master', '{\"field_name\":[\"module_id\",\"module_name\",\"module_text\",\"tab_index\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"text\"],\"field_scale\":[\"0\",\"\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\"],\"field_label\":[\"Module Id\",\"Module Name\",\"Module Text\",\"Tab Index\"],\"field_display\":[\"module_name\",\"module_text\",\"tab_index\"],\"field_required\":[\"module_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"int\"]}'),
(26, 'tbl_menu_master', '{\"field_name\":[\"menu_id\",\"module_id\",\"menu_name\",\"menu_text\",\"menu_url\",\"tab_index\",\"is_display\"],\"field_type\":[\"hidden\",\"select\",\"text\",\"text\",\"hidden\",\"checkbox\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"\",\"\",\"0\",\"\"],\"dropdown_table\":[\"\",\"tbl_module_master\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"module_id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"module_name\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Menu Id\",\"Module Name\",\"Menu Name\",\"Menu Text\",\"Menu Url\",\"Is Display\",\"Is Display\"],\"field_display\":[\"module_id\",\"menu_name\",\"menu_text\",\"menu_url\",\"tab_index\",\"is_display\"],\"field_required\":[\"menu_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"menu_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"bit\"]}'),
(27, 'tbl_user_master', '{\"field_name\":[\"user_id\",\"login_id\",\"login_pass\",\"person_name\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"text\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"User Id\",\"User Name\",\"Password\",\"Person Name\",\"Want to Enable\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"login_id\",\"login_pass\",\"person_name\",\"status\"],\"field_required\":[\"login_id\",\"login_pass\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"login_id\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(30, 'tbl_country_master', '{\"table_layout\":\"vertical\",\"field_name\":[\"country_id\",\"country_name\",\"company_id\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"0\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Country Id\",\"Country Name\",\"Company Name\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"country_name\",\"company_id\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_required\":[\"country_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"country_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(34, 'tbl_bank_master', '{\"field_name\":[\"bank_id\",\"bank_name\",\"branch_name\",\"account_no\",\"ifs_code\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"text\",\"text\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Bank Id\",\"Bank Name\",\"Branch Name\",\"Account No\",\"Ifs Code\",\"Status\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"bank_name\",\"branch_name\",\"account_no\",\"ifs_code\",\"status\"],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(35, 'tbl_currency_master', '{\"field_name\":[\"currency_id\",\"currency_symbol\",\"currency_name\",\"currency_in_paise\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Currency Id\",\"Currency Symbol\",\"Currency Name\",\"Currency In Paise\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"currency_symbol\",\"currency_name\",\"currency_in_paise\"],\"field_required\":[\"currency_symbol\",\"currency_name\",\"currency_in_paise\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"currency_symbol\",\"currency_name\",\"currency_in_paise\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(36, 'tbl_item_master', '{\"field_name\":[\"item_id\",\"item_gst\",\"item_name\",\"market_rate\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"radio\",\"text\",\"number\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"2\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"view_item_gst_type\",\"\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"id\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"value\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Item Id\",\"Item Gst\",\"Item\",\"Market Rate\",\"Status\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"item_gst\",\"item_name\",\"market_rate\",\"status\"],\"field_required\":[\"item_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"item_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"varchar\",\"decimal\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(37, 'tbl_customer_account_group_master', '{\"field_name\":[\"customer_account_group_id\",\"customer_account_group_name\",\"under_group\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"file\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Account Group Id\",\"Account Group\",\"Under Group\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"customer_account_group_name\",\"under_group\"],\"field_required\":[\"customer_account_group_name\",\"under_group\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"customer_account_group_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(40, 'tbl_gst_tax_detail', '{\"field_name\":[\"gst_tax_id\",\"hsn_code_id\",\"tax_type\",\"tax\",\"effective_date\",\"remark\"],\"field_type\":[\"hidden\",\"hidden\",\"select\",\"number\",\"date\",\"textarea\"],\"field_scale\":[\"0\",\"0\",\"\",\"2\",\"\",\"\"],\"dropdown_table\":[\"\",\"\",\"view_tax_type\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"id\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"value\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Tax Id\",\"Hsn Code\",\"Tax Type\",\"Tax\",\"Effective Date\",\"Remark\"],\"field_display\":[\"tax_type\",\"tax\",\"effective_date\",\"remark\"],\"field_required\":[\"tax_type\",\"tax\",\"effective_date\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"varchar\",\"decimal\",\"date\",\"varchar\"]}'),
(45, 'tbl_hsn_master', '{\"field_name\":[\"hsn_id\",\"hsn_code\",\"description\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Hsn Id\",\"Hsn Code\",\"Description\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"hsn_code\",\"description\",\"modified_date\",\"modified_by\"],\"field_required\":[\"hsn_code\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"hsn_code\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(47, 'view_gsttaxdetail_tax_type', '{\"field_name\":[\"id\",\"value\"],\"field_type\":[\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\"],\"dropdown_table\":[\"\",\"view_gsttaxdetail_tax_type\"],\"value_column\":[\"\",\"value\"],\"label_column\":[\"\",\"value\"],\"where_condition\":[\"\",\"\"],\"field_label\":[\"Id\",\"Value\"],\"field_display\":[],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"varchar\"]}'),
(48, 'tbl_company_master', '{\r\n    \"table_layout\": \"horizontal\",\r\n    \"field_name\": [\"company_id\", \"company_name\", \"company_code\", \"company_logo\", \"company_logo_url\", \"address\", \"city\", \"pincode\", \"state\", \"phone\", \"email\", \"web_address\", \"gstin\", \"bank_id\", \"jurisdiction\", \"created_date\", \"created_by\", \"modified_date\", \"modified_by\"],\r\n    \"field_type\": [\"hidden\", \"text\", \"text\", \"file\", \"text\", \"text\", \"text\", \"text\", \"text\", \"text\", \"text\", \"text\", \"text\", \"select\", \"hidden\", \"hidden\", \"hidden\", \"hidden\", \"hidden\"],\r\n    \"field_scale\": [\"0\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"0\", \"\", \"\", \"0\", \"\", \"0\"],\r\n    \"dropdown_table\": [\"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"tbl_bank_master\", \"\", \"\", \"\", \"\", \"\"],\r\n    \"value_column\": [\"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"bank_id\", \"\", \"\", \"\", \"\", \"\"],\r\n    \"label_column\": [\"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"bank_name\", \"\", \"\", \"\", \"\", \"\"],\r\n    \"where_condition\": [\"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"\", \"status=1\", \"\", \"\", \"\", \"\", \"\"],\r\n    \"field_label\": [\"Company Id\", \"Company Name\", \"Company Code\", \"Company Logo\", \"Company Logo Url\", \"Address\", \"City\", \"Pincode\", \"State\", \"Phone\", \"Email\", \"Web Address\", \"Gstin\", \"Bank Name\", \"Jurisdiction\", \"Created Date\", \"Created By\", \"Modified Date By\", \"Modified By\"],\r\n    \"field_display\": [\"company_name\", \"company_code\", \"company_logo\", \"company_logo_url\", \"address\", \"city\", \"pincode\", \"state\", \"phone\", \"email\", \"web_address\", \"gstin\", \"bank_id\", \"modified_date\", \"modified_by\"],\r\n    \"field_required\": [\"company_name\"],\r\n    \"allow_zero\": [],\r\n    \"allow_minus\": [],\r\n    \"chk_duplicate\": [],\r\n    \"is_disabled\": [\"company_logo_url\"],\r\n    \"after_detail\": [],\r\n    \"field_data_type\": [\"int\", \"varchar\", \"varchar\", \"text\", \"varchar\", \"text\", \"varchar\", \"varchar\", \"varchar\", \"varchar\", \"varchar\", \"varchar\", \"varchar\", \"int\", \"varchar\", \"datetime\", \"int\", \"datetime\", \"int\"]\r\n}'),
(53, 'tbl_packing_unit_master', '{\"field_name\":[\"packing_unit_id\",\"packing_unit_name\",\"conversion_factor\",\"unloading_charge\",\"loading_charge\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"number\",\"number\",\"number\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"3\",\"2\",\"2\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Packing Unit Id\",\" Unit \",\"Conversion Factor\",\"Unloading Charge\",\"Loading Charge\",\"Status\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"packing_unit_name\",\"conversion_factor\",\"status\"],\"field_required\":[\"packing_unit_name\",\"conversion_factor\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"packing_unit_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"decimal\",\"decimal\",\"decimal\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(54, 'tbl_menu_right_master', '{\"field_name\":[\"menu_right_id\",\"menu_id\",\"right_name\",\"right_text\"],\"field_type\":[\"hidden\",\"select\",\"text\",\"text\"],\"field_scale\":[\"0\",\"0\",\"\",\"\"],\"dropdown_table\":[\"\",\"tbl_menu_master\",\"\",\"\"],\"value_column\":[\"\",\"menu_id\",\"\",\"\"],\"label_column\":[\"\",\"menu_text\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\"],\"field_label\":[\"Menu Right Id\",\"Menu Id\",\"Right Name\",\"Right Text\"],\"field_display\":[\"menu_id\",\"right_name\",\"right_text\"],\"field_required\":[\"menu_id\",\"right_name\",\"right_text\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"char\",\"varchar\"]}'),
(55, 'tbl_user_right_master', '{\"table_layout\":\"vertical\",\"field_name\":[\"user_right_master_id\",\"user_id\",\"menu_right_id\",\"has_right\"],\"field_type\":[\"hidden\",\"select\",\"select\",\"checkbox\"],\"field_scale\":[\"0\",\"0\",\"0\",\"\"],\"dropdown_table\":[\"\",\"tbl_user_master\",\"tbl_menu_right_master\",\"\"],\"value_column\":[\"\",\"user_id\",\"menu_right_id\",\"\"],\"label_column\":[\"\",\"login_id\",\"menu_right_id\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\"],\"field_label\":[\"User Right Id\",\"User\",\"Menu Right Id\",\"Is Right\"],\"field_display\":[\"user_id\",\"menu_right_id\"],\"field_required\":[\"user_id\",\"menu_right_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"bigint\",\"int\",\"int\",\"bit\"]}'),
(58, 'tbl_hsn_code_master', '{\"field_name\":[\"hsn_code_id\",\"hsn_code_name\",\"description\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"textarea\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Hsn Code Id\",\"Hsn Code\",\"Description\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"hsn_code_name\",\"description\"],\"field_required\":[\"hsn_code_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"hsn_code_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(88, 'tbl_city_master', '{\"table_layout\":\"vertical\",\"field_name\":[\"city_id\",\"city_name\",\"state_id\",\"country_id\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"0\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"tbl_state_master\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"state_id\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"state_name\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"company_id=COMPANY_ID\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"City Id\",\"City Name\",\"State Name\",\"Country Name\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"city_name\",\"state_id\",\"country_id\",\"modified_date\",\"modified_by\"],\"field_required\":[\"city_name\",\"state_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"city_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"int\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(90, 'tbl_chamber_master', '{\"table_layout\":\"vertical\",\"field_name\":[\"chamber_id\",\"chamber_name\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Chamber Id\",\"Chamber Name\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"chamber_name\"],\"field_required\":[\"chamber_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"chamber_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"date\",\"int\",\"date\",\"int\",\"int\"]}'),
(91, 'tbl_floor_master', '{\"field_name\":[\"floor_id\",\"floor_name\",\"chamber_id\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"tbl_chamber_master\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"chamber_id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"chamber_name\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Floor Id\",\"Floor Name\",\"Chamber Id\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"floor_name\",\"chamber_id\"],\"field_required\":[\"floor_name\",\"chamber_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(92, 'tbl_customer_master', '{\"field_name\":[\"customer_id\",\"customer\",\"customer_name\",\"customer_type\",\"account_group_id\",\"address\",\"city_id\",\"pincode\",\"state_id\",\"country_id\",\"phone\",\"email_id\",\"web_address\",\"gstin\",\"pan\",\"aadhar_no\",\"mandli_license_no\",\"fssai_license_no\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"select\",\"select\",\"textarea\",\"select\",\"text\",\"hidden\",\"hidden\",\"text\",\"email\",\"text\",\"text\",\"text\",\"text\",\"text\",\"text\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"0\",\"0\",\"\",\"0\",\"\",\"0\",\"0\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"view_customer_type\",\"tbl_customer_account_group_master\",\"\",\"tbl_city_master\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"id\",\"customer_account_group_id\",\"\",\"city_id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"value\",\"customer_account_group_name\",\"\",\"city_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Customer Id\",\"Customer\",\"Customer Name\",\"Customer Type\",\"Account Group Name\",\"Address\",\"City Name\",\"Pincode\",\"State Name\",\"Country Name\",\"Phone\",\"Email Id\",\"Web Address\",\"Gstin\",\"Pan\",\"Aadhar No\",\"Mandli License No\",\"Fssai License No\",\"Status\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"customer\",\"account_group_id\",\"city_id\",\"state_id\",\"country_id\",\"phone\",\"email_id\",\"gstin\",\"pan\",\"aadhar_no\"],\"field_required\":[\"customer\",\"city_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"customer\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"int\",\"int\",\"text\",\"int\",\"varchar\",\"int\",\"int\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(93, 'tbl_contact_person_detail', '{\"field_name\":[\"contact_person_id\",\"customer_id\",\"contact_person_name\",\"mobile\",\"email\",\"is_whatsapp\",\"is_email\"],\"field_type\":[\"hidden\",\"hidden\",\"text\",\"text\",\"text\",\"checkbox\",\"checkbox\"],\"field_scale\":[\"0\",\"0\",\"\",\"\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Contact Person Id\",\"Customer Id\",\"Contact Person Name\",\"Mobile\",\"Email\",\"Is Whatsapp\",\"Is Email\"],\"field_display\":[\"contact_person_name\",\"mobile\",\"email\",\"is_whatsapp\",\"is_email\"],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"varchar\",\"varchar\",\"varchar\",\"tinyint\",\"tinyint\"]}'),
(94, 'tbl_customer_wise_item_preservation_price_list_master', '{\"table_layout\":\"vertical\",\"field_name\":[\"customer_wise_item_preservation_price_list_id\",\"customer_id\",\"item_id\",\"rent_per_kg_month\",\"rent_per_kg_season\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"hidden\",\"hidden\",\"number\",\"number\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"0\",\"2\",\"3\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"tbl_customer_master\",\"tbl_item_master\",\"tbl_packing_unit_master\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"customer_id\",\"item_id\",\"packing_unit_id\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"customer\",\"item_name\",\"packing_unit_name\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Customer Wise Item Preservation Price List Id\",\"Customer \",\"Item \",\"Rent \\/ Kg.\\/Month\",\"Rent \\/ Kg.\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"customer_id\",\"item_id\",\"rent_per_kg_month\",\"rent_per_kg_season\"],\"field_required\":[\"customer_id\",\"item_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"item_id\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"int\",\"decimal\",\"decimal\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(98, 'tbl_state_master', '{\"table_layout\":\"vertical\",\"field_name\":[\"state_id\",\"state_name\",\"country_id\",\"gst_code\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"text\",\"select\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"0\",\"\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"tbl_country_master\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"country_id\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"country_name\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"company_id=COMPANY_ID\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"State Id\",\"State Name\",\"Country Name\",\"Gst Code\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"state_name\",\"country_id\",\"gst_code\",\"modified_date\",\"modified_by\"],\"field_required\":[\"state_name\",\"country_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"state_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"int\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(124, 'tbl_company_year_master', '{\"table_layout\":\"horizontal\",\"field_name\":[\"company_year_id\",\"company_id\",\"company_year_type\",\"start_date\",\"end_date\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"hidden\",\"select\",\"date\",\"date\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"view_company_year_type\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"id\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"value\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Company Year Id\",\"Company Id\",\"Company Year Type\",\"Start Date\",\"End Date\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"company_year_type\",\"start_date\",\"end_date\"],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"varchar\",\"date\",\"date\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(126, 'tbl_item_preservation_price_list_master', '{\"table_layout\":\"vertical\",\"field_name\":[\"item_preservation_price_list_id\",\"item_id\",\"rent_per_kg_month\",\"rent_per_kg_season\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\",\"company_id\"],\"field_type\":[\"hidden\",\"hidden\",\"number\",\"number\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"2\",\"3\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"tbl_item_master \",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"item_id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"item_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Item Preservation Price List Id\",\"Item \",\"Rent \\/ Kg.\\/ Month  \",\"Rent \\/ Kg.\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\",\"Company Id\"],\"field_display\":[\"item_id\",\"rent_per_kg_month\",\"rent_per_kg_season\",\"modified_by\"],\"field_required\":[\"item_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"item_id\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"decimal\",\"decimal\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(127, 'tbl_item_preservation_price_list_detail', '{\"field_name\":[\"item_preservation_price_list_detail_id\",\"item_preservation_price_list_id\",\"packing_unit_id\",\"rent_per_qty_month\",\"rent_per_qty_season\"],\"field_type\":[\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"0\",\"2\",\"3\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Item Preservation Price List Detail Id\",\"Item Preservation Price List Id\",\"Packing Unit Id\",\"Rent Per Qty Month\",\"Rent Per Qty Season\"],\"field_display\":[],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"int\",\"decimal\",\"decimal\"]}'),
(128, 'tbl_customer_wise_item_preservation_price_list_detail', '{\"field_name\":[\"customer_wise_item_preservation_price_list_detail_id\",\"customer_wise_item_preservation_price_list_id\",\"packing_unit_id\",\"rent_per_qty_month\",\"rent_per_qty_season\"],\"field_type\":[\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"0\",\"2\",\"2\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Customer Wise Item Preservation Price List Detail Id\",\"Customer Wise Item Preservation Price List Id\",\"Packing Unit Id\",\"Rent Per Qty Month\",\"Rent Per Qty Season\"],\"field_display\":[],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"int\",\"decimal\",\"decimal\"]}'),
(129, 'tbl_outward_master', '{\"table_layout\":\"vertical\",\"field_name\":[\"outward_id\",\"outward_sequence\",\"outward_no\",\"outward_date\",\"customer\",\"total_qty\",\"total_wt\",\"gross_wt\",\"tare_wt\",\"net_wt\",\"loading_expense\",\"other_expense1\",\"other_expense2\",\"outward_order_by\",\"delivery_to\",\"vehicle_no\",\"driver_name\",\"driver_mob_no\",\"transporter\",\"sp_note\",\"created_by\",\"created_date\",\"modified_by\",\"modified_date\",\"company_id\",\"company_year_id\"],\"field_type\":[\"hidden\",\"number\",\"text\",\"date\",\"select\",\"number\",\"text\",\"number\",\"number\",\"number\",\"number\",\"number\",\"number\",\"select\",\"text\",\"text\",\"text\",\"text\",\"text\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"\",\"0\",\"0\",\"2\",\"2\",\"2\",\"2\",\"2\",\"2\",\"2\",\"0\",\"\",\"\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"tbl_customer_master cm INNER JOIN tbl_inward_master im ON cm.customer_id = im.customer INNER JOIN tbl_inward_detail id ON im.inward_id = id.inward_id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"tbl_contact_person_detail\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"customer_id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"contact_person_id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"customer_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"contact_person_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\r\n\"where_condition\":[\"\",\"\",\"\",\"\",\"im.company_id=COMPANY_ID AND (id.inward_qty - COALESCE((SELECT SUM(od.out_qty) FROM tbl_outward_detail od WHERE od.inward_detail_id = id.inward_detail_id), 0)) > 0 GROUP BY cm.customer_id, cm.customer_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"customer_id IN (SELECT DISTINCT customer FROM tbl_inward_master WHERE company_id = COMPANY_ID)\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Outward Id\",\"Outward No\",\"\",\"Outward Date\",\"Customer\",\"Total Qty\",\"Total Wt\",\"Gross Wt\",\"Tare Wt\",\"Net Wt\",\"Loading Expense\",\"Other Expense1\",\"Other Expense2\",\"Outward Order By\",\"Delivery To\",\"Vehicle No\",\"Driver Name\",\"Driver Mob No\",\"Transporter\",\"Sp Note\",\"Created By\",\"Created Date\",\"Modified By\",\"Modified Date\",\"Company Id\",\"Company Year Id\"],\"field_display\":[\"outward_sequence\",\"outward_date\",\"customer\",\"total_qty\",\"total_wt\",\"gross_wt\",\"tare_wt\"],\"field_required\":[\"outward_sequence\",\"customer\"],\"allow_zero\":[\"total_wt\"],\"allow_minus\":[\"total_wt\"],\"chk_duplicate\":[\"outward_sequence\",\"outward_no\"],\"is_disabled\":[\"outward_no\",\"total_qty\",\"total_wt\",\"loading_expense\"],\"after_detail\":[\"total_qty\",\"total_wt\",\"gross_wt\",\"tare_wt\",\"net_wt\",\"loading_expense\",\"other_expense1\",\"other_expense2\",\"outward_order_by\",\"delivery_to\",\"vehicle_no\",\"driver_name\",\"driver_mob_no\",\"transporter\",\"sp_note\"],\"field_data_type\":[\"bigint\",\"int\",\"varchar\",\"date\",\"int\",\"int\",\"decimal\",\"decimal\",\"decimal\",\"decimal\",\"decimal\",\"decimal\",\"decimal\",\"int\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(130, 'tbl_inward_master', '{\"table_layout\":\"vertical\",\"field_name\":[\"inward_id\",\"inward_sequence\",\"inward_no\",\"inward_date\",\"customer\",\"broker\",\"billing_starts_from\",\"total_unloading_charge\",\"sp_note\",\"total_qty\",\"total_wt\",\"weigh_bridge_slip_no\",\"gross_wt\",\"tare_wt\",\"net_wt\",\"vehicle_no\",\"driver_name\",\"driver_mobile_no\",\"transporter\",\"other_expense1\",\"other_expense2\",\"created_by\",\"created_date\",\"modified_by\",\"modified_date\",\"company_id\",\"company_year_id\"],\"field_type\":[\"hidden\",\"number\",\"text\",\"date\",\"select\",\"select\",\"hidden\",\"number\",\"text\",\"text\",\"text\",\"text\",\"number\",\"number\",\"number\",\"text\",\"text\",\"text\",\"text\",\"text\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"\",\"0\",\"0\",\"\",\"2\",\"\",\"0\",\"2\",\"\",\"2\",\"2\",\"2\",\"\",\"\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"tbl_customer_master\",\"tbl_customer_master\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"customer_id\",\"customer_id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"customer_name\",\"customer_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"status=1 AND customer_type=1\",\"status=1  AND customer_type=2\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Inward Id\",\"Inward No\",\"\",\"Inward Date\",\"Customer\",\"Broker\",\"Billing Starts From\",\"Unloading Charge\",\"Sp Note\",\"Total Qty\",\"Total Wt\",\"Weigh Bridge Slip No\",\"Gross Wt\",\"Tare Wt\",\"Net Wt\",\"Vehicle No\",\"Driver Name\",\"Driver Mobile No\",\"Transporter\",\"Other Expense1\",\"Other Expense2\",\"Created By\",\"Created Date\",\"Modified By\",\"Modified Date\",\"Company Id\",\"Company Year Id\"],\"field_display\":[\"inward_no\",\"inward_date\",\"customer\",\"broker\",\"total_unloading_charge\",\"vehicle_no\",\"driver_name\",\"transporter\"],\"field_required\":[\"inward_sequence\",\"inward_date\",\"customer\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"inward_sequence\",\"inward_no\"],\"is_disabled\":[\"inward_no\"],\"after_detail\":[\"total_unloading_charge\",\"sp_note\",\"total_qty\",\"total_wt\",\"weigh_bridge_slip_no\",\"gross_wt\",\"tare_wt\",\"net_wt\",\"vehicle_no\",\"driver_name\",\"driver_mobile_no\",\"transporter\",\"other_expense1\",\"other_expense2\"],\"field_data_type\":[\"bigint\",\"int\",\"varchar\",\"date\",\"int\",\"int\",\"date\",\"decimal\",\"text\",\"int\",\"decimal\",\"varchar\",\"decimal\",\"decimal\",\"decimal\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}'),
(131, 'tbl_inward_detail', '{\"field_name\":[\"inward_detail_id\",\"inward_id\",\"lot_no\",\"item\",\"gst_type\",\"variety\",\"packing_unit\",\"inward_qty\",\"inward_wt\",\"avg_wt_per_bag\",\"location\",\"moisture\",\"storage_duration\",\"rent_per_month\",\"rent_per_storage_duration\",\"seasonal_start_date\",\"seasonal_end_date\",\"seasonal_rent\",\"seasonal_rent_per\",\"unloading_charge\",\"remark\"],\"field_type\":[\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"0\",\"0\",\"\",\"0\",\"0\",\"2\",\"2\",\"\",\"\",\"0\",\"2\",\"2\",\"\",\"\",\"2\",\"0\",\"2\",\"\"],\"dropdown_table\":[\"\",\"\",\"\",\"tbl_item_master\",\"view_item_gst_type\",\"\",\"tbl_packing_unit_master\",\"\",\"\",\"\",\"\",\"\",\"view_storage_duration\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"item_id\",\"id\",\"\",\"packing_unit_id\",\"\",\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"item_name\",\"value\",\"\",\"packing_unit_name\",\"\",\"\",\"\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"status=1\",\"\",\"\",\"status=1\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Inward Detail Id\",\"Inward Id\",\"Lot No\",\"Item\",\"Gst Type\",\"Variety\",\"Packing Unit\",\"Inward Qty\",\"Inward Wt\",\"Avg Wt Per Bag\",\"Location\",\"Moisture\",\"Storage Duration\",\"Rent Per Month\",\"Rent Per Storage Duration\",\"Sesonal Start Date\",\"Seasonal End Date\",\"Seasonal Rent\",\"Seasonal Rent Per\",\"Unloading Charge\",\"Remark\"],\"field_display\":[\"lot_no\",\"item\",\"gst_type\",\"variety\",\"packing_unit\",\"inward_qty\",\"inward_wt\",\"avg_wt_per_bag\",\"location\",\"storage_duration\",\"unloading_charge\",\"remark\"],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"bigint\",\"bigint\",\"varchar\",\"int\",\"int\",\"varchar\",\"int\",\"int\",\"decimal\",\"decimal\",\"varchar\",\"varchar\",\"int\",\"decimal\",\"decimal\",\"date\",\"date\",\"decimal\",\"int\",\"decimal\",\"varchar\"]}'),
(132, 'tbl_outward_detail', '{\"field_name\":[\"outward_detail_id\",\"outward_id\",\"inward_detail_id\",\"out_qty\",\"out_wt\",\"loading_charges\"],\"field_type\":[\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"0\",\"2\",\"2\",\"2\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Outward Detail Id\",\"Outward Id\",\"Inward Detail Id\",\"Out Qty\",\"Out Wt\",\"Loading Charges\"],\"field_display\":[],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"bigint\",\"bigint\",\"bigint\",\"decimal\",\"decimal\",\"decimal\"]}'),
(133, 'tbl_rent_invoice_master', '{\"table_layout\":\"horizontal\",\"field_name\":[\"rent_invoice_id\",\"invoice_type\",\"rent_invoice_sequence\",\"invoice_no\",\"invoice_date\",\"billing_till_date\",\"debit_cash\",\"customer\",\"invoice_for\",\"hsn_code\",\"grace_days\",\"lot_no\",\"basic_amount\",\"unloading_exp\",\"loading_exp\",\"other_expense3\",\"tax_amount\",\"sgst\",\"cgst\",\"igst\",\"net_amount\",\"sp_note\",\"created_by\",\"created_date\",\"modified_by\",\"modified_date\",\"company_id\",\"company_year_id\"],\"field_type\":[\"hidden\",\"radio\",\"number\",\"text\",\"date\",\"date\",\"select\",\"select\",\"select\",\"select\",\"text\",\"select\",\"number\",\"number\",\"number\",\"textarea\",\"radio\",\"number\",\"number\",\"number\",\"number\",\"textarea\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"0\",\"\",\"\",\"\",\"0\",\"0\",\"0\",\"0\",\"0\",\"\",\"2\",\"2\",\"2\",\"2\",\"0\",\"0\",\"0\",\"0\",\"0\",\"\",\"0\",\"\",\"0\",\"\",\"0\",\"0\"],\"dropdown_table\":[\"\",\"view_invoice_type\",\"\",\"\",\"\",\"\",\"view_debit_cash\",\"tbl_customer_master cm INNER JOIN tbl_inward_master im ON cm.customer_id=im.customer\",\"view_invoice_for\",\"tbl_hsn_code_master\",\"\",\"tbl_inward_detail\",\"\",\"\",\"\",\"\",\"view_tax_amount\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"id\",\"\",\"\",\"\",\"\",\"id\",\"customer_id\",\"id\",\"hsn_code_id\",\"\",\"lot_no\",\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"value\",\"\",\"\",\"\",\"\",\"value\",\"customer_name\",\"value\",\"hsn_code_name,description\",\"\",\"lot_no\",\"\",\"\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"im.company_id=COMPANY_ID AND im.company_year_id=COMPANY_YEAR_ID\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Rent Invoice Id\",\"Invoice Type\",\"Invoice No\",\"\",\"Invoice Date\",\"Billing Till Date\",\"Debit \\/ Cash\",\"Customer\",\"Invoice For\",\"Hsn Code\",\"Grace Days\",\"Lot No\",\"Basic Amount\",\"Unloading Exp\",\"Loading Exp\",\"Other Expense3\",\"Tax Amount\",\"Sgst\",\"Cgst\",\"Igst\",\"Net Amount\",\"Sp Note\",\"Created By\",\"Created Date\",\"Modified By\",\"Modified Date\",\"Company Id\",\"Company Year Id\"],\"field_display\":[\"invoice_no\",\"invoice_date\",\"debit_cash\",\"customer\",\"hsn_code\",\"basic_amount\",\"tax_amount\",\"net_amount\",\"sp_note\"],\"field_required\":[\"rent_invoice_sequence\",\"invoice_date\",\"customer\",\"invoice_for\",\"hsn_code\",\"lot_no\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"rent_invoice_sequence\",\"invoice_no\"],\"is_disabled\":[\"invoice_no\",\"net_amount\"],\"after_detail\":[\"basic_amount\",\"unloading_exp\",\"loading_exp\",\"other_expense3\",\"tax_amount\",\"sgst\",\"cgst\",\"igst\",\"net_amount\",\"sp_note\",\"created_by\",\"created_date\",\"modified_by\",\"modified_date\",\"company_id\",\"company_year_id\"],\"field_data_type\":[\"bigint\",\"int\",\"int\",\"varchar\",\"date\",\"date\",\"int\",\"int\",\"int\",\"int\",\"int\",\"varchar\",\"decimal\",\"decimal\",\"decimal\",\"decimal\",\"int\",\"int\",\"int\",\"int\",\"decimal\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\",\"int\"]}');
INSERT INTO `tbl_generator_master` (`generator_id`, `table_name`, `generator_options`) VALUES
(134, 'tbl_rent_invoice_detail', '{\"field_name\":[\"rent_invoice_detail_id\",\"rent_invoice_id\",\"description\",\"qty\",\"unit\",\"weight\",\"rate_per_unit\",\"amount\",\"remark\"],\"field_type\":[\"hidden\",\"hidden\",\"text\",\"number\",\"text\",\"number\",\"number\",\"number\",\"text\"],\"field_scale\":[\"0\",\"0\",\"\",\"0\",\"\",\"2\",\"2\",\"0\",\"\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Rent Invoice Detail Id\",\"Rent Invoice Id\",\"Description\",\"Qty\",\"Unit\",\"Weight\",\"Rate \\/ Unit\",\"Amount\",\"Remark\"],\"field_display\":[\"description\",\"qty\",\"unit\",\"rate_per_unit\",\"amount\",\"remark\"],\"field_required\":[\"description\",\"qty\",\"unit\",\"rate_per_unit\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"varchar\",\"int\",\"varchar\",\"decimal\",\"decimal\",\"int\",\"varchar\"]}');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gst_tax_detail`
--

CREATE TABLE `tbl_gst_tax_detail` (
  `gst_tax_id` int(11) UNSIGNED NOT NULL,
  `hsn_code_id` int(11) UNSIGNED NOT NULL,
  `tax_type` varchar(100) DEFAULT NULL,
  `tax` decimal(18,2) DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_gst_tax_detail`
--

INSERT INTO `tbl_gst_tax_detail` (`gst_tax_id`, `hsn_code_id`, `tax_type`, `tax`, `effective_date`, `remark`) VALUES
(1, 1, '1', 9.00, '2017-07-01', ''),
(2, 1, '3', 18.00, '2017-07-01', ''),
(3, 1, '2', 12.00, '2017-07-01', ''),
(4, 2, '1', 8.00, '2017-07-01', ''),
(5, 2, '2', 3.00, '2017-07-01', ''),
(6, 2, '3', 9.00, '2017-07-01', ''),
(7, 3, '1', 11.00, '2017-07-01', ''),
(8, 3, '2', 12.00, '2017-07-01', ''),
(9, 3, '3', 11.00, '2017-07-01', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_hsn_code_master`
--

CREATE TABLE `tbl_hsn_code_master` (
  `hsn_code_id` int(11) UNSIGNED NOT NULL,
  `hsn_code_name` varchar(100) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_hsn_code_master`
--

INSERT INTO `tbl_hsn_code_master` (`hsn_code_id`, `hsn_code_name`, `description`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(1, '996721', 'Refrigerated Storage Service', '2025-06-17 12:46:55', 1, '2025-06-17 12:46:55', 1, 1),
(2, '998619', 'Support Service to Agriculture, Hunting, Forestry & Fishing', '2025-06-17 12:48:16', 1, '2025-06-17 12:51:23', 1, 1),
(3, '8', 'description', '2025-06-18 11:51:54', 1, '2025-06-23 10:11:26', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_inward_detail`
--

CREATE TABLE `tbl_inward_detail` (
  `inward_detail_id` bigint(20) UNSIGNED NOT NULL,
  `inward_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lot_no` varchar(100) DEFAULT NULL,
  `item` int(11) UNSIGNED DEFAULT NULL,
  `gst_type` int(11) DEFAULT NULL,
  `marko` varchar(100) DEFAULT NULL,
  `packing_unit` int(11) UNSIGNED DEFAULT NULL,
  `inward_qty` int(11) DEFAULT NULL,
  `inward_wt` decimal(18,2) DEFAULT NULL,
  `avg_wt_per_bag` decimal(18,2) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `moisture` varchar(100) DEFAULT NULL,
  `storage_duration` int(11) DEFAULT NULL,
  `rent_per_month` decimal(18,2) DEFAULT NULL,
  `rent_per_storage_duration` decimal(18,2) DEFAULT NULL,
  `seasonal_start_date` date DEFAULT NULL,
  `seasonal_end_date` date DEFAULT NULL,
  `rent_per` int(11) DEFAULT NULL,
  `unloading_charge` decimal(18,2) DEFAULT NULL,
  `remark` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_inward_detail`
--

INSERT INTO `tbl_inward_detail` (`inward_detail_id`, `inward_id`, `lot_no`, `item`, `gst_type`, `marko`, `packing_unit`, `inward_qty`, `inward_wt`, `avg_wt_per_bag`, `location`, `moisture`, `storage_duration`, `rent_per_month`, `rent_per_storage_duration`, `seasonal_start_date`, `seasonal_end_date`, `rent_per`, `unloading_charge`, `remark`) VALUES
(1, 1, '1', 3, 1, '23', 1, 100, 100.00, 1.00, 'Medical - Floor1 - 1', '1', 1, 0.00, 12.00, NULL, NULL, 1, 12.00, 'big apple'),
(2, 1, '2', 4, 2, '2', 2, 50, 150.00, 3.00, 'Medical - 2 - 1', '', 2, 0.00, 174.53, NULL, NULL, 1, 6.00, 'pen very small'),
(3, 2, '2', 5, 2, '1', 4, 80, 960.00, 12.00, 'fruits - 5 - 1', '', 9, 9999.00, 9999.00, '2025-06-19', '2025-12-19', 1, 20.00, ''),
(4, 2, '6', 6, 3, '2', 5, 90, 90.00, 1.00, 'Medical2 - 2 - 32', '', 1, 0.00, 112.00, NULL, NULL, 2, 1.00, ''),
(5, 2, '9', 7, 1, '1', 1, 12, 12.00, 1.00, 'Medical - 2 - 32', '', 4, 0.00, 30.00, NULL, NULL, 1, 12.00, ''),
(6, 1, '7', 5, 2, '1', 2, 23, 69.00, 3.00, 'Medical - Floor1 - 3', '', 3, 0.00, 22.00, '2025-06-19', '2025-06-19', 1, 6.00, ''),
(7, 3, '1', 5, 2, '3', 1, 34, 34.00, 1.00, 'Medical - Floor1 - 3', '', 3, 0.00, 33.00, '0000-00-00', '0000-00-00', 1, 12.00, '3'),
(9, 1, '7', 6, 3, '5', 5, 70, 70.00, 1.00, 'Medical - 2 - 87', '', 7, 100.00, 150.00, NULL, NULL, 1, 1.00, 'good'),
(10, 1, '4', 6, 3, '5', 4, 54, 648.00, 12.00, 'fruits - 5 - 54', '', 5, 45.00, 46.50, NULL, NULL, 1, 20.00, ''),
(11, 2, '10', 6, 3, '2', 4, 34, 408.00, 12.00, 'Medical2 - 2 - 34', '23', 4, 0.00, 23.00, NULL, NULL, 1, 20.00, ''),
(12, 1, '77', 3, 1, '67', 5, 767, 767.00, 1.00, 'fruits - 5 - 77', '', 6, 76.00, 93.73, '0000-00-00', '0000-00-00', 1, 1.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_inward_master`
--

CREATE TABLE `tbl_inward_master` (
  `inward_id` bigint(20) UNSIGNED NOT NULL,
  `inward_sequence` int(11) DEFAULT NULL,
  `inward_no` varchar(100) DEFAULT NULL,
  `inward_date` date DEFAULT NULL,
  `customer` int(11) UNSIGNED DEFAULT NULL,
  `broker` int(11) UNSIGNED DEFAULT NULL,
  `billing_starts_from` date DEFAULT NULL,
  `total_unloading_charge` decimal(18,2) DEFAULT NULL,
  `sp_note` text DEFAULT NULL,
  `total_qty` int(11) DEFAULT NULL,
  `total_wt` decimal(18,2) DEFAULT NULL,
  `weigh_bridge_slip_no` varchar(100) DEFAULT NULL,
  `gross_wt` decimal(18,2) DEFAULT NULL,
  `tare_wt` decimal(18,2) DEFAULT NULL,
  `net_wt` decimal(18,2) DEFAULT NULL,
  `vehicle_no` varchar(100) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `driver_mobile_no` varchar(100) DEFAULT NULL,
  `transporter` varchar(100) DEFAULT NULL,
  `other_expense1` varchar(500) DEFAULT NULL,
  `other_expense2` varchar(500) DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `company_year_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_inward_master`
--

INSERT INTO `tbl_inward_master` (`inward_id`, `inward_sequence`, `inward_no`, `inward_date`, `customer`, `broker`, `billing_starts_from`, `total_unloading_charge`, `sp_note`, `total_qty`, `total_wt`, `weigh_bridge_slip_no`, `gross_wt`, `tare_wt`, `net_wt`, `vehicle_no`, `driver_name`, `driver_mobile_no`, `transporter`, `other_expense1`, `other_expense2`, `created_by`, `created_date`, `modified_by`, `modified_date`, `company_id`, `company_year_id`) VALUES
(1, 1, '0001/25-26', '2025-06-19', 2, 3, '2025-06-19', 46.00, 'good', 1064, 1804.00, '5', 14.00, 2.00, 12.00, '5555', 'hetu', '87989878545', '45', '11', '1', 1, '2025-06-19 14:05:09', 1, '2025-06-26 16:03:01', 1, 22),
(2, 2, '0002/25-26', '2025-06-19', 4, 5, '2025-06-19', 53.00, 'mm', 216, 1470.00, '5', 34.00, 3.00, 31.00, '54545', 'manu', '7869878987', '2', '33', '634436', 1, '2025-06-19 14:07:20', 1, '2025-06-26 09:57:35', 1, 22),
(3, 1, '0001/24-25', '2024-06-20', 4, 5, '2024-06-20', 12.00, 'good', 34, 34.00, '5', 32.00, 3.00, 29.00, '12', 'hetu', '7869878987', '45', '8', '8', 1, '2025-06-20 10:04:30', 1, '2025-06-20 10:04:30', 1, 17),
(4, 3, '0003/25-26', '2025-06-26', 1, 3, '2025-06-26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-26 12:30:29', 1, '2025-06-26 12:30:29', 1, 22),
(5, 4, '0004/25-26', '2025-06-26', 6, 3, '2025-06-26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-26 12:32:20', 1, '2025-06-26 12:32:20', 1, 22),
(6, 1, '0001/26-27', '2026-06-26', 1, 3, '2026-06-26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-26 14:45:48', 1, '2025-06-26 14:45:48', 1, 16);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item_master`
--

CREATE TABLE `tbl_item_master` (
  `item_id` int(11) UNSIGNED NOT NULL,
  `item_gst` int(11) DEFAULT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `market_rate` decimal(18,2) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_item_master`
--

INSERT INTO `tbl_item_master` (`item_id`, `item_gst`, `item_name`, `market_rate`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(3, 1, 'apple', 10.00, 1, '2025-05-17 15:43:26', NULL, '2025-05-17 15:43:26', NULL, 2),
(4, 2, 'pen', 10.00, 1, '2025-05-17 15:43:39', NULL, '2025-05-17 15:43:39', NULL, 2),
(5, 2, 'shoes', 10.00, 1, '2025-05-17 15:44:04', NULL, '2025-05-20 11:28:47', 1, 1),
(6, 3, 'watch', 10.00, 1, '2025-05-19 15:33:34', 1, '2025-05-19 15:33:34', 1, 1),
(7, 1, 'pencile', 10.00, 1, '2025-05-24 12:16:42', 1, '2025-06-11 15:38:11', 1, 1),
(8, 1, 'shoess', 122.00, 1, '2025-06-13 17:22:34', 1, '2025-06-16 17:55:18', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item_preservation_price_list_detail`
--

CREATE TABLE `tbl_item_preservation_price_list_detail` (
  `item_preservation_price_list_detail_id` int(11) UNSIGNED NOT NULL,
  `item_preservation_price_list_id` int(11) UNSIGNED NOT NULL,
  `packing_unit_id` int(11) UNSIGNED NOT NULL,
  `rent_per_qty_month` decimal(18,2) DEFAULT NULL,
  `rent_per_qty_season` decimal(18,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_item_preservation_price_list_detail`
--

INSERT INTO `tbl_item_preservation_price_list_detail` (`item_preservation_price_list_detail_id`, `item_preservation_price_list_id`, `packing_unit_id`, `rent_per_qty_month`, `rent_per_qty_season`) VALUES
(1, 1, 2, 123.00, 13.000),
(2, 1, 4, 23.00, 3.000),
(3, 2, 2, 2.00, 2.000),
(4, 2, 4, 2.00, 2.000),
(5, 3, 2, 4546445.76, 0.000),
(6, 3, 4, 36747.58, 9999.000),
(7, 4, 2, 3.00, 0.000),
(8, 4, 4, 3.00, 0.000),
(9, 5, 2, 675.00, 0.000),
(10, 5, 4, 0.00, 0.000),
(11, 1, 1, 0.00, 0.000),
(12, 1, 5, 0.00, 0.000),
(13, 3, 1, 0.00, 0.000),
(14, 3, 5, 0.00, 0.000);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item_preservation_price_list_master`
--

CREATE TABLE `tbl_item_preservation_price_list_master` (
  `item_preservation_price_list_id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED DEFAULT NULL,
  `rent_per_kg_month` decimal(18,2) DEFAULT NULL,
  `rent_per_kg_season` decimal(18,3) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `company_year_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_item_preservation_price_list_master`
--

INSERT INTO `tbl_item_preservation_price_list_master` (`item_preservation_price_list_id`, `item_id`, `rent_per_kg_month`, `rent_per_kg_season`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`, `company_year_id`) VALUES
(1, 3, 123.00, 2312.000, '2025-05-29 15:16:32', 1, '2025-06-17 14:37:42', 1, 1, 22),
(2, 3, 2.00, 2.000, '2025-05-29 15:23:05', 1, '2025-05-29 15:24:37', 1, 1, 16),
(3, 5, 364346.45, 47547.567, '2025-05-30 12:17:18', 1, '2025-06-10 17:43:32', 1, 1, 22),
(4, 3, 1.00, 3.000, '2025-05-30 17:36:38', 1, '2025-05-30 17:36:38', 1, 2, 22),
(5, 4, 465.00, 6768.000, '2025-06-03 16:52:49', 1, '2025-06-03 16:53:42', 1, 1, 22);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu_master`
--

CREATE TABLE `tbl_menu_master` (
  `menu_id` int(11) UNSIGNED NOT NULL,
  `module_id` int(11) UNSIGNED NOT NULL,
  `menu_name` varchar(100) DEFAULT NULL,
  `menu_text` varchar(100) DEFAULT NULL,
  `menu_url` varchar(255) DEFAULT NULL,
  `menu_group` int(11) DEFAULT NULL,
  `tab_index` int(11) DEFAULT NULL,
  `is_display` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_menu_master`
--

INSERT INTO `tbl_menu_master` (`menu_id`, `module_id`, `menu_name`, `menu_text`, `menu_url`, `menu_group`, `tab_index`, `is_display`) VALUES
(1, 1, 'country', 'Country', 'frm_country_master.php', 4, 1, b'1'),
(2, 1, 'state', 'State', 'frm_state_master.php', 4, 2, b'1'),
(3, 1, 'city', 'City', 'frm_city_master.php', 4, 3, b'1'),
(4, 1, 'currency', 'Currency', 'frm_currency_master.php', 8, 4, b'1'),
(5, 1, 'bank', 'Bank', 'frm_bank_master.php', 8, 5, b'1'),
(6, 1, 'customer_account_group', 'Customer Account Group', 'frm_customer_account_group_master.php', 6, 6, b'1'),
(7, 1, 'customer', 'Customer', 'frm_customer_master.php', 6, 7, b'1'),
(8, 1, 'packing_unit', 'Packing Unit', 'frm_packing_unit_master.php', 7, 8, b'1'),
(9, 1, 'item', 'Item', 'frm_item_master.php', 7, 9, b'1'),
(10, 1, 'chamber', 'Chamber', 'frm_chamber_master.php', 5, 10, b'1'),
(11, 1, 'floor', 'Floor', 'frm_floor_master.php', 5, 11, b'1'),
(12, 1, 'item_preservation_price_list', 'Item Preservation Price List', 'frm_item_preservation_price_list_master.php', 7, 12, b'1'),
(13, 1, 'customer_wise_item_preservation_price_list', 'Customer wise Item Preservation Price List', 'frm_customer_wise_item_preservation_price_list_master.php', 7, 13, b'1'),
(14, 1, 'hsn_code', 'HSN Code', 'frm_hsn_code_master.php', 8, 14, b'1'),
(15, 2, 'inward', 'Inward', 'frm_inward_master.php', 14, 15, b'1'),
(16, 2, 'outward', 'Outward', 'frm_outward_master.php', 14, 16, b'1'),
(17, 2, 'invoice', 'Invoice', 'frm_rent_invoice_master.php', 14, 17, b'1'),
(18, 3, 'generate_rent_invoice', 'Generate Rent Invoice', 'frm_generate_rent_invoice_master.php', 15, 18, b'1'),
(19, 3, 'multi_invoice_print', 'Multi Invoice Print', 'frm_multi_invoice_print_master.php', 15, 19, b'1'),
(20, 3, 'inward_lock/unlock', 'Inward Lock / Unlock', 'frm_inward_lock/unlock_master.php', 15, 20, b'1'),
(21, 3, 'change_location', 'Change Location', 'frm_change_location_master.php', 15, 21, b'1'),
(22, 4, 'inward_summary', 'Inward Summary', 'frm_inward_summary_master.php', 9, 22, b'1'),
(23, 4, 'outward_summary', 'Outward Summary', 'frm_outward_summary_master.php', 9, 23, b'1'),
(24, 4, 'invoice_summary', 'Invoice Summary', 'frm_invoice_summary_master.php', 10, 24, b'1'),
(25, 4, 'invoice_gst_summary', 'Invoice GST Summary', 'frm_invoice_gst_summary_master.php', 10, 25, b'1'),
(26, 4, 'inward_outward_summary', 'Inward Outward Summary', 'frm_inward_outward_summary_master.php', 9, 26, b'1'),
(27, 4, 'rent_valuation', 'Rent Valuation', 'frm_rent_valuation_master.php', 13, 27, b'1'),
(28, 4, 'party_wise_inward_balance', 'Party wise Inward Balance', 'frm_party_wise_inward_balance_master.php', 9, 28, b'1'),
(29, 4, 'item_stock', 'Item Stock', 'frm_item_stock_master.php', 11, 29, b'1'),
(30, 4, 'item_stock_statement', 'Item Stock Statement', 'frm_item_stock_statement_master.php', 11, 30, b'1'),
(31, 4, 'lot_statement', 'Lot Statement', 'frm_lot_statement_master.php', 12, 31, b'1'),
(32, 4, 'lot_transfer_history', 'Lot Transfer History', 'frm_lot_transfer_history_master.php', 12, 32, b'1'),
(33, 4, 'location_detail_view', 'Location Detail View', 'frm_location_detail_view_master.php', 13, 33, b'1'),
(34, 4, 'item_preservation_charges_list', 'Item Preservation Charges List', 'frm_item_preservation_charges_list_master.php', 11, 34, b'1'),
(35, 4, 'yearly_stock_report', 'Yearly Stock Report', 'frm_yearly_stock_report_master.php', 11, 35, b'1'),
(36, 4, 'location_change_history', 'Location Change History', 'frm_location_change_history_master.php', 13, 36, b'1'),
(37, 5, 'receipt', 'Receipt', 'frm_receipt_master.php', 16, 37, b'1'),
(38, 5, 'payment', 'Payment', 'frm_payment_master.php', 16, 38, b'1'),
(39, 5, 'contra', 'Contra', 'frm_contra_master.php', 17, 39, b'1'),
(40, 5, 'journal', 'Journal', 'frm_journal_master.php', 17, 40, b'1'),
(41, 5, 'day_book', 'Day Book', 'frm_day_book_master.php', 18, 41, b'1'),
(42, 5, 'account_ledger', 'Account Ledger', 'frm_account_ledger_master.php', 18, 42, b'1'),
(43, 5, 'net_payable_outstanding', 'Net Payable Outstanding', 'frm_net_payable_outstanding_master.php', 19, 43, b'1'),
(44, 5, 'net_receivable_outstanding', 'Net Receivable Outstanding', 'frm_net_receivable_outstanding_master.php', 19, 44, b'1'),
(45, 6, 'user', 'User', 'frm_user_master.php', 1, 45, b'1'),
(46, 6, 'user_right', 'User Right Access', 'frm_user_right_master.php', 1, 46, b'1'),
(47, 6, 'company_year', 'Company Year', 'frm_company_year_master.php', 2, 47, b'1'),
(48, 6, 'company', 'Company', 'frm_company_master.php', 2, 48, b'1'),
(49, 6, 'module', 'Module', 'frm_module_master.php', 3, 49, b'1'),
(51, 6, 'menu', 'Menu', 'frm_menu_master.php', 3, 50, b'1'),
(52, 1, 'switchyear', 'Switch Year', 'srh_switch_year_master.php', 5, 51, b'1');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu_right_master`
--

CREATE TABLE `tbl_menu_right_master` (
  `menu_right_id` int(11) UNSIGNED NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `right_name` varchar(10) DEFAULT NULL,
  `right_text` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_menu_right_master`
--

INSERT INTO `tbl_menu_right_master` (`menu_right_id`, `menu_id`, `right_name`, `right_text`) VALUES
(1, 1, 'view', 'View'),
(2, 1, 'add', 'Add'),
(3, 1, 'edit', 'Edit'),
(4, 1, 'delete', 'Delete'),
(5, 1, 'excel', 'Excel'),
(6, 2, 'view', 'View'),
(7, 2, 'add', 'Add'),
(8, 2, 'edit', 'Edit'),
(9, 2, 'delete', 'Delete'),
(10, 2, 'excel', 'Excel'),
(11, 3, 'view', 'View'),
(12, 3, 'add', 'Add'),
(13, 3, 'edit', 'Edit'),
(14, 3, 'delete', 'Delete'),
(15, 3, 'excel', 'Excel'),
(16, 4, 'view', 'View'),
(17, 4, 'add', 'Add'),
(18, 4, 'edit', 'Edit'),
(19, 4, 'delete', 'Delete'),
(20, 4, 'excel', 'Excel'),
(21, 5, 'view', 'View'),
(22, 5, 'add', 'Add'),
(23, 5, 'edit', 'Edit'),
(24, 5, 'delete', 'Delete'),
(25, 5, 'excel', 'Excel'),
(26, 6, 'view', 'View'),
(27, 6, 'add', 'Add'),
(28, 6, 'edit', 'Edit'),
(29, 6, 'delete', 'Delete'),
(30, 6, 'excel', 'Excel'),
(31, 7, 'view', 'View'),
(32, 7, 'add', 'Add'),
(33, 7, 'edit', 'Edit'),
(34, 7, 'delete', 'Delete'),
(35, 7, 'excel', 'Excel'),
(36, 8, 'view', 'View'),
(37, 8, 'add', 'Add'),
(38, 8, 'edit', 'Edit'),
(39, 8, 'delete', 'Delete'),
(40, 8, 'excel', 'Excel'),
(41, 9, 'view', 'View'),
(42, 9, 'add', 'Add'),
(43, 9, 'edit', 'Edit'),
(44, 9, 'delete', 'Delete'),
(45, 9, 'excel', 'Excel'),
(46, 10, 'view', 'View'),
(47, 10, 'add', 'Add'),
(48, 10, 'edit', 'Edit'),
(49, 10, 'delete', 'Delete'),
(50, 10, 'excel', 'Excel'),
(51, 11, 'view', 'View'),
(52, 11, 'add', 'Add'),
(53, 11, 'edit', 'Edit'),
(54, 11, 'delete', 'Delete'),
(55, 11, 'excel', 'Excel'),
(56, 12, 'view', 'View'),
(57, 12, 'add', 'Add'),
(58, 12, 'edit', 'Edit'),
(59, 12, 'delete', 'Delete'),
(60, 12, 'excel', 'Excel'),
(61, 13, 'view', 'View'),
(62, 13, 'add', 'Add'),
(63, 13, 'edit', 'Edit'),
(64, 13, 'delete', 'Delete'),
(65, 13, 'excel', 'Excel'),
(66, 14, 'view', 'View'),
(67, 14, 'add', 'Add'),
(68, 14, 'edit', 'Edit'),
(69, 14, 'delete', 'Delete'),
(70, 14, 'excel', 'Excel'),
(71, 52, 'view', 'View'),
(72, 52, 'add', 'Add'),
(73, 52, 'edit', 'Edit'),
(74, 52, 'delete', 'Delete'),
(75, 52, 'excel', 'Excel'),
(76, 15, 'view', 'View'),
(77, 15, 'add', 'Add'),
(78, 15, 'edit', 'Edit'),
(79, 15, 'delete', 'Delete'),
(80, 15, 'excel', 'Excel'),
(81, 16, 'view', 'View'),
(82, 16, 'add', 'Add'),
(83, 16, 'edit', 'Edit'),
(84, 16, 'delete', 'Delete'),
(85, 16, 'excel', 'Excel'),
(86, 17, 'view', 'View'),
(87, 17, 'add', 'Add'),
(88, 17, 'edit', 'Edit'),
(89, 17, 'delete', 'Delete'),
(90, 17, 'excel', 'Excel'),
(91, 18, 'view', 'View'),
(92, 18, 'add', 'Add'),
(93, 18, 'edit', 'Edit'),
(94, 18, 'delete', 'Delete'),
(95, 18, 'excel', 'Excel'),
(96, 19, 'view', 'View'),
(97, 19, 'add', 'Add'),
(98, 19, 'edit', 'Edit'),
(99, 19, 'delete', 'Delete'),
(100, 19, 'excel', 'Excel'),
(101, 20, 'view', 'View'),
(102, 20, 'add', 'Add'),
(103, 20, 'edit', 'Edit'),
(104, 20, 'delete', 'Delete'),
(105, 20, 'excel', 'Excel'),
(106, 21, 'view', 'View'),
(107, 21, 'add', 'Add'),
(108, 21, 'edit', 'Edit'),
(109, 21, 'delete', 'Delete'),
(110, 21, 'excel', 'Excel'),
(111, 22, 'view', 'View'),
(112, 22, 'add', 'Add'),
(113, 22, 'edit', 'Edit'),
(114, 22, 'delete', 'Delete'),
(115, 22, 'excel', 'Excel'),
(116, 23, 'view', 'View'),
(117, 23, 'add', 'Add'),
(118, 23, 'edit', 'Edit'),
(119, 23, 'delete', 'Delete'),
(120, 23, 'excel', 'Excel'),
(121, 24, 'view', 'View'),
(122, 24, 'add', 'Add'),
(123, 24, 'edit', 'Edit'),
(124, 24, 'delete', 'Delete'),
(125, 24, 'excel', 'Excel'),
(126, 25, 'view', 'View'),
(127, 25, 'add', 'Add'),
(128, 25, 'edit', 'Edit'),
(129, 25, 'delete', 'Delete'),
(130, 25, 'excel', 'Excel'),
(131, 26, 'view', 'View'),
(132, 26, 'add', 'Add'),
(133, 26, 'edit', 'Edit'),
(134, 26, 'delete', 'Delete'),
(135, 26, 'excel', 'Excel'),
(136, 27, 'view', 'View'),
(137, 27, 'add', 'Add'),
(138, 27, 'edit', 'Edit'),
(139, 27, 'delete', 'Delete'),
(140, 27, 'excel', 'Excel'),
(141, 28, 'view', 'View'),
(142, 28, 'add', 'Add'),
(143, 28, 'edit', 'Edit'),
(144, 28, 'delete', 'Delete'),
(145, 28, 'excel', 'Excel'),
(146, 29, 'view', 'View'),
(147, 29, 'add', 'Add'),
(148, 29, 'edit', 'Edit'),
(149, 29, 'delete', 'Delete'),
(150, 29, 'excel', 'Excel'),
(151, 30, 'view', 'View'),
(152, 30, 'add', 'Add'),
(153, 30, 'edit', 'Edit'),
(154, 30, 'delete', 'Delete'),
(155, 30, 'excel', 'Excel'),
(156, 31, 'view', 'View'),
(157, 31, 'add', 'Add'),
(158, 31, 'edit', 'Edit'),
(159, 31, 'delete', 'Delete'),
(160, 31, 'excel', 'Excel'),
(161, 32, 'view', 'View'),
(162, 32, 'add', 'Add'),
(163, 32, 'edit', 'Edit'),
(164, 32, 'delete', 'Delete'),
(165, 32, 'excel', 'Excel'),
(166, 33, 'view', 'View'),
(167, 33, 'add', 'Add'),
(168, 33, 'edit', 'Edit'),
(169, 33, 'delete', 'Delete'),
(170, 33, 'excel', 'Excel'),
(171, 34, 'view', 'View'),
(172, 34, 'add', 'Add'),
(173, 34, 'edit', 'Edit'),
(174, 34, 'delete', 'Delete'),
(175, 34, 'excel', 'Excel'),
(176, 35, 'view', 'View'),
(177, 35, 'add', 'Add'),
(178, 35, 'edit', 'Edit'),
(179, 35, 'delete', 'Delete'),
(180, 35, 'excel', 'Excel'),
(181, 36, 'view', 'View'),
(182, 36, 'add', 'Add'),
(183, 36, 'edit', 'Edit'),
(184, 36, 'delete', 'Delete'),
(185, 36, 'excel', 'Excel'),
(186, 37, 'view', 'View'),
(187, 37, 'add', 'Add'),
(188, 37, 'edit', 'Edit'),
(189, 37, 'delete', 'Delete'),
(190, 37, 'excel', 'Excel'),
(191, 38, 'view', 'View'),
(192, 38, 'add', 'Add'),
(193, 38, 'edit', 'Edit'),
(194, 38, 'delete', 'Delete'),
(195, 38, 'excel', 'Excel'),
(196, 39, 'view', 'View'),
(197, 39, 'add', 'Add'),
(198, 39, 'edit', 'Edit'),
(199, 39, 'delete', 'Delete'),
(200, 39, 'excel', 'Excel'),
(201, 40, 'view', 'View'),
(202, 40, 'add', 'Add'),
(203, 40, 'edit', 'Edit'),
(204, 40, 'delete', 'Delete'),
(205, 40, 'excel', 'Excel'),
(206, 41, 'view', 'View'),
(207, 41, 'add', 'Add'),
(208, 41, 'edit', 'Edit'),
(209, 41, 'delete', 'Delete'),
(210, 41, 'excel', 'Excel'),
(211, 42, 'view', 'View'),
(212, 42, 'add', 'Add'),
(213, 42, 'edit', 'Edit'),
(214, 42, 'delete', 'Delete'),
(215, 42, 'excel', 'Excel'),
(216, 43, 'view', 'View'),
(217, 43, 'add', 'Add'),
(218, 43, 'edit', 'Edit'),
(219, 43, 'delete', 'Delete'),
(220, 43, 'excel', 'Excel'),
(221, 44, 'view', 'View'),
(222, 44, 'add', 'Add'),
(223, 44, 'edit', 'Edit'),
(224, 44, 'delete', 'Delete'),
(225, 44, 'excel', 'Excel'),
(226, 45, 'view', 'View'),
(227, 45, 'add', 'Add'),
(228, 45, 'edit', 'Edit'),
(229, 45, 'delete', 'Delete'),
(230, 45, 'excel', 'Excel'),
(231, 46, 'view', 'View'),
(232, 46, 'add', 'Add'),
(233, 46, 'edit', 'Edit'),
(234, 46, 'delete', 'Delete'),
(235, 46, 'excel', 'Excel'),
(236, 47, 'view', 'View'),
(237, 47, 'add', 'Add'),
(238, 47, 'edit', 'Edit'),
(239, 47, 'delete', 'Delete'),
(240, 47, 'excel', 'Excel'),
(241, 48, 'view', 'View'),
(243, 48, 'edit', 'Edit'),
(245, 48, 'excel', 'Excel'),
(246, 49, 'view', 'View'),
(247, 49, 'add', 'Add'),
(248, 49, 'edit', 'Edit'),
(249, 49, 'delete', 'Delete'),
(250, 49, 'excel', 'Excel'),
(251, 51, 'view', 'View'),
(252, 51, 'add', 'Add'),
(253, 51, 'edit', 'Edit'),
(254, 51, 'delete', 'Delete'),
(255, 51, 'excel', 'Excel');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_module_master`
--

CREATE TABLE `tbl_module_master` (
  `module_id` int(11) UNSIGNED NOT NULL,
  `module_name` varchar(100) DEFAULT NULL,
  `module_text` varchar(100) DEFAULT NULL,
  `tab_index` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_module_master`
--

INSERT INTO `tbl_module_master` (`module_id`, `module_name`, `module_text`, `tab_index`) VALUES
(1, 'master', 'Master', 1),
(2, 'transaction', 'Transaction', 2),
(3, 'utilities', 'Utilities', 3),
(4, 'report', 'Report', 4),
(5, 'accounting', 'Accounting', 5),
(6, 'admin', 'Admin', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_outward_detail`
--

CREATE TABLE `tbl_outward_detail` (
  `outward_detail_id` bigint(11) NOT NULL,
  `outward_id` bigint(20) UNSIGNED NOT NULL,
  `inward_detail_id` bigint(20) UNSIGNED NOT NULL,
  `out_qty` decimal(18,2) DEFAULT NULL,
  `out_wt` decimal(18,2) DEFAULT NULL,
  `loading_charges` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_outward_detail`
--

INSERT INTO `tbl_outward_detail` (`outward_detail_id`, `outward_id`, `inward_detail_id`, `out_qty`, `out_wt`, `loading_charges`) VALUES
(1, 1, 1, 20.00, 20.00, 2.00),
(2, 1, 10, 4.00, 48.00, 1.00),
(3, 1, 9, 10.00, 10.00, 1.00),
(4, 2, 3, 10.00, 120.00, 1.00),
(5, 2, 5, 12.00, 12.00, 2.00),
(6, 3, 7, 20.00, 20.00, 2.00),
(7, 1, 6, 2.00, 6.00, 7.00),
(8, 1, 2, 50.00, 150.00, 7.00),
(9, 2, 11, 4.00, 48.00, 1.00),
(10, 1, 12, 557.00, 557.00, 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_outward_master`
--

CREATE TABLE `tbl_outward_master` (
  `outward_id` bigint(20) UNSIGNED NOT NULL,
  `outward_sequence` int(11) NOT NULL,
  `outward_no` varchar(100) DEFAULT NULL,
  `outward_date` date NOT NULL,
  `customer` int(11) UNSIGNED DEFAULT NULL,
  `total_qty` int(11) DEFAULT NULL,
  `total_wt` decimal(18,2) DEFAULT NULL,
  `gross_wt` decimal(18,2) DEFAULT NULL,
  `tare_wt` decimal(18,2) DEFAULT NULL,
  `net_wt` decimal(18,2) DEFAULT NULL,
  `loading_expense` decimal(18,2) DEFAULT NULL,
  `other_expense1` decimal(18,2) DEFAULT NULL,
  `other_expense2` decimal(18,2) DEFAULT NULL,
  `outward_order_by` int(11) DEFAULT NULL,
  `delivery_to` varchar(100) DEFAULT NULL,
  `vehicle_no` varchar(500) DEFAULT NULL,
  `driver_name` varchar(500) DEFAULT NULL,
  `driver_mob_no` varchar(500) DEFAULT NULL,
  `transporter` varchar(500) DEFAULT NULL,
  `sp_note` varchar(8000) DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `company_year_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_outward_master`
--

INSERT INTO `tbl_outward_master` (`outward_id`, `outward_sequence`, `outward_no`, `outward_date`, `customer`, `total_qty`, `total_wt`, `gross_wt`, `tare_wt`, `net_wt`, `loading_expense`, `other_expense1`, `other_expense2`, `outward_order_by`, `delivery_to`, `vehicle_no`, `driver_name`, `driver_mob_no`, `transporter`, `sp_note`, `created_by`, `created_date`, `modified_by`, `modified_date`, `company_id`, `company_year_id`) VALUES
(1, 1, '0001/25-26', '2025-08-05', 2, 643, 791.00, 45.00, 5.00, 40.00, 975.00, 5.00, 5.00, 2, 'Hetu', '555', 'manu', '9665656565', '5', 'good', 1, '2025-06-25 10:09:48', 1, '2025-06-26 16:03:42', 1, 22),
(2, 2, '0002/25-26', '2025-06-26', 4, 26, 180.00, 78.00, 8.00, 70.00, 38.00, 2.00, 8.00, 4, 'Drashti chavda', '8', 'hetu', '8383939389', '8', 'hetu', 1, '2025-06-25 10:10:39', 1, '2025-06-26 15:59:51', 1, 22),
(3, 3, '0003/25-26', '2025-06-25', 4, 20, 20.00, 45.00, 5.00, 40.00, 40.00, 5.00, 5.00, 4, 'Drashti chavda', '555', 'drashti', '9845654525', '1', 'mmm', 1, '2025-06-25 10:11:15', 1, '2025-06-25 10:11:15', 1, 22);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_packing_unit_master`
--

CREATE TABLE `tbl_packing_unit_master` (
  `packing_unit_id` int(11) UNSIGNED NOT NULL,
  `packing_unit_name` varchar(100) DEFAULT NULL,
  `conversion_factor` decimal(18,3) DEFAULT NULL,
  `unloading_charge` decimal(18,2) DEFAULT NULL,
  `loading_charge` decimal(18,2) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_packing_unit_master`
--

INSERT INTO `tbl_packing_unit_master` (`packing_unit_id`, `packing_unit_name`, `conversion_factor`, `unloading_charge`, `loading_charge`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(1, 'Box', 1.000, 12.00, 2.00, 1, '2025-04-08 16:29:17', 1, '2025-06-07 10:43:03', 1, 1),
(2, 'bag10KG', 3.000, 6.00, 7.00, 1, '2025-04-08 16:29:27', 1, '2025-04-24 11:44:27', 1, 2),
(3, 'watch', 120.000, 12.00, 121.00, 2, '2025-04-23 12:35:28', NULL, '2025-05-28 14:27:42', 1, 2),
(4, 'bag 20kg', 12.000, 20.00, 1.00, 1, '2025-04-26 15:15:12', NULL, '2025-06-07 14:02:05', 1, 1),
(5, 'bag 40kg', 1.000, 1.00, 1.00, 1, '2025-05-02 16:28:03', NULL, '2025-06-11 15:19:41', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rent_invoice_detail`
--

CREATE TABLE `tbl_rent_invoice_detail` (
  `rent_invoice_detail_id` int(11) UNSIGNED NOT NULL,
  `rent_invoice_id` int(11) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `unit` varchar(500) DEFAULT NULL,
  `weight` decimal(18,2) DEFAULT NULL,
  `rate_per_unit` decimal(18,2) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `remark` varchar(1000) DEFAULT NULL,
  `inward_no` int(11) DEFAULT NULL,
  `inward_date` date DEFAULT NULL,
  `lot_no` int(11) DEFAULT NULL,
  `item` int(11) DEFAULT NULL,
  `marko` int(11) DEFAULT NULL,
  `invoice_qty` int(11) DEFAULT NULL,
  `unit_name` int(11) DEFAULT NULL,
  `wt_per_kg` int(11) DEFAULT NULL,
  `storage_duration` int(11) DEFAULT NULL,
  `rent_per_storage_duration` int(11) DEFAULT NULL,
  `rent_per` int(11) DEFAULT NULL,
  `outward_date` date DEFAULT NULL,
  `charges_from` date DEFAULT NULL,
  `charges_to` date DEFAULT NULL,
  `actual_month` int(11) DEFAULT NULL,
  `actual_day` int(11) DEFAULT NULL,
  `invoice_month` int(11) DEFAULT NULL,
  `invoice_day` int(11) DEFAULT NULL,
  `invoice_amount` int(11) DEFAULT NULL,
  `invoice_for` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rent_invoice_master`
--

CREATE TABLE `tbl_rent_invoice_master` (
  `rent_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_type` int(11) DEFAULT NULL,
  `rent_invoice_sequence` int(11) DEFAULT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `billing_till_date` date DEFAULT NULL,
  `debit_cash` int(20) DEFAULT NULL,
  `customer` int(11) UNSIGNED DEFAULT NULL,
  `invoice_for` int(11) DEFAULT NULL,
  `hsn_code` int(11) DEFAULT NULL,
  `grace_days` int(11) DEFAULT NULL,
  `lot_no` varchar(100) DEFAULT NULL,
  `basic_amount` decimal(18,2) DEFAULT NULL,
  `unloading_exp` decimal(18,2) DEFAULT NULL,
  `loading_exp` decimal(18,2) DEFAULT NULL,
  `other_expense3` decimal(18,2) DEFAULT NULL,
  `tax_amount` int(11) DEFAULT NULL,
  `sgst` int(11) DEFAULT NULL,
  `cgst` int(11) DEFAULT NULL,
  `igst` int(11) DEFAULT NULL,
  `net_amount` decimal(10,0) DEFAULT NULL,
  `sp_note` varchar(8000) DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `company_year_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_rent_invoice_master`
--

INSERT INTO `tbl_rent_invoice_master` (`rent_invoice_id`, `invoice_type`, `rent_invoice_sequence`, `invoice_no`, `invoice_date`, `billing_till_date`, `debit_cash`, `customer`, `invoice_for`, `hsn_code`, `grace_days`, `lot_no`, `basic_amount`, `unloading_exp`, `loading_exp`, `other_expense3`, `tax_amount`, `sgst`, `cgst`, `igst`, `net_amount`, `sp_note`, `created_by`, `created_date`, `modified_by`, `modified_date`, `company_id`, `company_year_id`) VALUES
(1, 2, 1, '0001/25-26', '2025-06-26', '2025-06-26', 1, 4, 1, 1, 0, '9', 1.00, 21.00, 12.00, 21.00, 2, 0, 0, 0, 2, '21', 1, NULL, 1, '2025-06-26 14:49:23', 1, 22),
(2, 2, 2, '0002/25-26', '2025-06-26', '2025-06-26', 0, 4, 1, 1, 0, '9', 0.00, 0.00, 0.00, 0.00, 0, 0, 0, 0, 0, '', 1, NULL, 1, '2025-06-26 15:28:29', 1, 22),
(3, 2, 3, '0003/25-26', '2025-06-26', '2025-06-26', 1, 2, 1, 1, 786, '1,77', 0.00, 0.00, 0.00, 0.00, 0, 0, 0, 0, 0, '', 1, NULL, 1, '2025-06-26 16:08:37', 1, 22);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_state_master`
--

CREATE TABLE `tbl_state_master` (
  `state_id` int(11) UNSIGNED NOT NULL,
  `state_name` varchar(100) DEFAULT NULL,
  `country_id` int(11) UNSIGNED DEFAULT NULL,
  `gst_code` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_state_master`
--

INSERT INTO `tbl_state_master` (`state_id`, `state_name`, `country_id`, `gst_code`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(2, 'Goa1', 4, '2D45G56', '2025-04-10 15:08:30', 1, '2025-05-21 16:28:29', 1, 1),
(4, 'Bihar', 1, '123', '2025-04-23 12:06:29', 1, '2025-05-20 16:11:04', 1, 1),
(5, 'gondal', 1, '12', '2025-04-26 15:35:03', 1, '2025-05-20 16:11:06', 1, 2),
(6, 'Goa', 3, '2323', '2025-05-19 13:06:16', 1, '2025-05-20 16:29:56', 2, 2),
(8, 'Gujarat', 1, '1123', '0000-00-00 00:00:00', 1, '2025-05-19 16:08:54', 1, 1),
(9, 'Rajasthan', 1, '12312', '2025-05-20 18:20:39', 1, '2025-06-24 10:30:05', 1, 1),
(35, 'Gujarat', 13, '12312', '2025-06-23 16:01:06', 1, '2025-06-23 16:01:06', 1, 1),
(36, 'Karnataka', 3, '12312', '2025-06-23 16:47:03', 1, '2025-06-23 16:47:03', 1, 1),
(37, 'Goa2', 6, '12312', '2025-06-23 17:16:06', 1, '2025-06-24 10:30:17', 1, 1),
(38, 'Karnataka34', 19, '123122', '2025-06-23 17:40:36', 1, '2025-06-23 17:40:36', 1, 1),
(39, 'Gujarat8', 3, '12312', '2025-06-24 10:30:39', 1, '2025-06-24 10:30:39', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_master`
--

CREATE TABLE `tbl_user_master` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `login_id` varchar(100) DEFAULT NULL,
  `login_pass` varchar(100) DEFAULT NULL,
  `person_name` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `permission_version` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_master`
--

INSERT INTO `tbl_user_master` (`user_id`, `login_id`, `login_pass`, `person_name`, `status`, `permission_version`, `created_date`, `created_by`, `modified_date`, `modified_by`, `company_id`) VALUES
(1, 'admin', 'admin', 'bhumita radadiya', 1, '2025-05-16 11:44:37', '2025-04-03 14:17:09', 1, '2025-05-15 15:12:12', 1, 1),
(2, 'nidhi', 'nidhi', 'Nidhi', 1, '2025-06-11 05:44:15', '2025-04-05 10:49:40', 1, '2025-06-11 11:14:15', 1, 2),
(3, 'bhumita', '12345', 'bhumita', 1, '2025-05-16 12:11:10', '2025-04-26 14:02:41', NULL, '2025-05-16 17:41:10', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_right_master`
--

CREATE TABLE `tbl_user_right_master` (
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `menu_right_id` int(11) UNSIGNED DEFAULT NULL,
  `has_right` bit(1) NOT NULL DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_right_master`
--

INSERT INTO `tbl_user_right_master` (`user_id`, `menu_right_id`, `has_right`) VALUES
(3, 2, b'1'),
(3, 3, b'1'),
(3, 4, b'1'),
(3, 1, b'1'),
(3, 5, b'1'),
(3, 7, b'1'),
(3, 8, b'1'),
(3, 9, b'1'),
(3, 6, b'1'),
(3, 10, b'1'),
(3, 12, b'1'),
(3, 13, b'1'),
(3, 14, b'1'),
(3, 11, b'1'),
(3, 15, b'1'),
(3, 17, b'1'),
(3, 18, b'1'),
(3, 19, b'1'),
(3, 16, b'1'),
(3, 20, b'1'),
(3, 22, b'1'),
(3, 23, b'1'),
(3, 24, b'1'),
(3, 21, b'1'),
(3, 25, b'1'),
(3, 27, b'1'),
(3, 28, b'1'),
(3, 29, b'1'),
(3, 26, b'1'),
(3, 30, b'1'),
(3, 32, b'1'),
(3, 33, b'1'),
(3, 34, b'1'),
(3, 31, b'1'),
(3, 35, b'1'),
(3, 37, b'1'),
(3, 38, b'1'),
(3, 39, b'1'),
(3, 36, b'1'),
(3, 40, b'1'),
(3, 42, b'1'),
(3, 43, b'1'),
(3, 44, b'1'),
(3, 41, b'1'),
(3, 45, b'1'),
(3, 47, b'1'),
(3, 48, b'1'),
(3, 49, b'1'),
(3, 46, b'1'),
(3, 50, b'1'),
(3, 52, b'1'),
(3, 53, b'1'),
(3, 54, b'1'),
(3, 51, b'1'),
(3, 55, b'1'),
(3, 57, b'1'),
(3, 58, b'1'),
(3, 59, b'1'),
(3, 56, b'1'),
(3, 60, b'1'),
(3, 62, b'1'),
(3, 63, b'1'),
(3, 64, b'1'),
(3, 61, b'1'),
(3, 65, b'1'),
(3, 67, b'1'),
(3, 68, b'1'),
(3, 69, b'1'),
(3, 66, b'1'),
(3, 70, b'1'),
(3, 72, b'1'),
(3, 73, b'1'),
(3, 74, b'1'),
(3, 71, b'1'),
(3, 75, b'1'),
(3, 227, b'1'),
(3, 229, b'1'),
(3, 226, b'1'),
(3, 232, b'1'),
(3, 233, b'1'),
(3, 234, b'1'),
(3, 231, b'1'),
(3, 237, b'1'),
(3, 238, b'1'),
(3, 243, b'1'),
(3, 241, b'1'),
(3, 247, b'1'),
(3, 248, b'1'),
(3, 249, b'1'),
(3, 252, b'1'),
(3, 253, b'1'),
(3, 254, b'1'),
(2, 3, b'1'),
(2, 4, b'1'),
(2, 1, b'1'),
(2, 76, b'1'),
(2, 82, b'1'),
(2, 83, b'1'),
(2, 81, b'1'),
(2, 86, b'1'),
(2, 93, b'1'),
(2, 98, b'1'),
(2, 103, b'1'),
(2, 108, b'1'),
(2, 227, b'1'),
(2, 229, b'1'),
(2, 226, b'1'),
(2, 230, b'1'),
(2, 232, b'1'),
(2, 234, b'1'),
(2, 231, b'1'),
(2, 235, b'1'),
(2, 237, b'1'),
(2, 239, b'1'),
(2, 236, b'1'),
(2, 240, b'1'),
(2, 241, b'1'),
(2, 245, b'1'),
(2, 247, b'1'),
(2, 249, b'1'),
(2, 246, b'1'),
(2, 250, b'1'),
(2, 252, b'1'),
(2, 254, b'1'),
(2, 251, b'1'),
(2, 255, b'1');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_company_year_type`
-- (See below for the actual view)
--
CREATE TABLE `view_company_year_type` (
`id` int(1)
,`value` varchar(7)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_customer_type`
-- (See below for the actual view)
--
CREATE TABLE `view_customer_type` (
`id` int(1)
,`value` varchar(8)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_debit_cash`
-- (See below for the actual view)
--
CREATE TABLE `view_debit_cash` (
`id` int(1)
,`value` varchar(5)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_invoice_for`
-- (See below for the actual view)
--
CREATE TABLE `view_invoice_for` (
`id` int(1)
,`value` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_invoice_type`
-- (See below for the actual view)
--
CREATE TABLE `view_invoice_type` (
`id` int(1)
,`value` varchar(14)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_item_gst_type`
-- (See below for the actual view)
--
CREATE TABLE `view_item_gst_type` (
`id` int(1)
,`value` varchar(18)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_rent_type`
-- (See below for the actual view)
--
CREATE TABLE `view_rent_type` (
`value` varchar(8)
,`Lable` varchar(14)
,`id` int(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_status_type`
-- (See below for the actual view)
--
CREATE TABLE `view_status_type` (
`id` int(1)
,`value` varchar(8)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_storage_duration`
-- (See below for the actual view)
--
CREATE TABLE `view_storage_duration` (
`id` int(2)
,`value` varchar(15)
,`Lable` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_tax_amount`
-- (See below for the actual view)
--
CREATE TABLE `view_tax_amount` (
`id` int(1)
,`value` varchar(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_tax_type`
-- (See below for the actual view)
--
CREATE TABLE `view_tax_type` (
`id` int(1)
,`value` varchar(4)
);

-- --------------------------------------------------------

--
-- Structure for view `view_company_year_type`
--
DROP TABLE IF EXISTS `view_company_year_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_company_year_type`  AS SELECT 1 AS `id`, 'Forward' AS `value`union all select 2 AS `id`,'Reverse' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_customer_type`
--
DROP TABLE IF EXISTS `view_customer_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_customer_type`  AS SELECT 1 AS `id`, 'Customer' AS `value`union all select 2 AS `id`,'Broker' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_debit_cash`
--
DROP TABLE IF EXISTS `view_debit_cash`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_debit_cash`  AS SELECT 1 AS `id`, 'Debit' AS `value`union all select 2 AS `id`,'Cash' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_invoice_for`
--
DROP TABLE IF EXISTS `view_invoice_for`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_invoice_for`  AS SELECT 1 AS `id`, 'Regular (Out.+Stock)' AS `value`union all select 2 AS `id`,'Partial Outward wise' AS `value` union all select 3 AS `id`,'Completed Outward' AS `value` union all select 4 AS `id`,'Seasonal' AS `value` union all select 5 AS `id`,'Manual Invoice' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_invoice_type`
--
DROP TABLE IF EXISTS `view_invoice_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_invoice_type`  AS SELECT 1 AS `id`, 'Regular' AS `value`union all select 2 AS `id`,'Tax Invoice' AS `value` union all select 3 AS `id`,'Bill of Supply' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_item_gst_type`
--
DROP TABLE IF EXISTS `view_item_gst_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_item_gst_type`  AS SELECT 1 AS `id`, 'GST Applicable' AS `value`union all select 2 AS `id`,'GST Exempted' AS `value` union all select 3 AS `id`,'GST Not Applicable' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_rent_type`
--
DROP TABLE IF EXISTS `view_rent_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_rent_type`  AS SELECT 'Quantity' AS `value`, 'Total Quantity' AS `Lable`, 1 AS `id`union all select 'Kg' AS `value`,'Total KG' AS `Lable`,2 AS `id`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_status_type`
--
DROP TABLE IF EXISTS `view_status_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_status_type`  AS SELECT 1 AS `id`, 'Active' AS `value`union all select 2 AS `id`,'Deactive' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_storage_duration`
--
DROP TABLE IF EXISTS `view_storage_duration`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_storage_duration`  AS SELECT 1 AS `id`, 'Daily' AS `value`, 'Day' AS `Lable`union all select 2 AS `2`,'Weekly' AS `Weekly`,'Week' AS `7 Days` union all select 3 AS `3`,'Fortnightly' AS `Fortnightly`,'15 Days' AS `15 Days` union all select 4 AS `4`,'Monthly' AS `Monthly`,'Month' AS `Monthly` union all select 5 AS `5`,'1 Month 1 Day' AS `1 Month 1 Day`,'1 Month 1 Day' AS `1 Month 1 Day` union all select 6 AS `6`,'1 Month 7 Days' AS `1 Month 7 Days`,'1 Month 7 Days' AS `1 Month 7 Days` union all select 7 AS `7`,'1 Month 15 Days' AS `1 Month 15 Days`,'1 Month 15 Days' AS `1 Month 15 Days` union all select 8 AS `8`,'2 Months' AS `2 Months`,'2 Months' AS `2 Months` union all select 9 AS `9`,'Seasonal' AS `Seasonal`,'Season' AS `Seasonal` union all select 10 AS `10`,'No Billing' AS `No Billing`,'No Billing' AS `No Billing`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_tax_amount`
--
DROP TABLE IF EXISTS `view_tax_amount`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_tax_amount`  AS SELECT 1 AS `id`, 'None' AS `value`union all select 2 AS `id`,'SGST + CGST' AS `value` union all select 3 AS `id`,'IGST' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_tax_type`
--
DROP TABLE IF EXISTS `view_tax_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_tax_type`  AS SELECT 1 AS `id`, 'SGST' AS `value`union all select 2 AS `id`,'CGST' AS `value` union all select 3 AS `id`,'IGST' AS `value`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_bank_master`
--
ALTER TABLE `tbl_bank_master`
  ADD PRIMARY KEY (`bank_id`),
  ADD KEY `bank_master_created_by` (`created_by`),
  ADD KEY `bank_master_modified_by` (`modified_by`),
  ADD KEY `bank_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_chamber_master`
--
ALTER TABLE `tbl_chamber_master`
  ADD PRIMARY KEY (`chamber_id`),
  ADD KEY `chamber_master_company_id` (`company_id`),
  ADD KEY `chamber_master_created_by` (`created_by`),
  ADD KEY `chamber_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_city_master`
--
ALTER TABLE `tbl_city_master`
  ADD PRIMARY KEY (`city_id`),
  ADD KEY `city_master_country_id` (`country_id`),
  ADD KEY `city_master_state_id` (`state_id`),
  ADD KEY `city_master_created_by` (`created_by`),
  ADD KEY `city_master_modified_by` (`modified_by`),
  ADD KEY `city_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_company_master`
--
ALTER TABLE `tbl_company_master`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `company_master_bank_id` (`bank_id`),
  ADD KEY `company_master_created_by` (`created_by`),
  ADD KEY `company_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_company_year_master`
--
ALTER TABLE `tbl_company_year_master`
  ADD PRIMARY KEY (`company_year_id`),
  ADD KEY `company_year_master_created_by` (`created_by`),
  ADD KEY `company_year_master_modified_by` (`modified_by`),
  ADD KEY `company_year_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_contact_person_detail`
--
ALTER TABLE `tbl_contact_person_detail`
  ADD PRIMARY KEY (`contact_person_id`),
  ADD KEY `contact_person_detail_customer_id` (`customer_id`);

--
-- Indexes for table `tbl_country_master`
--
ALTER TABLE `tbl_country_master`
  ADD PRIMARY KEY (`country_id`),
  ADD KEY `country_master_created_by` (`created_by`),
  ADD KEY `country_master_modified_by` (`modified_by`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `tbl_currency_master`
--
ALTER TABLE `tbl_currency_master`
  ADD PRIMARY KEY (`currency_id`),
  ADD KEY `currency_master_created_by` (`created_by`),
  ADD KEY `currency_master_modified_by` (`modified_by`),
  ADD KEY `currency_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_customer_account_group_master`
--
ALTER TABLE `tbl_customer_account_group_master`
  ADD PRIMARY KEY (`customer_account_group_id`),
  ADD KEY `customer_account_group_master_created_by` (`created_by`),
  ADD KEY `customer_account_group_master_modified_by` (`modified_by`),
  ADD KEY `customer_account_group_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_customer_master`
--
ALTER TABLE `tbl_customer_master`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `state_id` (`state_id`),
  ADD KEY `customer_master_country_id` (`country_id`),
  ADD KEY `customer_master_city_id` (`city_id`),
  ADD KEY `customer_master_customer_account_group_id` (`account_group_id`),
  ADD KEY `customer_master_created_by` (`created_by`),
  ADD KEY `customer_master_modified_by` (`modified_by`),
  ADD KEY `customer_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_customer_wise_item_preservation_price_list_detail`
--
ALTER TABLE `tbl_customer_wise_item_preservation_price_list_detail`
  ADD PRIMARY KEY (`customer_wise_item_preservation_price_list_detail_id`),
  ADD KEY `customer_wise_item_preservation_detail_packing_unit_id` (`packing_unit_id`),
  ADD KEY `customer_wise_item_preservation_master_id` (`customer_wise_item_preservation_price_list_id`);

--
-- Indexes for table `tbl_customer_wise_item_preservation_price_list_master`
--
ALTER TABLE `tbl_customer_wise_item_preservation_price_list_master`
  ADD PRIMARY KEY (`customer_wise_item_preservation_price_list_id`),
  ADD UNIQUE KEY `uq_item_customer_year` (`item_id`,`customer_id`,`company_year_id`,`company_id`) USING BTREE,
  ADD KEY `customer_wise_item_preservation_price_list_created_by` (`created_by`),
  ADD KEY `customer_wise_item_preservation_price_list_customer_id` (`customer_id`),
  ADD KEY `customer_wise_preservation_price_list_company_id` (`company_id`),
  ADD KEY `customer_wise_preservation_price_list_modified_by` (`modified_by`),
  ADD KEY `customer_wise_preservation_price_list_company_year_id` (`company_year_id`);

--
-- Indexes for table `tbl_floor_master`
--
ALTER TABLE `tbl_floor_master`
  ADD PRIMARY KEY (`floor_id`),
  ADD KEY `floor_master_chamber_id` (`chamber_id`),
  ADD KEY `floor_master_created_by` (`created_by`),
  ADD KEY `floor_master_modified_by` (`modified_by`),
  ADD KEY `floor_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_generator_master`
--
ALTER TABLE `tbl_generator_master`
  ADD PRIMARY KEY (`generator_id`);

--
-- Indexes for table `tbl_gst_tax_detail`
--
ALTER TABLE `tbl_gst_tax_detail`
  ADD PRIMARY KEY (`gst_tax_id`),
  ADD KEY `hsn_id` (`hsn_code_id`);

--
-- Indexes for table `tbl_hsn_code_master`
--
ALTER TABLE `tbl_hsn_code_master`
  ADD PRIMARY KEY (`hsn_code_id`),
  ADD KEY `hsn_code_master_created_by` (`created_by`),
  ADD KEY `hsn_code_master_modified_by` (`modified_by`),
  ADD KEY `hsn_code_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_inward_detail`
--
ALTER TABLE `tbl_inward_detail`
  ADD PRIMARY KEY (`inward_detail_id`),
  ADD KEY `inward_detail_item` (`item`),
  ADD KEY `inward_detail_packing_unit` (`packing_unit`),
  ADD KEY `inward_detail_inward_id` (`inward_id`);

--
-- Indexes for table `tbl_inward_master`
--
ALTER TABLE `tbl_inward_master`
  ADD PRIMARY KEY (`inward_id`),
  ADD UNIQUE KEY `inward_no` (`inward_no`),
  ADD UNIQUE KEY `unique_inward_seq_per_year_company` (`company_id`,`company_year_id`,`inward_sequence`),
  ADD KEY `customer` (`customer`),
  ADD KEY `broker` (`broker`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `modified_by` (`modified_by`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `company_year_id` (`company_year_id`);

--
-- Indexes for table `tbl_item_master`
--
ALTER TABLE `tbl_item_master`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `item_master_created_by` (`created_by`),
  ADD KEY `item_master_modified_by` (`modified_by`),
  ADD KEY `item_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_item_preservation_price_list_detail`
--
ALTER TABLE `tbl_item_preservation_price_list_detail`
  ADD PRIMARY KEY (`item_preservation_price_list_detail_id`),
  ADD KEY `item_preservation_detail_master_id` (`item_preservation_price_list_id`),
  ADD KEY `item_preservation_detail_unit_id` (`packing_unit_id`);

--
-- Indexes for table `tbl_item_preservation_price_list_master`
--
ALTER TABLE `tbl_item_preservation_price_list_master`
  ADD PRIMARY KEY (`item_preservation_price_list_id`),
  ADD UNIQUE KEY `uk_item_year` (`item_id`,`company_year_id`,`company_id`) USING BTREE,
  ADD KEY `item_preservation_price_master_company_id` (`company_id`),
  ADD KEY `item_preservation_price_master_created_by` (`created_by`),
  ADD KEY `item_preservation_price_master_modified_by` (`modified_by`),
  ADD KEY `item_preservation_price_master_company_year_id` (`company_year_id`);

--
-- Indexes for table `tbl_menu_master`
--
ALTER TABLE `tbl_menu_master`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `menu_master_module_id` (`module_id`);

--
-- Indexes for table `tbl_menu_right_master`
--
ALTER TABLE `tbl_menu_right_master`
  ADD PRIMARY KEY (`menu_right_id`);

--
-- Indexes for table `tbl_module_master`
--
ALTER TABLE `tbl_module_master`
  ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `tbl_outward_detail`
--
ALTER TABLE `tbl_outward_detail`
  ADD PRIMARY KEY (`outward_detail_id`),
  ADD KEY `outward_detail_inward_detail` (`inward_detail_id`),
  ADD KEY `outward_master_outward_master` (`outward_id`);

--
-- Indexes for table `tbl_outward_master`
--
ALTER TABLE `tbl_outward_master`
  ADD PRIMARY KEY (`outward_id`),
  ADD UNIQUE KEY `outward_no` (`outward_no`),
  ADD KEY `outward_master_customer` (`customer`),
  ADD KEY `outward_master_company_id` (`company_id`),
  ADD KEY `outward_master_created_by` (`created_by`),
  ADD KEY `outward_master_modified_by` (`modified_by`),
  ADD KEY `outward_master_company_year_id` (`company_year_id`);

--
-- Indexes for table `tbl_packing_unit_master`
--
ALTER TABLE `tbl_packing_unit_master`
  ADD PRIMARY KEY (`packing_unit_id`),
  ADD KEY `packing_unit_master_created_by` (`created_by`),
  ADD KEY `packing_unit_master_modified_by` (`modified_by`),
  ADD KEY `packing_unit_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_rent_invoice_detail`
--
ALTER TABLE `tbl_rent_invoice_detail`
  ADD PRIMARY KEY (`rent_invoice_detail_id`);

--
-- Indexes for table `tbl_rent_invoice_master`
--
ALTER TABLE `tbl_rent_invoice_master`
  ADD PRIMARY KEY (`rent_invoice_id`),
  ADD KEY `rent_invoice_master_created_by` (`created_by`),
  ADD KEY `rent_invoice_master_modified_by` (`modified_by`),
  ADD KEY `rent_invoice_master_company_year_id` (`company_year_id`),
  ADD KEY `rent_invoice_master_company_id` (`company_id`),
  ADD KEY `rent_invoice_master_customer` (`customer`);

--
-- Indexes for table `tbl_state_master`
--
ALTER TABLE `tbl_state_master`
  ADD PRIMARY KEY (`state_id`),
  ADD KEY `state_master_country_id` (`country_id`),
  ADD KEY `state_master_created_by` (`created_by`),
  ADD KEY `state_master_modified_by` (`modified_by`),
  ADD KEY `state_master_company_id` (`company_id`);

--
-- Indexes for table `tbl_user_master`
--
ALTER TABLE `tbl_user_master`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `user_master_created_by` (`created_by`),
  ADD KEY `user_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_user_right_master`
--
ALTER TABLE `tbl_user_right_master`
  ADD KEY `menu_right_id` (`menu_right_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_bank_master`
--
ALTER TABLE `tbl_bank_master`
  MODIFY `bank_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_chamber_master`
--
ALTER TABLE `tbl_chamber_master`
  MODIFY `chamber_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_city_master`
--
ALTER TABLE `tbl_city_master`
  MODIFY `city_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_company_master`
--
ALTER TABLE `tbl_company_master`
  MODIFY `company_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_company_year_master`
--
ALTER TABLE `tbl_company_year_master`
  MODIFY `company_year_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_contact_person_detail`
--
ALTER TABLE `tbl_contact_person_detail`
  MODIFY `contact_person_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_country_master`
--
ALTER TABLE `tbl_country_master`
  MODIFY `country_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_currency_master`
--
ALTER TABLE `tbl_currency_master`
  MODIFY `currency_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_customer_account_group_master`
--
ALTER TABLE `tbl_customer_account_group_master`
  MODIFY `customer_account_group_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_customer_master`
--
ALTER TABLE `tbl_customer_master`
  MODIFY `customer_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_customer_wise_item_preservation_price_list_detail`
--
ALTER TABLE `tbl_customer_wise_item_preservation_price_list_detail`
  MODIFY `customer_wise_item_preservation_price_list_detail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `tbl_customer_wise_item_preservation_price_list_master`
--
ALTER TABLE `tbl_customer_wise_item_preservation_price_list_master`
  MODIFY `customer_wise_item_preservation_price_list_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_floor_master`
--
ALTER TABLE `tbl_floor_master`
  MODIFY `floor_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_generator_master`
--
ALTER TABLE `tbl_generator_master`
  MODIFY `generator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `tbl_gst_tax_detail`
--
ALTER TABLE `tbl_gst_tax_detail`
  MODIFY `gst_tax_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `tbl_hsn_code_master`
--
ALTER TABLE `tbl_hsn_code_master`
  MODIFY `hsn_code_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_inward_detail`
--
ALTER TABLE `tbl_inward_detail`
  MODIFY `inward_detail_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_inward_master`
--
ALTER TABLE `tbl_inward_master`
  MODIFY `inward_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_item_master`
--
ALTER TABLE `tbl_item_master`
  MODIFY `item_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_item_preservation_price_list_detail`
--
ALTER TABLE `tbl_item_preservation_price_list_detail`
  MODIFY `item_preservation_price_list_detail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tbl_item_preservation_price_list_master`
--
ALTER TABLE `tbl_item_preservation_price_list_master`
  MODIFY `item_preservation_price_list_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_menu_master`
--
ALTER TABLE `tbl_menu_master`
  MODIFY `menu_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tbl_menu_right_master`
--
ALTER TABLE `tbl_menu_right_master`
  MODIFY `menu_right_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=256;

--
-- AUTO_INCREMENT for table `tbl_module_master`
--
ALTER TABLE `tbl_module_master`
  MODIFY `module_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_outward_detail`
--
ALTER TABLE `tbl_outward_detail`
  MODIFY `outward_detail_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_outward_master`
--
ALTER TABLE `tbl_outward_master`
  MODIFY `outward_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_packing_unit_master`
--
ALTER TABLE `tbl_packing_unit_master`
  MODIFY `packing_unit_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_rent_invoice_detail`
--
ALTER TABLE `tbl_rent_invoice_detail`
  MODIFY `rent_invoice_detail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_rent_invoice_master`
--
ALTER TABLE `tbl_rent_invoice_master`
  MODIFY `rent_invoice_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_state_master`
--
ALTER TABLE `tbl_state_master`
  MODIFY `state_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `tbl_user_master`
--
ALTER TABLE `tbl_user_master`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_bank_master`
--
ALTER TABLE `tbl_bank_master`
  ADD CONSTRAINT ` bank_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `bank_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `bank_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_chamber_master`
--
ALTER TABLE `tbl_chamber_master`
  ADD CONSTRAINT `chamber_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `chamber_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `chamber_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_city_master`
--
ALTER TABLE `tbl_city_master`
  ADD CONSTRAINT `city_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `city_master_country_id` FOREIGN KEY (`country_id`) REFERENCES `tbl_country_master` (`country_id`),
  ADD CONSTRAINT `city_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `city_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `city_master_state_id` FOREIGN KEY (`state_id`) REFERENCES `tbl_state_master` (`state_id`);

--
-- Constraints for table `tbl_company_master`
--
ALTER TABLE `tbl_company_master`
  ADD CONSTRAINT `company_master_bank_id` FOREIGN KEY (`bank_id`) REFERENCES `tbl_bank_master` (`bank_id`),
  ADD CONSTRAINT `company_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `company_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_company_year_master`
--
ALTER TABLE `tbl_company_year_master`
  ADD CONSTRAINT `company_year_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `company_year_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `company_year_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_contact_person_detail`
--
ALTER TABLE `tbl_contact_person_detail`
  ADD CONSTRAINT `contact_person_detail_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `tbl_customer_master` (`customer_id`);

--
-- Constraints for table `tbl_country_master`
--
ALTER TABLE `tbl_country_master`
  ADD CONSTRAINT `country_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `country_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `country_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_currency_master`
--
ALTER TABLE `tbl_currency_master`
  ADD CONSTRAINT `currency_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `currency_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `currency_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_customer_account_group_master`
--
ALTER TABLE `tbl_customer_account_group_master`
  ADD CONSTRAINT `customer_account_group_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `customer_account_group_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `customer_account_group_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_customer_master`
--
ALTER TABLE `tbl_customer_master`
  ADD CONSTRAINT `customer_master_city_id` FOREIGN KEY (`city_id`) REFERENCES `tbl_city_master` (`city_id`),
  ADD CONSTRAINT `customer_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `customer_master_country_id` FOREIGN KEY (`country_id`) REFERENCES `tbl_country_master` (`country_id`),
  ADD CONSTRAINT `customer_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `customer_master_customer_account_group_id` FOREIGN KEY (`account_group_id`) REFERENCES `tbl_customer_account_group_master` (`customer_account_group_id`),
  ADD CONSTRAINT `customer_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `customer_master_state_id` FOREIGN KEY (`state_id`) REFERENCES `tbl_state_master` (`state_id`);

--
-- Constraints for table `tbl_customer_wise_item_preservation_price_list_detail`
--
ALTER TABLE `tbl_customer_wise_item_preservation_price_list_detail`
  ADD CONSTRAINT `customer_wise_item_preservation_detail_packing_unit_id` FOREIGN KEY (`packing_unit_id`) REFERENCES `tbl_packing_unit_master` (`packing_unit_id`),
  ADD CONSTRAINT `customer_wise_item_preservation_master_id` FOREIGN KEY (`customer_wise_item_preservation_price_list_id`) REFERENCES `tbl_customer_wise_item_preservation_price_list_master` (`customer_wise_item_preservation_price_list_id`);

--
-- Constraints for table `tbl_customer_wise_item_preservation_price_list_master`
--
ALTER TABLE `tbl_customer_wise_item_preservation_price_list_master`
  ADD CONSTRAINT `customer_wise_item_preservation_price_list_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `customer_wise_item_preservation_price_list_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `tbl_customer_master` (`customer_id`),
  ADD CONSTRAINT `customer_wise_item_preservation_price_list_item_id` FOREIGN KEY (`item_id`) REFERENCES `tbl_item_master` (`item_id`),
  ADD CONSTRAINT `customer_wise_preservation_price_list_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `customer_wise_preservation_price_list_company_year_id` FOREIGN KEY (`company_year_id`) REFERENCES `tbl_company_year_master` (`company_year_id`),
  ADD CONSTRAINT `customer_wise_preservation_price_list_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_floor_master`
--
ALTER TABLE `tbl_floor_master`
  ADD CONSTRAINT `floor_master_chamber_id` FOREIGN KEY (`chamber_id`) REFERENCES `tbl_chamber_master` (`chamber_id`),
  ADD CONSTRAINT `floor_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `floor_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `floor_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_gst_tax_detail`
--
ALTER TABLE `tbl_gst_tax_detail`
  ADD CONSTRAINT `gst_tax_detail_hsn_code_id` FOREIGN KEY (`hsn_code_id`) REFERENCES `tbl_hsn_code_master` (`hsn_code_id`);

--
-- Constraints for table `tbl_hsn_code_master`
--
ALTER TABLE `tbl_hsn_code_master`
  ADD CONSTRAINT `hsn_code_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `hsn_code_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `hsn_code_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_inward_detail`
--
ALTER TABLE `tbl_inward_detail`
  ADD CONSTRAINT `inward_detail_inward_id` FOREIGN KEY (`inward_id`) REFERENCES `tbl_inward_master` (`inward_id`),
  ADD CONSTRAINT `inward_detail_item` FOREIGN KEY (`item`) REFERENCES `tbl_item_master` (`item_id`),
  ADD CONSTRAINT `inward_detail_packing_unit` FOREIGN KEY (`packing_unit`) REFERENCES `tbl_packing_unit_master` (`packing_unit_id`);

--
-- Constraints for table `tbl_inward_master`
--
ALTER TABLE `tbl_inward_master`
  ADD CONSTRAINT `inward_master_broker` FOREIGN KEY (`broker`) REFERENCES `tbl_customer_master` (`customer_id`),
  ADD CONSTRAINT `inward_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `inward_master_company_year_id` FOREIGN KEY (`company_year_id`) REFERENCES `tbl_company_year_master` (`company_year_id`),
  ADD CONSTRAINT `inward_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `inward_master_customer` FOREIGN KEY (`customer`) REFERENCES `tbl_customer_master` (`customer_id`),
  ADD CONSTRAINT `inward_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_item_master`
--
ALTER TABLE `tbl_item_master`
  ADD CONSTRAINT `item_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `item_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `item_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_item_preservation_price_list_detail`
--
ALTER TABLE `tbl_item_preservation_price_list_detail`
  ADD CONSTRAINT `item_preservation_detail_master_id` FOREIGN KEY (`item_preservation_price_list_id`) REFERENCES `tbl_item_preservation_price_list_master` (`item_preservation_price_list_id`),
  ADD CONSTRAINT `item_preservation_detail_unit_id` FOREIGN KEY (`packing_unit_id`) REFERENCES `tbl_packing_unit_master` (`packing_unit_id`);

--
-- Constraints for table `tbl_item_preservation_price_list_master`
--
ALTER TABLE `tbl_item_preservation_price_list_master`
  ADD CONSTRAINT `item_preservation_price_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `item_preservation_price_master_company_year_id` FOREIGN KEY (`company_year_id`) REFERENCES `tbl_company_year_master` (`company_year_id`),
  ADD CONSTRAINT `item_preservation_price_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `item_preservation_price_master_item_id` FOREIGN KEY (`item_id`) REFERENCES `tbl_item_master` (`item_id`),
  ADD CONSTRAINT `item_preservation_price_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_menu_master`
--
ALTER TABLE `tbl_menu_master`
  ADD CONSTRAINT `menu_master_module_id` FOREIGN KEY (`module_id`) REFERENCES `tbl_module_master` (`module_id`);

--
-- Constraints for table `tbl_outward_detail`
--
ALTER TABLE `tbl_outward_detail`
  ADD CONSTRAINT `outward_detail_inward_detail` FOREIGN KEY (`inward_detail_id`) REFERENCES `tbl_inward_detail` (`inward_detail_id`),
  ADD CONSTRAINT `outward_master_outward_master` FOREIGN KEY (`outward_id`) REFERENCES `tbl_outward_master` (`outward_id`);

--
-- Constraints for table `tbl_outward_master`
--
ALTER TABLE `tbl_outward_master`
  ADD CONSTRAINT `outward_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `outward_master_company_year_id` FOREIGN KEY (`company_year_id`) REFERENCES `tbl_company_year_master` (`company_year_id`),
  ADD CONSTRAINT `outward_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `outward_master_customer` FOREIGN KEY (`customer`) REFERENCES `tbl_customer_master` (`customer_id`),
  ADD CONSTRAINT `outward_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_packing_unit_master`
--
ALTER TABLE `tbl_packing_unit_master`
  ADD CONSTRAINT `packing_unit_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `packing_unit_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `packing_unit_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_rent_invoice_master`
--
ALTER TABLE `tbl_rent_invoice_master`
  ADD CONSTRAINT `rent_invoice_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `rent_invoice_master_company_year_id` FOREIGN KEY (`company_year_id`) REFERENCES `tbl_company_year_master` (`company_year_id`),
  ADD CONSTRAINT `rent_invoice_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `rent_invoice_master_customer` FOREIGN KEY (`customer`) REFERENCES `tbl_customer_master` (`customer_id`),
  ADD CONSTRAINT `rent_invoice_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_state_master`
--
ALTER TABLE `tbl_state_master`
  ADD CONSTRAINT `state_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `state_master_country_id` FOREIGN KEY (`country_id`) REFERENCES `tbl_country_master` (`country_id`),
  ADD CONSTRAINT `state_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `state_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_user_master`
--
ALTER TABLE `tbl_user_master`
  ADD CONSTRAINT `user_master_company_id` FOREIGN KEY (`company_id`) REFERENCES `tbl_company_master` (`company_id`),
  ADD CONSTRAINT `user_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `user_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_user_right_master`
--
ALTER TABLE `tbl_user_right_master`
  ADD CONSTRAINT `user_right_master_menu_right_id` FOREIGN KEY (`menu_right_id`) REFERENCES `tbl_menu_right_master` (`menu_right_id`),
  ADD CONSTRAINT `user_right_master_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user_master` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
