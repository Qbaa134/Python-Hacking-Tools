import os
import sys
from scapy.all import *
from colorama import Fore, Style, init
import json
import csv
import ctypes

print(r"  ____    ____              _   _                  ")
print(r" / __ \  |___ \ _ __  _   _| |_| |__   ___  _ __   ")
print(r" | | | |   __) | '_ \| | | | __| '_ \ / _ \| '_ \  ")
print(r" | |_| |  / __/| |_) | |_| | |_| | | | (_) | | | | ")
print(r" \__-\_\ |_____| .__/ \__, |\__|_| |_|\___/|_| |_| ")
print(r"               |_|    |___/  by Qbaa134            ")

init(autoreset=True)  # Automatyczne resetowanie kolorów

# Funkcja do wyświetlania tekstu w zielonym kolorze
def green_text(text):
    return f"{Fore.GREEN}{text}{Style.RESET_ALL}"

# Funkcja do sprawdzania, czy skrypt jest uruchomiony z uprawnieniami administratora
def is_admin():
    try:
        return ctypes.windll.shell32.IsUserAnAdmin() != 0
    except:
        return False

# Funkcja do listowania dostępnych interfejsów sieciowych
def list_interfaces():
    print(green_text("Dostępne interfejsy sieciowe:"))
    try:
        interfaces = get_if_list()
        if not interfaces:
            print(green_text("Brak dostępnych interfejsów sieciowych."))
        for idx, iface in enumerate(interfaces):
            print(green_text(f"[{idx}] {iface}"))
        return interfaces
    except Exception as e:
        print(Fore.RED + f"Nie udało się pobrać interfejsów: {e}")
        return []

# Funkcja do skanowania sieci lokalnej
def scan_network(network_range):
    print(green_text(f"Rozpoczynam skanowanie sieci: {network_range}..."))
    ans, _ = arping(network_range, verbose=False)
    devices = []
    for sent, received in ans:
        devices.append({"IP": received.psrc, "MAC": received.hwsrc})
        print(green_text(f"Znaleziono urządzenie: IP={received.psrc}, MAC={received.hwsrc}"))
    return devices

# Funkcja do zapisywania wyników do pliku JSON
def save_devices_to_json(devices, filename):
    with open(filename, 'w') as file:
        json.dump(devices, file, indent=4)
    print(green_text(f"Wyniki zapisano do pliku JSON: {filename}"))

# Funkcja do przechwytywania pakietów
def sniff_packets(interface, duration, filters):
    print(green_text(f"Rozpoczynam przechwytywanie pakietów na interfejsie {interface} przez {duration} sekund..."))
    packets = sniff(iface=interface, timeout=duration, prn=lambda x: packet_callback(x, filters))
    return packets

# Funkcja do obsługi przechwyconych pakietów
def packet_callback(packet, filters):
    if packet.haslayer(IP):
        if filters.get("ip") and packet[IP].src not in filters["ip"] and packet[IP].dst not in filters["ip"]:
            return
        print(green_text(f"Pakiet: {packet.summary()}"))
        if packet.haslayer(TCP):
            print(green_text(f"Protokół: TCP, Port źródłowy: {packet[TCP].sport}, Port docelowy: {packet[TCP].dport}"))
        elif packet.haslayer(UDP):
            print(green_text(f"Protokół: UDP, Port źródłowy: {packet[UDP].sport}, Port docelowy: {packet[UDP].dport}"))
        elif packet.haslayer(ICMP):
            print(green_text(f"Protokół: ICMP"))

# Funkcja główna programu
def main():
    print(green_text("=== Wireshark-like Sniffer ==="))

    # Sprawdzenie, czy mamy uprawnienia administratora (Windows)
    if not is_admin():
        print(Fore.RED + "Skrypt wymaga uprawnień administratora. Uruchom ponownie jako administrator.")
        return

    interfaces = list_interfaces()
    if not interfaces:
        return

    interface_idx = int(input(green_text("Wybierz numer interfejsu do monitorowania: ")))
    selected_interface = interfaces[interface_idx]

    print(green_text("\n=== Opcje programu ==="))
    print(green_text("1. Przechwytywanie pakietów"))
    print(green_text("2. Skanowanie sieci lokalnej"))
    print(green_text("3. Wyjście"))
    choice = int(input(green_text("Wybierz opcję: ")))

    if choice == 1:
        duration = int(input(green_text("Podaj czas przechwytywania w sekundach: ")))
        ip_filter = input(green_text("Filtruj po adresie IP (wpisz adres lub pozostaw puste): "))
        filters = {"ip": [ip_filter] if ip_filter else []}
        packets = sniff_packets(selected_interface, duration, filters)

        save_option = input(green_text("Zapisz przechwycone pakiety do pliku pcap? (tak/nie): ")).strip().lower()
        if save_option == "tak":
            wrpcap(r"C:\Users\KUBA\Desktop\capture.pcap", packets)
            print(green_text("Pakiety zapisano do pliku capture.pcap"))

    elif choice == 2:
        network_range = input(green_text("Podaj zakres sieci do skanowania (np. 192.168.1.0/24): "))
        devices = scan_network(network_range)
        save_option = input(green_text("Zapisz wyniki do pliku JSON? (tak/nie): ")).strip().lower()
        if save_option == "tak":
            save_devices_to_json(devices, "network_scan.json")

    elif choice == 3:
        print(green_text("Wyjście z programu."))
        return
    else:
        print(Fore.RED + "Nieprawidłowy wybór!")

if __name__ == "__main__":
    main()
input("Kliknij aby zakończyć")
