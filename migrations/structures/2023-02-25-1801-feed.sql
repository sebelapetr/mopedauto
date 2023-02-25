ALTER TABLE `products`
    ADD `manufacturer` varchar(100) NOT NULL,
ADD `gtin` varchar(60) NULL AFTER `manufacturer`,
ADD `color` varchar(100) NULL AFTER `gtin`,
ADD `material` varchar(100) NULL AFTER `color`;