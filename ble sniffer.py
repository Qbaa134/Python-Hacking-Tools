import asyncio
from bleak import BleakScanner

# Funkcja obliczająca odległość na podstawie RSSI i współczynnika tłumienia
def calculate_distance(rssi, rssi_0=-40, n=2):
    """
    Oblicza odległość na podstawie wartości RSSI.
    
    rssi: wartość RSSI w dBm
    rssi_0: RSSI przy odległości 1 metra (domyślnie -40 dBm)
    n: współczynnik tłumienia (domyślnie 2)
    """
    distance = 10 ** ((rssi_0 - rssi) / (10 * n))
    return distance

# Funkcja skanująca urządzenia Bluetooth
async def discover_devices():
    print("Searching for Bluetooth devices...")
    devices = await BleakScanner.discover()
    return devices

def main():
    asyncio.run(run())

async def run():
    devices = await discover_devices()

    # Pytanie użytkownika o wartości RSSI i n po wykryciu urządzeń
    try:
        rssi_input = float(input("\nEnter the RSSI value you want to calculate the distance for (in dBm): "))
        n_input = float(input("Enter the attenuation coefficient (n): "))
        rssi_0_input = float(input("Enter the reference RSSI value (RSSI_0, typically -40 for 1 meter): "))
    except ValueError:
        print("Invalid input! Please enter valid numbers for RSSI and n.")
        return

    # Obliczanie odległości na podstawie wprowadzonych danych
    for device in devices:
        print(f"\nFound device: {device.name}")
        print(f"  Address: {device.address}")
        # Uzyskanie wartości RSSI
        rssi = device.rssi
        print(f"  RSSI: {rssi} dBm")

        # Obliczanie odległości
        distance = calculate_distance(rssi, rssi_0_input, n_input)
        print(f"  Estimated Distance: {distance:.2f} meters")

if __name__ == "__main__":
    main()
