INSERT INTO `razdel` (`id`, `razdel_name`,`fl_not_send`, `del`) VALUES
(7, 'N', 0,1),
(1, 'C', 0,0),
(2, 'B', 0,0),
(3, 'A', 0,0), 
(4, 'D', 0,0),
(5, 'Other', 0,1),
(6, 'БАН', 0,1);


-- ----------------

UPDATE razdel SET id=0 WHERE id=7;
