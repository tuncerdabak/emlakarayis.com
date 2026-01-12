# GitHub Actions ile Otomatik FTP Dağıtımı (Deploy) Kurulumu

Bu kılavuz, kodlarınızı GitHub'a `main` branch'ine push'ladığınızda otomatik olarak sunucunuza (FTP) yüklenmesini sağlayan yapıyı kurmanıza yardımcı olur.

## 1. Hazırlık: FTP Bilgilerini Edinme
Hosting sağlayıcınızdan (cPanel, Plesk vb.) aşağıdaki bilgileri not edin:
- **FTP Sunucusu (Server):** (Örn: `ftp.emlakarayis.com` veya IP adresi)
- **FTP Kullanıcı Adı (Username):**
- **FTP Şifresi (Password):**

## 2. GitHub Secrets Tanımlama
Güvenlik nedeniyle FTP şifrenizi asla kodların içine yazmamalısınız. Bunun yerine GitHub Secrets kullanacağız.

1. GitHub deposuna gidin: [emlakarayis.com](https://github.com/tuncerdabak/emlakarayis.com)
2. Üst menüden **Settings** (Ayarlar) sekmesine tıklayın.
3. Sol menüden **Secrets and variables** > **Actions** seçeneğine tıklayın.
4. **New repository secret** butonuna tıklayarak aşağıdaki 3 secret'ı tek tek ekleyin:

| Name | Secret (Değer) | Açıklama |
| :--- | :--- | :--- |
| `FTP_SERVER` | `ftp.siteadresiniz.com` | Hosting FTP sunucu adresi |
| `FTP_USERNAME` | `kullaniciadiniz` | FTP kullanıcı adı |
| `FTP_PASSWORD` | `sifreniz` | FTP şifresi |

## 3. GitHub Action Workflow Dosyası Oluşturma
Projenizde `.github/workflows/ftp-deploy.yml` adında bir dosya oluşturun ve aşağıdaki kodu içine yapıştırın.

⚠️ **Önemli:** `server-dir` (Sunucu dizini) ayarını kendi sunucu yapınıza göre düzenleyin. Genellikle `public_html/` veya `/` olur. Yanlış dizin sitenizi bozabilir.

```yaml
name: 🚀 FTP Deploy

on:
  push:
    branches:
      - main

jobs:
  web-deploy:
    name: 🎉 Deploy
    runs-on: ubuntu-latest
    steps:
    - name: 🚚 Get latest code
      uses: actions/checkout@v4
    
    - name: 📂 Sync files
      uses: SamKirkland/FTP-Deploy-Action@v4.3.4
      with:
        server: ${{ secrets.FTP_SERVER }}
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        server-dir: ./ # Eğer FTP kullanıcısı direkt doğru klasöre login oluyorsa ./ kullanın.
        exclude: | # Yüklenmesini İSTEMEDİĞİNİZ dosya/klasörler
          **/.git*
          **/.git*/**
          **/node_modules/**
          .github/**
          task.md
          *.md
          .vscode/**
```

## 4. Test Etme
1. Bu dosyayı (`.github/workflows/ftp-deploy.yml`) oluşturup GitHub'a push'layın.
2. GitHub'da **Actions** sekmesine gidin.
3. "FTP Deploy" isimli iş akışının çalıştığını göreceksiniz.
4. Eğer yeşil tik (Success) alırsa dosyalarınız sunucuya yüklenmiş demektir.

---
**Not:** İlk yükleme biraz uzun sürebilir çünkü tüm dosyaları kontrol edecektir. Sonraki yüklemeler sadece değişen dosyaları yükleyeceği için çok daha hızlı olacaktır.
