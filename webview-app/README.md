# Emlak Arayış Mobile App - APK Oluşturma Rehberi

Bu proje, `emlakarayis.com` web sitesini bir mobil uygulama (WebView) olarak paketleyen Flutter tabanlı bir kaynak koddur.

## 🚀 5 Dakikada APK Oluşturma (Codemagic ile - En Kolay Yol)

Android Studio kurmadan, sadece bu kodları kullanarak APK alabilirsiniz:

1.  **GitHub'a Yükleyin:** Bu `webview-app` klasörünü yeni bir GitHub reposuna yükleyin.
2.  **Codemagic'e Kayıt Olun:** [codemagic.io](https://codemagic.io) adresine gidin ve GitHub ile giriş yapın.
3.  **Uygulama Ekleyin:** GitHub reponuzu seçin.
4.  **Workflow Ayarları:** 
    - Build Platform: **Android**
    - Build Format: **APK**
5.  **Build'i Başlatın:** "Start initial build" butonuna basın.
6.  **APK'yı İndirin:** Build bittiğinde size bir indirme linki verecektir.

## 💻 Geliştirici Yolu (Android Studio ile)

Eğer bilgisayarınızda Flutter kuruluysa:

1.  Klasöre gidin: `cd webview-app`
2.  Paketleri çekin: `flutter pub get`
3.  APK oluşturun: `flutter build apk --split-per-abi`
4.  Dosya şurada oluşacaktır: `build/app/outputs/flutter-apk/app-release.apk`

## 🛠️ Özelleştirmeler

- **Uygulama Adı:** `android/app/src/main/AndroidManifest.xml` dosyasındaki `android:label` kısmından değiştirebilirsiniz.
- **Paket Adı:** `com.emlakarayis.app` olarak ayarlanmıştır.
- **İzinler:** Kamera ve dosya seçme izinleri tanımlanmıştır (Emlak fotoğrafları yüklemek için gereklidir).

## 📁 Dosya Yapısı

- `lib/main.dart`: Uygulamanın ana mantığı ve site linki.
- `pubspec.yaml`: Gereken kütüphaneler (InAppWebView vb.).
- `android/`: Android platformuna özel ayarlar.
