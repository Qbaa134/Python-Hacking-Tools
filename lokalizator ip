import requests

def get_location_by_ip(ip_address):
    # Użycie API ipinfo.io
    url = f"https://ipinfo.io/{ip_address}/json"
    response = requests.get(url)
    data = response.json()

    # Wyświetlenie danych
    location_info = {
        'ip': data.get('ip'),
        'hostname': data.get('hostname'),
        'city': data.get('city'),
        'region': data.get('region'),
        'country': data.get('country'),
        'location': data.get('loc'),  # Współrzędne (szerokość, długość geograficzna)
        'org': data.get('org')  # Informacje o organizacji/ISP
    }

    return location_info

def main():
    # Pobranie adresu IP od użytkownika
    ip = input("Podaj adres IP: ")

    # Sprawdzanie, czy adres jest poprawny
    if not ip:
        print("Adres IP nie może być pusty!")
        return
    
    # Pobieranie informacji o lokalizacji
    location = get_location_by_ip(ip)
    
    # Wyświetlanie wyników
    if 'error' in location:
        print(f"Nie udało się uzyskać lokalizacji dla IP {ip}")
    else:
        print(f"Lokalizacja dla IP {ip}:")
        print(f"IP: {location['ip']}")
        print(f"Hostname: {location['hostname']}")
        print(f"Miasto: {location['city']}")
        print(f"Region: {location['region']}")
        print(f"Kraj: {location['country']}")
        print(f"Współrzędne: {location['location']}")
        print(f"Organizacja/ISP: {location['org']}")

if __name__ == "__main__":
    main()
