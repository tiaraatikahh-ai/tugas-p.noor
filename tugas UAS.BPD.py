import random

def main():
    while True:
        angka_rahasia = random.randint(1, 100)
        max_attempts = 5
        attempts = 0

        print("\n=== Permainan Tebak Angka (1-100) ===")
        print(f"Anda punya {max_attempts} kesempatan untuk menebak.")

        while attempts < max_attempts:
            try:
                tebakan = int(input(f"Tebakan ke-{attempts+1}: "))
                attempts += 1

                if tebakan < 1 or tebakan > 100:
                    print("Harap masukkan angka antara 1 dan 100.")
                    continue

                if tebakan < angka_rahasia:
                    print("Bilangan lebih besar!")
                elif tebakan > angka_rahasia:
                    print("Bilangan lebih kecil!")
                else:
                    print(f"Jawaban Anda benar 🎉 dalam {attempts} percobaan!")
                    break
            except ValueError:
                print("Input tidak valid, harap masukkan angka.")

        else:
            # Jika loop selesai tanpa break → gagal
            print(f"Sayang sekali, kesempatan habis. Angka rahasia adalah {angka_rahasia}.")

        ulang = input("Apakah Anda ingin mengulang permainan? (y/n): ").lower()
        if ulang != 'y':
            print("Terima kasih telah bermain!")
            break

if __name__ == "__main__":
    main()