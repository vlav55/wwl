<?php
   // Подключаем автозагрузчик Composer
      require_once '/var/www/vlav/data/www/wwl/inc/mpdf/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML(file_get_contents("../invoices/invoice_template.html"));
$mpdf->Output();
exit;
   // Используем пространство имен mpdf
  // use Mpdf\Mpdf;

   // HTML-код, который нужно сконвертировать в PDF
   $html = '
   <!DOCTYPE html>
   <html lang="ru">
   <head>
       <meta charset="UTF-8">
       <title>Пример HTML в PDF</title>
       <style>
           body {
               font-family: Arial, sans-serif;
               line-height: 1.6;
           }
           h1 {
               color: #333;
           }
       </style>
   </head>
   <body>
       <h1>Документ в PDF</h1>
       <p>Этот документ был создан из HTML с использованием библиотеки mpdf.</p>
   </body>
   </html>
   ';

   // Создаем новый экземпляр mpdf
   $mpdf = new Mpdf();

   // Загружаем HTML в mpdf
   $mpdf->WriteHTML($html);

   // Выводим PDF в браузере или сохраняем в файл
   $mpdf->Output('document.pdf', 'D'); // D для загрузки в браузер, F для сохранения на сервере

   // Останавливаем скрипт
   exit;
   
?>
