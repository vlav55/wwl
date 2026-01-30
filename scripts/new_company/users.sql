INSERT INTO `users` (`id`, `0ctrl`, `username`, `real_user_name`, `tg_nick`, `wa_user_name`, `email_from_name`, `email`, `uid`, `sip`, `callback_url`, `acc_id`, `klid`, `gk_code`, `passw`, `token`, `access_level`, `tm_lastlogin`, `comm`, `telegram_id`, `pact_channel_id`, `fl_notify_if_new`, `fl_notify_if_other`, `fl_notify_of_own_only`, `fl_allowlogin`, `del`, `pact_phone`, `garant`, `pact_company_id`, `pact_token`, `pact_channel_online`, `fb_pixel`) VALUES
(1, 0, 'n/a', '', '', '', '', '', 0, 0, '', 0, 0, '', '', '', 0, 0, '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0),
(2, 0, 'vlav', '', '', '', 'VKTRADE', '', 0, 0, '', 0, 0, '', 'e70f7a0d2ddf3dc3efe9758a271c2779', '', 1, 1664915864, '', 315058329, 124160, 1, 1, 0, 1, 0, '', 0, 0, '', 0, 0),
(3, 0, 'admin', '', '', '', '', '', 0, 0, '', 0, 0, '', '21232f297a57a5a743894a0e4a801fc3', '', 3, 0, 'admin', 0, 0, 1, 1, 0, 1, 0, '', 0, 0, '', 0, 0);

----------------
UPDATE users SET id=0 WHERE id=1

