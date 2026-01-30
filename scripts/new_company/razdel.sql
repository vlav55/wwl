INSERT INTO `razdel` (`id`, `razdel_name`, `del`) VALUES
(7, 'N', 0),
(1, 'C', 0),
(2, 'B', 0),
(3, 'A', 0), 
(4, 'D', 0),
(5, 'Other', 0),
(6, 'БАН', 0);


-- ----------------

UPDATE razdel SET id=0 WHERE id=7;
