<?php
// yakala.php
// Bu script yerel portu dinler ve gelen mail şifresini ekrana basar.

// 1. Ayarlar
$host = '127.0.0.1';
$port = 8888; // Boş olan herhangi bir port

// 2. Soket oluştur
set_time_limit(0);
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_bind($sock, $host, $port) or die("Port bağlanamadı ($port). Başka port deneyin.\n");
socket_listen($sock, 3) or die("Dinlenemiyor\n");

echo "========================================\n";
echo "📡  DİNLEME MODU AKTİF ($host:$port)\n";
echo "📨  Şimdi Faveo panelinden bir mail gönderin...\n";
echo "========================================\n\n";

// 3. Bağlantı bekle
$client = socket_accept($sock);

// 4. SMTP Konuşmasını Taklit Et
$msg = "220 FakeMail v1.0\r\n"; 
socket_write($client, $msg, strlen($msg));

// Gelen verileri oku
while (true) {
    $input = socket_read($client, 1024);
    $input = trim($input);
    
    // Gelen komutu göster (Debug için)
    // echo "Gelen: $input\n"; 

    if (strpos($input, 'EHLO') === 0 || strpos($input, 'HELO') === 0) {
        // Merhabalaşma
        $output = "250-Hello\r\n250 AUTH LOGIN\r\n";
        socket_write($client, $output, strlen($output));
    } 
    elseif ($input == 'AUTH LOGIN') {
        // Kullanıcı adı iste
        $output = "334 VXNlcm5hbWU6\r\n"; // Base64 'Username:'
        socket_write($client, $output, strlen($output));
        
        // Kullanıcı adını al ve çöz
        $userBase64 = trim(socket_read($client, 1024));
        echo "👤 KULLANICI: " . base64_decode($userBase64) . "\n";
        
        // Şifre iste
        $output = "334 UGFzc3dvcmQ6\r\n"; // Base64 'Password:'
        socket_write($client, $output, strlen($output));
        
        // ŞİFREYİ AL VE ÇÖZ!
        $passBase64 = trim(socket_read($client, 1024));
        echo "🔑 ŞİFRE:    " . base64_decode($passBase64) . "\n";
        
        echo "\n✅ ŞİFRE YAKALANDI! İşlem tamam.\n";
        break;
    }
}

socket_close($client);
socket_close($sock);
?>
