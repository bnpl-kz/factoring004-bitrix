CREATE TABLE IF NOT EXISTS `bnpl_payment_orders`
(
    `ID` int(11) NOT NULL,
    `ORDER_ID` int(11) NOT NULL,
    `STATUS` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `CREATED_AT` datetime NOT NULL,
    `UPDATED_AT` datetime NOT NULL
);

CREATE TABLE IF NOT EXISTS `bnpl_payment_order_preapps`
(
    `ID` int(11) NOT NULL,
    `PREAPP_UID` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `ORDER_ID` int(11) NOT NULL
);

ALTER TABLE `bnpl_payment_orders`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `bnpl_payment_order_preapps`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `PREAPP_UID` (`PREAPP_UID`);

ALTER TABLE `bnpl_payment_orders`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bnpl_payment_order_preapps`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;