-- 08-01-2026 -- 
ALTER TABLE `tc_inspection` CHANGE `result` `result` VARCHAR(50) NOT NULL DEFAULT '0.000';
ALTER TABLE `purchase_order_trans` ADD `optional_unit_id` INT NOT NULL DEFAULT '0' AFTER `unit_id`;
