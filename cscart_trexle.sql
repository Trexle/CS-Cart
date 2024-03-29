# ! Make sure table prefix is the same 

--------------------------------
-- Install Payment Processors --
--------------------------------
# 1005 is the ID - CS-Cart recommends >1000 so 1005 should be safe
INSERT INTO `cscart_payment_processors` (`processor_id`, `processor`, `processor_script`, `processor_template`, `admin_template`, `callback`, `type`)
VALUES
	(1005, 'Trexle', 'trexle.php', 'views/orders/components/payments/cc_trexle.tpl', 'trexle.tpl', 'Y', 'P');

------------------------
-- Language Variables --
------------------------
# Only for English... Adapt if necessary

INSERT INTO cscart_language_values (`lang_code`, `name`, `value`) VALUES ('en', 'card_token', 'Card Token'), ('en', 'card_name', 'Card Name');